<?php

namespace CreditCommons\Exceptions;

/**
 * Violation containing an arbitrary message
 */
class CCOtherViolation extends CCViolation {

  function __construct(
    public string $translated
  ) {
    parent::__construct($translated);
  }

  function makeMessage() : string {
    return $this->translated;
  }
}

