<?php

namespace CCNode;

use CCNode\Entry;
use CCNode\BlogicRequester;
use CCNode\Workflows;
use CCNode\TransactionStorageTrait;
use CreditCommons\Workflow;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\MaxLimitViolation;
use CreditCommons\Exceptions\MinLimitViolation;
use CreditCommons\Exceptions\CCViolation;
use CreditCommons\Exceptions\WorkflowViolation;
use CreditCommons\TransactionInterface;
use CreditCommons\BaseTransaction;
use CreditCommons\Account;
use CreditCommons\TransversalEntry;

class Transaction extends BaseTransaction implements \JsonSerializable {
  use TransactionStorageTrait;

  /**
   * Create a new transaction from a few required fields defined upstream.
   * @param stdClass $data
   *   well formatted payer, payee, description & quant and array of stdClass entries.
   * @return \static
   */
  public static function createFromUpstream(\stdClass $data) : BaseTransaction {
    global $user;
    $data->author = $user->id;
    $data->state = TransactionInterface::STATE_INITIATED;
    $data->entries[0]->primary = 1;
    $data->entries = static::createEntries($data->entries, $user);
    $class = static::determineTransactionClass($data->entries);
    return $class::create($data);
  }

  /**
   * @param array $entries
   * @return boolean
   *   TRUE if these entries imply a TransversalTransaction
   */
  protected static function determineTransactionClass(array $entries) : string {
    foreach ($entries as $entry) {
      if ($entry instanceOf TransversalEntry) {
        return 'CCNode\TransversalTransaction';
      }
    }
    return 'CCNode\Transaction';
  }


  /**
   * Call the business logic, append entries.
   * Validate the transaction in its workflow's 'creation' state
   */
  function buildValidate() : void {
    global $loadedAccounts, $config, $user;

    $workflow = $this->getWorkflow();
    $desired_state = $workflow->creation->state;
    if (!$workflow->canTransitionToState($user->id, $this, $desired_state, $user->admin)) {
      throw new WorkflowViolation(
        acc_id: $user->id,
        type: $this->type,
        from: $this->state,
        to: $desired_state,
      );
    }

    $first_entry = reset($this->entries);
    // Add fees, etc by calling on the blogic service
    if ($config['blogic_service_url']) {
      if ($fees = (new BlogicRequester($config['blogic_service_url']))->appendTo($this)) {
        $this->entries = array_merge($this->entries, $fees);
      }
    }
    foreach ($this->sum() as $acc_id => $info) {
      $account = load_account($acc_id);
      $ledgerAccountInfo = $account->getAccountSummary();
      $projected = $ledgerAccountInfo->pending->balance + $info->diff;
      if ($projected > $first_entry->payee->max) {
        throw new MaxLimitViolation(acc_id: $acc_id, limit: $first_entry->payee->max, projected: $projected);
      }
      elseif ($projected < $first_entry->payer->min) {
        throw new MinLimitViolation(acc_id: $acc_id, limit: $first_entry->payer->min, projected: $projected);
      }
    }
    $this->state = TransactionInterface::STATE_VALIDATED;
  }

  /**
   * @param string $target_state
   * @throws \Exception
   */
  function changeState(string $target_state) {
    $this->sign($target_state);
  }

  /**
   *
   * @global Account $user
   * @param string $target_state
   * @return $this
   * @throws WorkflowViolation
   */
  function sign(string $target_state) {
    global $user;
    if (!$this->getWorkflow()->canTransitionToState($user->id, $this, $target_state, $user->admin)) {
      throw new WorkflowViolation(
        acc_id: $user->id,
        type: $this->type,
        from: $this->state,
        to: $target_state,
      );
    }

    $this->state = $target_state;
    $this->saveNewVersion();
    return $this;
  }

  /**
   * Add up all the transactions and return the differences in balances for
   * every involved user.
   *
   * @param Transaction $transaction
   * @return array
   *   The differences, keyed by account name.
   */
  public function sum() : array {
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
   *
   * @todo make transitions a property or function of the transaction object.
   */
  public function jsonSerialize() : array {
    global $user;
    return [
      'uuid' => $this->uuid,
      'updated' => $this->updated,
      'state' => $this->state,
      'type' => $this->type,
      'version' => $this->version,
      'entries' => $this->entries,
      'transitions' => $this->getWorkflow()->getTransitions($user->id, $this, $user->admin)
    ];
  }

  /**
   * Load this transaction's workflow from the local json storage.
   * @todo Sort out
   */
  public function getWorkflow() : Workflow {
    if ($w = (new Workflows())->get($this->type)) {
      return $w;
    }
    throw new DoesNotExistViolation(type: 'workflow', id: $this->type);
  }

  /**
   *
   * @param stdClass[] $rows
   *   Which are Entry objects flattened by json for transport.
   * @param string $author
   * @return Entry[]
   *   The created entries
   */
  protected static function createEntries(array $rows, Account $author = NULL) : array {
    global $config;
    $entries = [];
    foreach ($rows as $row) {
      if (!$row->quant and !$config['zero_payments']) {
        throw new CCViolation(message: "Zero transactions not allowed on this node.");
      }
      if ($author){
        $row->author = $author->id;
      }
      $row->payer = load_account($row->payer);
      $row->payee = load_account($row->payee);
      $entries[] = Entry::create($row);
    }
    return $entries;
  }

}
