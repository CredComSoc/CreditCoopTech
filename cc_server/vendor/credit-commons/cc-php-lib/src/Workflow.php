<?php

namespace CreditCommons;
use CreditCommons\Transaction;
use CreditCommons\Entry;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\WorkflowViolation;

/**
 * Calculate what actions an account can perform on a transaction.
 *
 * @author matslats
 */
class Workflow {

  public $id;
  public $label;
  public $summary;
  public $active;
  public $direction;
  public $creation;
  public $states;

  const RELATION_PAYEE = 'payee';
  const RELATION_PAYER = 'payer';
  const RELATION_AUTHOR = 'author';

  function __construct(\stdClass $info) {
    $this->id = $info->id;
    $this->label = $info->label;
    $this->summary = $info->summary;
    $this->active = $info->active;
    $this->direction = $info->direction;
    $this->creation = $info->creation;
    $this->creation->confirm = (bool)$this->creation->confirm; // Not sure this is needed.
    // Converting these to arrays because php handles arrays better.
    foreach ($info->states as $state => $data) {
      $this->states[$state] = (array)$data;
    }
  }

  /**
   * Get all the operations the given user can do on the current transaction.
   *
   * @param string $acc_id
   * @param Transaction $transaction
   * @param bool $admin
   *   TRUE if the $acc_id is an admin
   * @return array
   */
  function getTransitions($acc_id, Transaction $transaction, bool $admin = FALSE): array {
    $links = [];
    if ($transaction->state == TransactionInterface::STATE_VALIDATED) {
      if ($admin or $this->canTransitionToState($acc_id, $transaction->state, $transaction->entries[0], $this->creation->state)) {
        $links[$this->creation->state] = '/transaction/'.$transaction->uuid.'/'.$this->creation->state;
        //signifies a cancel operation, or delete this unconfirmed transaction.
        $links['null'] = '/transaction/'.$transaction->uuid.'/null';
      }
    }
    else {
      $relatives = $this->getMyRelations($acc_id, $transaction->entries[0]);
      if (isset($this->states[$transaction->state])) {
        $state = $this->states[$transaction->state];
        foreach ($state as $target_state => $info) {
          $permitted = $info->actors;
          if ($admin or array_intersect($relatives, $permitted)) {
            $links[$target_state] = "/transaction/$transaction->uuid/$target_state";
          }
        }
      }
    }
    return $links;
  }

  /**
   * Check that the given user is permitted to transit the given transaction to the given state.
   *
   * @param string $acc_id
   * @param Transaction $transaction
   * @param string $target_state
   * @return bool
   * @throws CCViolation
   *
   * @todo if admin can do anything is it necessary to call this function in
   * admin mode? or can it not just return true if admin = true?
   */
  function canTransitionToState(string $acc_id, string $from_state, Entry $main_entry, string $target_state = NULL) : bool {
    if ($from_state == TransactionInterface::STATE_INITIATED or $from_state == TransactionInterface::STATE_VALIDATED) {
      if ($this->direction == 'bill') {
        $permitted_relations = ['payee'];
      }
      elseif ($this->direction == 'credit') {
        $permitted_relations = ['payer'];
      }
      else {// 3rdParty
        $permitted_relations = ['author'];
      }
    }
    else {
      $available_states = $this->states[$from_state];
      if (!empty($available_states[$target_state])) {
        $permitted_relations = $available_states[$target_state]->actors;
      }
      elseif($from_state == $target_state) {
        throw new WorkflowViolation(from: $from_state, to: $target_state, type: $this->id);
      }
      else {
        // Non Existent pathway - should never happen.
        throw new DoesNotExistViolation(type: 'transition', id: "$from_state>>$target_state");
      }
    }
    $actual_relations = $this->getMyRelations($acc_id, $main_entry);
    return (bool)array_intersect($actual_relations, $permitted_relations);
  }

  /**
   * Determine the relationship(s) of the authenticated user to the current
   * transaction
   * @param $acc_id
   *   The name of the account, user or wallet, as it appears in the ledger
   * @param Entry $entry
   *   The main entry of a transaction
   * @return array
   *   A list of account names.
   */
  function getMyRelations($acc_id, Entry $entry) : array {
    $relations = [];
    if($acc_id == $entry->payee->id) {// taken from the first entry.
      $relations[] = Workflow::RELATION_PAYEE;
    }
    elseif ($acc_id == $entry->payer->id) {
      $relations[] = Workflow::RELATION_PAYER;
    }
    if (isset($entry->author) and $acc_id == $entry->author) {// @todo this might be ambiguous.
      $relations[] = Workflow::RELATION_AUTHOR;
    }
    // Todo test for blog relations on the other entries.
    return $relations;
  }

  /**
   * Get the labels of the transitions from the given state to the given states
   * @param string $current_state
   * @param array $allowed_states
   *   $paths, keyed by state_name
   * @return type
   */
  function actionLabels($current_state, array $allowed_states) : array {
    $labels = [];
    if ($current_state == TransactionInterface::STATE_VALIDATED) {
      $labels[$this->creation->state] = $this->creation->label;
      $labels['null'] = 'Cancel';
    }
    else {
      foreach ($allowed_states as $state => $path) {
        if (empty($this->states[$current_state][$state])) {
          // This should never happen.
          trigger_error("workflow $this->id doesn't allow transition from $current_state to $state");
          continue;
        }
        $labels[$state] = $this->states[$current_state][$state]->label;
      }
    }
    return $labels;
  }


  /**
   * Generate a thumbprint excluding labels (which may be localised).
   * @return string
   */
  function getHash() : string {
    foreach ($this->states as $state => $target_states) {
      foreach ((array)$target_states as $target_name => $target_state) {
        $hashable_states[$state][$target_name] = $target_state->actors;
      }
    }
    $creation = clone($this->creation);
    unset($creation->label);
    unset($creation->confirm); //this is also just user experience
    return md5($this->id . json_encode($creation) . json_encode($hashable_states));
  }
}
