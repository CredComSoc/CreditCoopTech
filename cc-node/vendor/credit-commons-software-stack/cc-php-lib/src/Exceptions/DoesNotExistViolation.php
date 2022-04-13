<?php

namespace CreditCommons\Exceptions;

/**
 * Equivalent to 404 response but also shows the type!
 */
class DoesNotExistViolation extends CCViolation {

  public function __construct(
    // The type of object which does not exist
    public string $type,
    // The id of the object which does not exist
    public string $id
  ) {
    parent::__construct();
  }


  function makeMessage() : string {
    return "The $this->type does not exist: $this->id";
  }
}
