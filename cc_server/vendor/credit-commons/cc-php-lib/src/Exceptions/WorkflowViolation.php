<?php

namespace CreditCommons\Exceptions;

/**
 * The workflow denies permission to the user to perform the action on the transaction.
 */
class WorkflowViolation extends CCViolation {

  public function __construct(
    // The Wirkflow ID
    public string $type,
    // The current state of the transaction
    public string $from,
    // The state to which the transaction may not be moved
    public string $to
  ) {
    parent::__construct();
  }

  function makeMessage() : string {
    return "$this->user could not move the $this->type transaction from $this->from to $this->to.";
  }
}
