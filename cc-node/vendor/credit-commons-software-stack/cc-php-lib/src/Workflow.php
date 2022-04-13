<?php

namespace CreditCommons;
use CreditCommons\BaseTransaction;
use CreditCommons\BaseEntry;
use CreditCommons\Exceptions\CCViolation;
use CreditCommons\Exceptions\DoesNotExistViolation;

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
  public $states;
  public $creation;

  const RELATION_PAYEE = 'payee';
  const RELATION_PAYER = 'payer';
  const RELATION_AUTHOR = 'author';
  const RELATION_ADMIN = '<admin>'; // not sure about this one.

  function __construct(\stdClass $info) {
    $this->id = $info->id;
    $this->label = $info->label;
    $this->summary = $info->summary;
    $this->active = $info->active;
    // Converting these to arrays because php handles arrays better.
    foreach ($info->states as $state => $data) {
      $this->states[$state] = (array)$data;
    }
    $this->creation = $info->creation;
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
  function getTransitions($acc_id, BaseTransaction $transaction, bool $admin = FALSE): array {
    $dest_states = [];
    if ($transaction->state == TransactionInterface::STATE_VALIDATED) {
      if ($this->canTransitionToState($acc_id, $transaction, $this->creation->state, $admin)) {
        $dest_states[] = $this->creation->state;
      }
    }
    else {
      $relatives = $this->getMyRelations($acc_id, $transaction->entries[0], $admin);
      if (isset($this->states[$transaction->state])) {
        $state = $this->states[$transaction->state];
        foreach ($state as $target_state => $info) {
          $permitted = $info->signatories;
          $permitted[] = SELF::RELATION_ADMIN;
          if (array_intersect($relatives, $permitted)) {
            $dest_states[] = $target_state;
          }
        }
      }
    }
    return $dest_states;
  }

  /**
   * Check that the given user is permitted to transit the given transaction to the given state.
   *
   * @param string $acc_id
   * @param Transaction $transaction
   * @param string $target_state
   * @param bool $admin
   *   if TRUE, all available transitions in the workflow are permitted.
   * @return bool
   * @throws CCViolation
   *
   * @todo if admin can do anything is it necessary to call this function in
   * admin mode? or can it not just return true if admin = true?
   */
  function canTransitionToState(string $acc_id, BaseTransaction $transaction, string $target_state = NULL, bool $admin = FALSE) : bool {
    if ($transaction->state == TransactionInterface::STATE_VALIDATED) {
      $permitted_relations = $this->creation->by;
    }
    elseif ($transaction->state == TransactionInterface::STATE_INITIATED) {
      if (!$this->active) {
        // A disabled workflow cannot be used to create a new transaction;
        throw new DoesNotExistViolation(type: 'workflow', id: $this->id);
      }
      $permitted_relations = $this->creation->by;
    }
    else {
      $available_states = $this->states[$transaction->state];
      if (!empty($available_states[$target_state])) {
        $permitted_relations = $available_states[$target_state]->signatories;
      }
      elseif($transaction->state == $target_state) {
        throw new CCViolation("Transaction is already $target_state");
      }
      else {
        // Non Existent pathway - should never happen.
        throw new CCViolation('No workflow path from state '.$transaction->state .' to '.$target_state);
      }
    }
    $permitted_relations[] = SELF::RELATION_ADMIN;
    $actual_relations = $this->getMyRelations($acc_id, $transaction->entries[0], $admin);
    return (bool)array_intersect($actual_relations, $permitted_relations);
  }

  /**
   * Determine the relationship(s) of the authenticated user to the current
   * transaction
   * @param $acc_id
   *   The name of the account, user or wallet, as it appears in the ledger
   * @param Entry $entry
   *   The main entry of a transaction
   * @param bool $admin
   *   TRUE to add the 'admin' relation
   * @return array
   *   A list of account names.
   *
   * @todo find out if the current user (or $acc_id) is an admin without depending on anything in CCNode
   */
  function getMyRelations($acc_id, BaseEntry $entry, bool $admin = FALSE) : array {
    $relations = [];
    if($admin) {
      $relations[] = Workflow::RELATION_ADMIN;
    }
    if($acc_id == $entry->payee->id) {// taken from the first entry.
      $relations[] = Workflow::RELATION_PAYEE;
    }
    elseif ($acc_id == $entry->payer->id) {
      $relations[] = Workflow::RELATION_PAYER;
    }
    if ($acc_id == $entry->author) {// todo this might be ambiguous.
      $relations[] = Workflow::RELATION_AUTHOR;
    }
    // Todo test for blog relations on the other entries.
    return $relations;
  }

  /**
   * Get the labels of the transitions from the given state to the given states
   * @param string $current_state
   * @param array $allowed_states
   * @return type
   */
  function actionLabels($current_state, array $allowed_states) : array {
    $labels = [];
    if ($current_state == TransactionInterface::STATE_VALIDATED) {
      $labels[$this->creation->state] = $this->creation->label;
    }
    else {
      foreach ($allowed_states as $state) {
        if (empty($this->states[$current_state][$state])) {
          // This should never happen.
          trigger_error("workflow '$this->id' doesn't allow transition from $current_state to $state");
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
        $hashable_states[$state][$target_name] = $target_state->signatories;
      }
    }
    $creation = clone($this->creation);
    unset($creation->label);
    unset($creation->confirm); //this is also just user experience
    return md5($this->id . json_encode($creation) . json_encode($hashable_states));
  }
}
