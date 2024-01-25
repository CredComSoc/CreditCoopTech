<?php

namespace CreditCommons;
use CreditCommons\CreateFromValidatedStdClassTrait;

/**
 * A single entry of a transaction in a flat format, including the header info.
 *
 * This class is for transaction display only. It has no depth and no functionality
 */
class EntryFull {
  use CreateFromValidatedStdClassTrait;

  function __construct(
    public string $uuid,
    public string $payer,
    public string $payee,
    public string $quant, // float or formatted
    public string $type,
    public string $author,
    public string $state,
    public string $written,
    public string $description,
    public \stdClass $metadata
  ) { }

  static function create(\stdClass $data) {
    static::validateFields($data);
    return new static(
      $data->uuid,
      $data->payer,
      $data->payee,
      $data->quant,
      $data->type,
      $data->author,
      $data->state,
      $data->written,
      $data->description,
      $data->metadata
    );
  }

}
