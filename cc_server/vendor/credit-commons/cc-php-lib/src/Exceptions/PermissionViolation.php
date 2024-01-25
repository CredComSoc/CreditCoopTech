<?php

namespace CreditCommons\Exceptions;

/**
 * Replaces 403 status response
 */
class PermissionViolation extends CCViolation {

  /**
   * Without this constructor function the reflection class gets the wrong properties.
   */
  function __construct() {
    parent::__construct('placeholder');
  }


  function makeMessage() : string {
    return "Node $this->node denied access to $this->method method on $this->path/$this->endpoint for $this->acc_id";
  }
}
