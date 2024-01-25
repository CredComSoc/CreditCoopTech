<?php

namespace CreditCommons\Exceptions;

/**
 *  CCFailure is an software failure within a node. (5xx)
 */
class CCFailure extends CCError {

  function __construct(
    public string $translated
  ) {
    parent::__construct($translated, 500);
  }

  function makeMessage() : string {
    return $this->translated;
  }

}

