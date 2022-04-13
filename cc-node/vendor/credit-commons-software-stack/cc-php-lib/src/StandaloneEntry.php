<?php

namespace CreditCommons;
use CreateFromValidatedStdClassTrait;

/**
 * A single entry of a transaction in a flat format, including the header info.
 *
 * This class is for transaction display only. It has no depth and no functionality
 *
 * @todo implement transaction view modes in the dev client.
 */
class StandaloneEntry {
  use CreateFromValidatedStdClassTrait;

  function __construct(
    public string $uuid,
    public string $payer,
    public string $payee,
    public int $quant,
    public string $type,
    public string $author,
    public string $state,
    public string $created,
    public string $updated,
    public string $description,
    public int $version
  ) { }

  static function create(\stdClass $data) {
    static::validateFields($data);
    return new static($data->uuid, $data->payer, $data->payee, $data->quant, $data->type, $data->author, $data->state, $data->created, $data->updated, $data->description, $data->version);
  }

}
