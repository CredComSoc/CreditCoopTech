<?php

namespace CreditCommons\Exceptions;

/**
 * The workflow denies permission to the user to perform the action on the transaction.
 */
class DeprecatedWorkflowViolation extends CCViolation {

  public function __construct(
    // The Wirkflow ID
    public string $id
  ) {
    parent::__construct();
  }

  function makeMessage() : string {
    return "Workflow '$this->id' is deprecated.";
  }
}
