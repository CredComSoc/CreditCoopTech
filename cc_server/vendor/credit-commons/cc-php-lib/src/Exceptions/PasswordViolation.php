<?php

namespace CreditCommons\Exceptions;

/**
 * For when a valid username but wrong auth key is supplied.
 *
 * @todo Maybe merge with hashMismatchViolation.
 */
class PasswordViolation extends CCViolation {

  function __construct(
    public string $key
  ) {
    parent::__construct("Wrong password: $key");
  }

  /**
   * {@inheritDoc}
   */
  function makeMessage() : string {
    return "'$this->acc_id' is not authorised by '$this->key'" ;
  }
}
