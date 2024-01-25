<?php

namespace CreditCommons;

use CreditCommons\Workflow;

/**
 * Description of TransactionInterface
 */
interface TransactionInterface {

  const STATE_INITIATED = 'init'; //internal use only
  const STATE_VALIDATED = 'validated';
  const STATE_PENDING = 'pending';
  const STATE_COMPLETED = 'completed';
  const STATE_ERASED = 'erased';
  const STATE_TIMEDOUT = 'timedout';//internal use only [not implemented]

  const COUNTED_STATES = ['pending', 'completed'];

  /**
   * Retrieve the whole workflow object, determined by the transaction->type.
   * @return Workflow
   */
  function getWorkflow() : Workflow;

}
