<?php

namespace CreditCommons;

use \CreditCommons\CreateFromValidatedStdClassTrait;

/**
 * A set of stats summarising an account's trading activity.
 * This class would be more performant
 */
class TradeStats {
  use CreateFromValidatedStdClassTrait;

  private $allPartners = [];

  function __construct(
    public int $balance,
    public int $trades,
    public int $entries,
    public int $gross_in,
    public int $gross_out,
    public int $partners
  ) {
    $this->volume = $this->gross_in + $this->gross_out;
  }

  static function create(\stdClass $data) : static {
    static::validateFields($data);
    return new static(
      $data->balance,
      $data->trades,
      $data->entries,
      $data->gross_in,
      $data->gross_out,
      $data->partners
    );
  }


}
