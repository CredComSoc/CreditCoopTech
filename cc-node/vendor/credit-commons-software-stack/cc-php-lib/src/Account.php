<?php

namespace CreditCommons;

use \CreditCommons\CreateFromValidatedStdClassTrait;

/**
 * This object represents an account in the ledger. It does not form part of the
 * public API but is strongly implied to be included in the API library.
 *
 * @todo should this be abstract?
 */
abstract class Account {
  use CreateFromValidatedStdClassTrait;

  function __construct(
    public string $id,
    public bool $status,
    public int $min,
    public int $max
  ) {}

  static function create(\stdClass $data) : Account {
    static::validateFields($data);
    return new static($data->id, $data->status, $data->min, $data->max, $data->url??NULL);
  }

  abstract function getRelPath() : string;

}
