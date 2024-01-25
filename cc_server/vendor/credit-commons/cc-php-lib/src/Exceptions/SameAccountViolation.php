<?php

namespace CreditCommons\Exceptions;

/**
 * Violation for when an account should not be in a transaction.
 */
class SameAccountViolation extends CCViolation {

  public function __construct(
    public string $wrongId,
  ) {
    parent::__construct();
  }

  function makeMessage() : string {
    return 'Entries must have a different payer and payee: '.$this->wrongId;
  }
}
