<?php

namespace CreditCommons;
use CreditCommons\Entry;

/**
 * Transversal entries have different classes (and hence methods) according to
 * which ledger it is shared with.
 */

class TransversalEntry extends Entry {

  /**
   *
   * Entries being shared with other nodes need:
   * - Accounts collapsed to their transversal path.
   * - Quant converted to go trunkwards
   *
   * @return stdClass
   */
  public function jsonSerialize() : array {
    global $orientation, $config;
    if ($orientation->adjacentAccount() == 'client') {
      return parent::jsonSerialize();
    }
    $flat = [
      'payee' => $this->payee->transversalPath,
      'payer' => $this->payer->transversalPath,
      'quant' => $this->quant,
      'description' => $this->description,
      'type' => $this->type
    ];
    if ($config['bot']['share_metadata']) {
      $flat['metadata'] = $this->metadata;
    }
    return $flat;
  }

}

/**
 * Entry which is shared with the trunkwards ledger only.
 */
class TrunkwardsEntry extends TransversalEntry {

  /**
   * array_map callback. Convert the quant of the entry coming from trunk.
   */
  function fromTrunkNode() : self {
    global $config;
    $clone = clone($this);
    if ($config['bot']['rate'] <> 1) {
      $clone->quant /= $config['bot']['rate'];
    }
    return $clone;
  }

  /**
   * array_map callback. Convert the quant of the entry going towards trunk
   */
  function toTrunkNode() : self {
    global $config;
    $clone = clone($this);
    if ($config['bot']['rate'] <> 1) {
      $clone->quant *= $config['bot']['rate'];
    }
    return $clone;
  }

}
