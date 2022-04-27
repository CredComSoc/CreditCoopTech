<?php

namespace CreditCommons\Exceptions;

/**
 * The transaction 'type' does not correspond to a known workflow ID.
 */
class UnknownWorkflowViolation extends CCViolation {

  public function __construct(
    // The unknown workflow ID
    public string $workflow_id
  ) {
    parent::__construct();
  }

  function makeMessage() : string {
    return "This node is not aware of a '$this->workflow_id' workflow.";
  }
}
