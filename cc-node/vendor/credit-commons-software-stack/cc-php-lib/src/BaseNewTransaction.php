<?php

namespace CreditCommons;
use CreditCommons\CreateFromValidatedStdClassTrait;

/**
 * Receive data from the user and prepare it to send to the node via the transaction/new endpoint
 */
abstract class BaseNewTransaction {
  use CreateFromValidatedStdClassTrait;

  function __construct(
    public string $payee,
    public string $payer,
    public int $quant,
    public string $description,
    public string $type
  ){}

}

