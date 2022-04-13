<?php

namespace CCNode;

use CreditCommons\TradeStats as BaseStats;

/**
 * Build the stats as we iterate through the database results
 */
class TradeStats extends BaseStats {

  private $allPartners = [];

  // Produce an empty object.
  static function builder() : static {
    $data = (object)[
      'balance' => 0,
      'trades' => 0,
      'entries' => 0,
      'gross_in' => 0,
      'gross_out' => 0,
      'partners' => 0
    ];
    return parent::create($data);
  }

  /**
   * Populate the object trade by trade
   * @param int $diff
   * @param string $partner
   * @param bool $isPrimary
   */
  function logTrade(int $diff, string $partner, bool $isPrimary) : void {
    $this->volume += (abs($diff));
    $this->balance += $diff;
    if ($diff > 0) {
      $this->gross_in += $diff;
    }
    else {
      $this->gross_out -= $diff;
    }
    if ($isPrimary) {
      $this->trades++;
      $this->allPartners[$partner] = 1;
      // We have to update this every time, I think.
      $this->partners = count($this->allPartners);
    }
  }

}

