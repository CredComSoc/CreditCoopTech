<?php

namespace CCNode\Transaction;
use CCNode\Transaction\Entry;
use CCNode\Accounts\Remote;

/**
 * Transversal entries have different classes (and hence methods) according to
 * which ledger it is shared with.
 * @todo make a new interface for this.
 */
class EntryTransversal extends Entry {

  /**
   * @var CCNode\Transaction
   */
  protected $transaction;

  public function setTransaction(TransversalTransaction $transaction) : void {
    $this->transaction = $transaction;
  }

  /**
   * Convert the entry for sending to another node.
   */
  public function jsonSerialize() : array {
    global $user;
    $flat = [
      'quant' => $this->quant,
      'description' => $this->description,
      'metadata' => (object)[]
    ];

    if ($this->includeMetaData()) {
      // Calculate metadata for relaying branchwards
      foreach (['payee', 'payer'] as $role) {
        $name = $this->{$role}->id;
        $flat[$role] = $this->{$role}->foreignId();
      }
    }
    return $flat;
  }

  // unused?
  private function isGoingBackToClient() : bool {
    return $this->transaction->responseMode and
      $user->id == $transaction->trunkwardAccount and
      !$user instanceOf Remote;
  }

  protected function includeMetaData() {
    global $user;
    if ($user == $this->transaction->trunkwardAccount and $this->transaction->responseMode == TRUE) {
      return \CCNode\getConfig('priv.metadata');
    }
    else return TRUE;
  }

}
