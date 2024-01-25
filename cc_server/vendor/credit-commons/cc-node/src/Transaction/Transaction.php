<?php

namespace CCNode\Transaction;

use CCNode\Transaction\EntryTransversal;
use CCNode\Orientation;
use CCNode\BlogicRequester;
use CCNode\Accounts\Remote;
use CCNode\Accounts\Trunkward;
use CreditCommons\Workflow;
use CreditCommons\NewTransaction;
use CreditCommons\TransactionInterface;
use CreditCommons\Exceptions\MaxLimitViolation;
use CreditCommons\Exceptions\MinLimitViolation;
use CreditCommons\Exceptions\WorkflowViolation;
use CreditCommons\Exceptions\DoesNotExistViolation;


class Transaction extends \CreditCommons\Transaction implements \JsonSerializable {

  const DECIMAL_PLACES = 6;
  const REGEX_DATE = '/[0-9]{4}-[0|1]?[0-9]-[0-3][0-9]/';
  const REGEX_TIME = '/[0-2][0-9]:[0-5][0-9]:[0-5][0-9]/';
  use \CCNode\Transaction\StorageTrait;

  /**
   * The full workflow object.
   * @var Workflow
   */
  protected Workflow $workflow;

  /**
   * The database ID of the transaction, (for linking to the entries table)
   * @var int
   */
  protected int $txID = 0;

  /**
   * FALSE for request, TRUE for response mode
   * @var Bool
   */
  public bool $responseMode = FALSE;

  /**
   * Name of the user who wrote the latest version
   * @var string
   */
  public string $scribe;

  /**
   * Create a new transaction from a few required fields defined upstream.
   * @param NewTransaction $new
   * @return TransactionInterface
   */
  public static function createFromNew(NewTransaction $new) : TransactionInterface {
    $data = new \stdClass;
    $data->uuid = $new->uuid;
    $data->type = $new->type;
    $data->state = TransactionInterface::STATE_INITIATED;
    $data->version = -1; // Corresponds to state init, pre-validated.
    $data->entries = [(object)[
      'payee' => $new->payee,
      'payer' => $new->payer,
      'description' => $new->description,
      'metadata' => $new->metadata,
      'quant' => $new->quant,
      'isPrimary' => TRUE
    ]];
    $transaction = Transaction::create($data);
    return $transaction;
  }

  public static function createFromUpstream(\stdClass $data) : TransactionInterface {
    global $cc_user, $cc_config;
    if ($cc_user->id == $cc_config->trunkwardAcc){
      Trunkward::convertIncomingEntries($data->entries, $cc_user->id, $cc_config->conversionRate);
    }
    $data->state = TransactionInterface::STATE_INITIATED;
    return static::create($data);
  }


  static function create(\stdClass $data) : static {
    global $cc_user, $orientation;
    if (isset($orientation))unset($orientation);
    static::upcastEntries($data->entries);
    $data->version = $data->version??-1;
    $data->written = $data->written??'';
    static::validateFields($data);
    $transaction_class = '\CCNode\Transaction\Transaction';
    foreach ($data->entries as $e) {
      if ($e instanceOf EntryTransversal) {
        $transaction_class = '\CCNode\Transaction\TransversalTransaction';
        if (!isset($orientation)) {
          $orientation = Orientation::CreateTransversal($data->entries[0]->payee, $data->entries[0]->payer);
        }
        break;
      }
    }
    if (!isset($orientation)) {
      $orientation = Orientation::CreateLocal($data->entries[0]->payee, $data->entries[0]->payer);
    }

    $transaction = new $transaction_class($data->uuid, $data->type, $data->state, $data->entries, $data->written, $data->version);
    if (isset($data->txID)) {
      $transaction->txID = $data->txID;
    }
    $transaction->scribe = $data->scribe??$cc_user->id;
    return $transaction;
  }

  /**
   * Call the business logic, append entries.
   * Validate the transaction in its workflow's 'creation' state
   *
   * @return Entry[]
   *   Any new rows added by the business logic.
   */
  function buildValidate() : array {
    global $cc_config, $cc_user;
    $workflow = $this->getWorkflow();
    if (!$workflow->active) {
      // not allowed to make new transactions with non-active workflows
      throw new DoesNotExistViolation(type: 'workflow', id: $this->type);
    }
    $desired_state = $workflow->creation->state;
    if (!$cc_user->admin and !$workflow->canTransitionToState($cc_user->id, $this->state, $this->entries[0], $desired_state)) {
      $v = new WorkflowViolation(type: $this->type, from: $this->state, to: $desired_state);
      throw $v;
    }
    // Add fees, etc by calling on the blogic module, either internally or via REST API
    // @todo make the same function name for both.
    $rows = $cc_config->blogicMod ? $this->callBlogic($cc_config->blogicMod) : [];
    $this->checkLimits();
    return $rows;
  }

