<?php

namespace CCNode\Transaction;

use CCNode\Transaction\Entry;
use CCNode\BlogicRequester;
use CCNode\Workflows;
use CCNode\Accounts\Remote;
use CCNode\Accounts\Branch;
use CCNode\Accounts\Trunkward;
use CreditCommons\Workflow;
use CreditCommons\Exceptions\MaxLimitViolation;
use CreditCommons\Exceptions\MinLimitViolation;
use CreditCommons\Exceptions\CCOtherViolation;
use CreditCommons\Exceptions\WorkflowViolation;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\TransactionInterface;
use CreditCommons\BaseTransaction;
use function CCNode\load_account;

class Transaction extends BaseTransaction implements \JsonSerializable {
  use \CCNode\Transaction\StorageTrait;

  protected $workflow;
  /**
   * The database ID of the transaction, (for linking to the entries table)
   * @var int
   */
  protected int $txID;

  /**
   * FALSE for request, TRUE for response mode
   * @var Bool
   */
  public bool $responseMode = FALSE;

  /**
   * Create a new transaction from a few required fields defined upstream.
   * @param stdClass $data
   *   Well formatted payer, payee, description & quant and array of stdClass entries.
   * @return \static
   */
  public static function createFromUpstream(\stdClass $data) : BaseTransaction {
    $data->state = TransactionInterface::STATE_INITIATED;
    $transaction_class = Entry::upcastAccounts($data->entries);
    return $transaction_class::create($data);
    // N.B. This isn't saved yet.
  }

  static function create(\stdClass $data) : static {
    $data->version = $data->version??-1;
    $data->written = $data->written??'';
    static::validateFields($data);
    $t = new static($data->uuid, $data->written, $data->type, $data->state, $data->entries, $data->version);
    $t->txID = $data->txID??0;
    return $t;
  }


  /**
   * Call the business logic, append entries.
   * Validate the transaction in its workflow's 'creation' state
   */
  function buildValidate() : void {
    global $loadedAccounts, $config, $user;

    $workflow = $this->getWorkflow();
    if (!$workflow->active) {
      throw new DoesNotExistViolation(type: 'workflow', id: $this->type);
    }
    $desired_state = $workflow->creation->state;
    if (!$user->admin and !$workflow->canTransitionToState($user->id, $this, $desired_state)) {
      throw new WorkflowViolation(type: $this->type, from: $this->state, to: $desired_state);
    }
    // Add fees, etc by calling on the blogic service
    if ($config['blogic_service_url']) {
      $rows = (new BlogicRequester($config['blogic_service_url']))->getRows($this);
      foreach ($rows as &$row) {
        $row->payee = load_account($row->payee);
        $row->payer = load_account($row->payer);
      }
      $this->upcastEntries($rows, TRUE);
    }
    $first_entry = reset($this->entries);
    foreach ($this->sum() as $acc_id => $info) {
      // Note only in the accounts in the main entry are limit-checked.
      $account = load_account($acc_id);
      $acc_summary = $account->getAccountSummary();
      $stats = getConfig('validate_pending') ? $acc_summary->pending : $acc_summary->completed;
      $projected = $stats->balance + $info->diff;
      if ($projected > $first_entry->payee->max) {
        throw new MaxLimitViolation(limit: $first_entry->payee->max, projected: $projected);
      }
      elseif ($projected < $first_entry->payer->min) {
        throw new MinLimitViolation(limit: $first_entry->payer->min, projected: $projected);
      }
    }
    $this->state = TransactionInterface::STATE_VALIDATED;
  }


  /**
   * Insert the transaction for the first time
   */
  function insert() : int {
    // for first time transactions...
    $workflow = $this->getWorkflow();
    // The transaction is in 'validated' state.
    if ($workflow->creation->confirm) {
      $this->version = -1;
      $status_code = 200;
    }
    else {
      // Write the transaction immediately to its 'creation' state
      $this->state = $workflow->creation->state;
      $this->version = 0;
      $status_code = 201;
    }
    // this adds +1 to the version.
    $this->saveNewVersion();

    return $status_code;
  }

