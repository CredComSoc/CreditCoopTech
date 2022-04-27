<?php

namespace CreditCommons\Leaf;
use CreditCommons\BaseEntry;
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
    public int $quant,
    public string $author, // the author is always local and we don't need to cast it into account
    public array $metadata, // does not recognise field type: \stdClass
    public string $description = '',
  ) { }

  static function create(\stdClass $data) : static {
    static::validateFields($data);
    return new static($data->payee, $data->payer, $data->quant, $data->author, $data->metadata, $data->description);
  }
}