  /**
   * Check the transaction doesn't transgress balance limits
   *
   * @throws MaxLimitViolation
   * @throws MinLimitViolation
   */
  protected function checkLimits() {
    global $cc_config;
    $payee = $this->entries[0]->payee;
    if ($payee->max or $payee->min) {
      $acc_summary = $payee->getSummary(TRUE);
      $stats = $cc_config->validatePending ? $acc_summary->pending : $acc_summary->completed;
      $payee_diff = $this->sum($payee->id);
      $projected = $stats->balance + $payee_diff;
      if ($payee_diff > 0 and $projected > $payee->max) {
        throw new MaxLimitViolation(limit: $payee->max, projected: $projected, accId: $payee->id);
      }
    }
    $payer = $this->entries[0]->payer;
    if ($payer->max or $payer->min) {
      $acc_summary = $payer->getSummary(TRUE);
      $stats = $cc_config->validatePending ? $acc_summary->pending : $acc_summary->completed;
      $payer_diff = $this->sum($payer->id);
      $projected = $stats->balance + $payer_diff;
      if ($payer_diff < 0 and $projected < $payer->min) {
        throw new MinLimitViolation(limit: $payer->max, projected: $projected, accId: $payer->id);
      }
    }
    $this->state = TransactionInterface::STATE_VALIDATED;
  }

  /**
   * Call whatever Blogic class and upcast and append any resulting rows.
   */
  protected function callBlogic(string $bLogicMod) : array {
    // this is rather cumbersome because blogic wants the first entry with
    // flattened payee and payee and the transaction->type.
    $first = $this->entries[0];
    $blogic_entry = [
      'payee' => (string)$first->payee,
      'payer' => (string)$first->payer,
      'quant' => $first->quant,
      'description' => $first->description,
      'metadata' => $first->metadata,
      'type' => $this->type
    ];
    if (class_exists($bLogicMod)) {
      $blogic_service = new $bLogicMod();
      $rows = $blogic_service->addRows(...$blogic_entry);
    }
    else {// It must be a url
      // The Blogic service will know itself which account to put fees in.
      $rows = (new BlogicRequester($bLogicMod))->addRows(...$blogic_entry);
    }
    // The Blogic returns entries with upcast account objects
    static::upcastEntries($rows, TRUE);

    $this->entries = array_merge($this->entries, $rows);
    return $rows;
  }

  /**
   * Insert the transaction for the first time
   * @return bool
   *   FALSE if the transaction is in a temporary state
   */
  function insert() : bool {
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
   * @param string $target_state
   * @return bool
   *   TRUE if a new version of the transaction was saved. FALSE if the transaction was deleted (transactions in validated state only)
   * @throws WorkflowViolation
   */
  function changeState(string $target_state) : bool {
    global $cc_user;
    // If the logged in account is local, then at least one of the local accounts must be local.
    // No leaf account can manipulate transactions which only bridge this ledger.
    if ($this->entries[0]->payer instanceOf Remote and $this->entries[0]->payee instanceOf Remote and !$cc_user instanceOf Remote) {
      throw new WorkflowViolation(
        type: $this->type,
        from: $this->state,
        to: $args['dest_state'],
      );
    }
    if ($target_state == 'null' and $this->state == 'validated') {
      $this->delete();
      return FALSE;
    }

    if ($cc_user->admin or $this->getWorkflow()->canTransitionToState($cc_user->id, $this->state, $this->entries[0], $target_state)) {
      $this->state = $target_state;
      $this->saveNewVersion();
      return TRUE;
    }
    throw new WorkflowViolation(
      type: $this->type,
      from: $this->state,
      to: $target_state,
    );
  }

  /**
   * Calculate the total difference this transaction makes to an account balance.
   *
   * @param string $acc_id
   *   The account whose diff is needed.
   *
   * @return int
   *   The total difference caused by this transaction to the account balance
   */
  private function sum(string $acc_id) : int {
    $diff = 0;
    foreach ($this->entries as $entry) {
      if ($acc_id == $entry->payee->id) {
        $diff += $entry->quant;
      }
      elseif ($acc_id == $entry->payer->id) {
        $diff -= $entry->quant;
      }
    }
    return $diff;
  }

  /**
   * {@inheritDoc}
   */
  public function transitions() : array {
    global $cc_user;
    return $this->getWorkflow()->getTransitions($cc_user->id, $this, $cc_user->admin);
  }

  /**
   * Load this transaction's workflow from the local json storage.
   * @todo Sort out
   */
  public function getWorkflow() : Workflow {
    global $cc_workflows;
    if (isset($cc_workflows[$this->type])) {
      return $cc_workflows[$this->type];
    }
    throw new DoesNotExistViolation(type: 'workflow', id: $this->type);
  }


  /**
   * Make entry objects from json entries with users already upcast.
   * - upcast the accounts
   * - add 'author' and isPrimary fields
   * - determine which class each entry is.
   *
   * @param stdClass[] $rows
   *   Entry objects received as json.
   * @param bool $is_additional
   *   TRUE if these transactions were created (as fees etc.) by the current
   *   node or downstream, and hence should be passed back upstream
   * @return bool
   *   TRUE if any of the Entries is transversal.
   */
  protected static function upcastEntries(array &$rows, bool $is_additional = FALSE) : bool {
    global $cc_user;
    $transversal_transaction = FALSE;
    foreach ($rows as &$row) {
      $transversal_row = \CCNode\upcastAccounts($row);
      // sometimes this is called with entries from the db including isPrimary
      // other times it is called with additional entries from json
      if (!isset($row->isPrimary)) {
        $row->isPrimary = !$is_additional;
      }
      if (empty($row->author)) {
        $row->author = $cc_user->id;
      }
      $entry_class = 'CCNode\Transaction\Entry';
      if ($transversal_row) {
        $entry_class .= 'Transversal';
        $transversal_transaction = TRUE;
      }
      // Transversal entries require the transaction as a parameter.
      $row = [$entry_class, 'create']($row);
    }
    return $transversal_transaction;
  }

  public function jsonSerialize(): mixed {
    return $this->jsonDisplayable();
  }

}