  /**
   *  Add an additional entry to the transaction.
   */
  function addEntry(Entry $entry) : void {
    $entry->additional = TRUE;
    $this->entries[] = $entry;
  }

  /**
   * @param string $target_state
   * @throws WorkflowViolation
   */
  function changeState(string $target_state) : int {
    global $user;
    // If the logged in account is local, then at least one of the local accounts must be local.
    // No leaf account can manipulate transactions which only bridge this ledger.
    if ($this->entries[0]->payer instanceOf Remote and $this->entries[0]->payee instanceOf Remote and !$user instanceOf Remote) {
      throw new WorkflowViolation(
        type: $this->type,
        from: $this->state,
        to: $args['dest_state'],
      );
    }
    if ($target_state == 'null' and $this->state == 'validated') {
      $this->delete();
      return 200;
    }
    if ($user->admin or $this->getWorkflow()->canTransitionToState($user->id, $this, $target_state)) {
      $this->state = $target_state;
      $this->saveNewVersion();
      return 201;
    }
    throw new WorkflowViolation(
      type: $this->type,
      from: $this->state,
      to: $target_state,
    );
  }

  /**
   * Add up all the transactions and return the differences in balances for
   * every involved user.
   *
   * @param Transaction $transaction
   * @return array
   *   The differences, keyed by account name.
   */
  private function sum() : array {
    $accounts = [];
    foreach ($this->entries as $entry) {
      $accounts[$entry->payee->id] = $entry->payee;
      $accounts[$entry->payer->id] = $entry->payer;
      $sums[$entry->payer->id][] = -$entry->quant;
      $sums[$entry->payee->id][] = $entry->quant;
    }
    foreach ($sums as $localName => $diffs) {
      $accounts[$localName]->diff = array_sum($diffs);
    }
    return $accounts;
  }

  /**
   * Export the transaction to json for transport.
   * - get the actions
   * - remove some properties.
   *
   * @return array
   */
  public function jsonSerialize() : array {
    return [
      'uuid' => $this->uuid,
      'written' => $this->written,
      'state' => $this->state,
      'type' => $this->type,
      'version' => $this->version,
      'entries' => $this->entries,
      'transitions' => $this->transitions()
    ];
  }

  private function transitions() {
    global $user;
    return $this->getWorkflow()->getTransitions($user->id, $this, $user->admin);
  }

  /**
   * Load this transaction's workflow from the local json storage.
   * @todo Sort out
   */
  protected function getWorkflow() : Workflow {
    if (!$this->workflow) {
      $this->workflow = (new Workflows())->get($this->type);
    }
    return $this->workflow;
  }


  /**
   * Make entry objects from json entries with users already upcast by Entry::upcastAccounts
   * @param stdClass[] $rows
   *   Which are Entry objects flattened by json for transport.
   * @return Entry[]
   *   The created entries
   */
  public function upcastEntries(array $rows, bool $additional = FALSE) : void {
    global $config, $user;
    foreach ($rows as $row) {
      // Could this be done earlier?
      if (!$row->quant and !$config['zero_payments']) {
        throw new CCOtherViolation("Zero transactions not allowed on this node.");
      }
      $row->isAdditional = $additional;
      $row->isPrimary = empty($this->entries);
      if (empty($row->author)) {
        $row->author = $user->id;
      }
      if($row->payee instanceOf Branch and $row->payer instanceOf Branch) {
        // both accounts are leafwards, the current node is at the apex of the route.
        $create_method = ['EntryTransversal', 'create'];
      }
      elseif ($row->payee instanceOf Trunkward or $row->payer instanceOf Trunkward) {
        // One of the accounts is trunkward, so this class does conversion of amounts.
        $create_method = ['EntryTrunkward', 'create'];
      }
      elseif ($row->payee instanceOf Branch or $row->payer instanceOf Branch) {
        // One account is local, one account is further leafwards.
        $create_method = ['EntryTransversal', 'create'];
      }
      else {
        $create_method = ['Entry', 'create'];
      }
      $create_method[0] = '\CCNode\Transaction\\'.$create_method[0];
      $row->isPrimary = empty($this->entries);
      $this->entries[] = $create_method($row, $this);
    }
  }

}
