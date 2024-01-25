<?php

namespace CreditCommons;
use CreditCommons\Account;
use CreditCommons\Exceptions\SameAccountViolation;
use CreditCommons\CreateFromValidatedStdClassTrait;

/**
 * This class pretty much mirrors what's in the database. But also translates
 * payer and payee addresses for upstream/downstream ledgers.
 */
class Entry implements \JsonSerializable {
  use CreateFromValidatedStdClassTrait;

  /**
   * Only remote entries need this
   * @var float
   */
  public float $trunkwardQuant;

  function __construct(
    public Account $payee,
    public Account $payer,
    public float $quant,
    public \stdClass $metadata, // Does not recognise field type: \stdClass
    public string $description = '',
  ) {
    if ($payee == $payer) {
      // @note If this happens with the trunkwards account then it means the
      // remote address does not exist, but cc-php-lib has no way of knowing
      // what is the trunkwards account.
      throw new SameAccountViolation($payee);
    }
    if ($quant < 0) {
      throw new InvalidFieldsViolation(type: 'entry', fields: ['quant' => $quant]);
    }
  }

  static function create(\stdClass $data) : static {
    if (!isset($data->metadata)) {
      $data->metadata = new \stdClass();
    }
    static::validateFields($data);
    return new static (
      $data->payee,
      $data->payer,
      $data->quant,
      $data->metadata,
      $data->description
    );
  }

  /**
   * For sending the transaction back to the client.
   * Ideally there would be three serializers, for sending trunkwards, leafwards
   * and to the blogic service,
   */
  public function jsonSerialize() : mixed {
    // Handle according to whether the transaction is going trunkwards or leafwards
    $array = [
      // Trunkward path is best if we don't have context.
      'payee' => $this->payee->trunkwardPath(),
      'payer' => $this->payer->trunkwardPath(),
      'quant' => $this->quant,
      'description' => $this->description,
      'metadata' => $this->metadata
    ];
    unset(
      $array['metadata']->{$this->payee->id},
      $array['metadata']->{$this->payer->id}
    );
    return $array;
  }

}
