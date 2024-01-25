<?php

namespace CreditCommons;
use CreditCommons\CreateFromValidatedStdClassTrait;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\DeprecatedWorkflowViolation;

/**
 * Receive and validate new transaction data coming from the leaf/client.
 */
class NewTransaction {
  use CreateFromValidatedStdClassTrait;

  function __construct(
    public string $uuid,
    public string $payee,
    public string $payer,
    public int    $quant,
    public string $description,
    public string $type,
    public \stdClass $metadata,
  ){}

  /**
   * @param \stdClass $data
   */
  static function create(\stdClass $data, array $workflows, $current_user_id) {
    // Set defaults.
    $data->metadata = $data->metadata ?? new \stdClass();

    if (!isset($workflows[$data->type])) {
      throw new DoesNotExistViolation(type: 'workflow', id: $data->type);
    }
    elseif (!$workflows[$data->type]->active) {
      throw new DeprecatedWorkflowViolation(id: $data->type);
    }
    $direction = $workflows[$data->type]->direction;
    if ($direction == 'credit') {
      $data->payer = $current_user_id;
    }
    elseif ($direction == 'bill') {
      $data->payee = $current_user_id;
    }
    // otherwise it's a 3rd party transaction with payer and payee given.
    $data->uuid = $data->uuid ?? sprintf(
      '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0x0fff) | 0x4000,
      mt_rand(0, 0x3fff) | 0x8000,
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff)
    );

    static::validateFields($data);

    return new static(
      uuid: $data->uuid,
      payee: $data->payee,
      payer: $data->payer,
      quant: $data->quant,
      type: $data->type,
      description: $data->description,
      metadata: $data->metadata ?? new stdClass()
    );
  }

}

