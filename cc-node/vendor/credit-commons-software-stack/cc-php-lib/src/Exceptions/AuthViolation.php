<?php

namespace CreditCommons\Exceptions;

/**
 * Replaces 403 status response
 */
class AuthViolation extends CCViolation {

  public function __construct(
    // The account which is not permitted
    public string $acc_id
  ) {
    parent::__construct();
  }


  function makeMessage() : string {
    return "The auth key provided did not correspond to any user $acc_id";
  }
}
