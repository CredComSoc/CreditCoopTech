<?php

namespace CCNode\Transaction;

use CCNode\Orientation;


/**
 * Transversal entries have different classes (and hence methods) according to
 * which ledger it is shared with.
 * @todo make a new interface for this and put some elements into cc-php-lib
 */
class EntryTransversal extends Entry {

  static function create(\stdClass $data) : static {
    global $cc_config;
    $entry = parent::create($data);
    // Calculate now in case entry is sent trunkwards.
    // NB the trunkwardQuant is stored and used for making the hash.
    if (isset($data->trunkwardQuant)) {
      // If this is coming from trunkwards, $data->trunkwardQuant is already set
      $entry->trunkwardQuant = $data->trunkwardQuant;
    }
    else {
      // No need for rounding
      $entry->trunkwardQuant = $entry->quant * $cc_config->conversionRate;
    }
    return $entry;
  }

  /**
   * Convert the entry for relaying to another node.
   *
   * @note To convert the addresses we need to work out whether it is being sent
   * trunkward or leafward. This is the main or the only reason why $transaction
   * is a property. If the entry is going back to the client the quant should be formatted.
   */
  public function jsonSerialize() : mixed {
    global $cc_user, $cc_config, $orientation;
    if ($orientation->target === Orientation::CLIENT) {
      // Response to client
      $array = parent::jsonSerialize();
    }
    else {
      unset($this->metadata->{$this->payer->id});
      unset($this->metadata->{$this->payee->id});
      // Handle according to whether the transaction is going trunkwards or leafwards
      if ($orientation->target == Orientation::TRUNKWARD) {
        $array['payee'] = $this->payee->trunkwardPath();
        $array['payer'] = $this->payer->trunkwardPath();
        $array['quant'] = $this->trunkwardQuant;
        if ($cc_config->privacy['metadata']) {
          $array['metadata'] = $this->metadata;
        }
      }
      elseif ($orientation->target == Orientation::LEAFWARD) {
        $array['payee'] = $this->payee->leafwardPath();
        $array['payer'] = $this->payer->leafwardPath();
        $array['quant'] = $this->quant; // convert if user is local
      }
      $array['description'] = $this->description;
      $array['metadata'] = $this->metadata;
    }
    return $array;
  }

}
