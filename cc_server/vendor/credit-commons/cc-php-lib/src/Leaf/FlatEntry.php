<?php

namespace CreditCommons\Leaf;

use CreditCommons\CreateFromValidatedStdClassTrait;

/**
 * Simple Entry object for use in client-only application.
 *
 */
class FlatEntry {
  use CreateFromValidatedStdClassTrait;

  function __construct(
    public string $payee,
    public string $payer,
    public float $quant,
    public \stdClass $metadata, // does not recognise field type: \stdClass
    public string $description = '',
  ) { }

  static function create(\stdClass $data) : static {
    static::validateFields($data);
    return new static($data->payee, $data->payer, $data->quant, $data->metadata, $data->description);
  }

}
