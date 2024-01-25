<?php

namespace CreditCommons\Exceptions;

/**
 * Violation means bad data from outside (4xx).
 */
class CCViolation extends CCError {

  function __construct($message = NULL) {
    parent::__construct($message, 400);
  }

  function makeMessage() : string{
    return $this->message;
  }
}

