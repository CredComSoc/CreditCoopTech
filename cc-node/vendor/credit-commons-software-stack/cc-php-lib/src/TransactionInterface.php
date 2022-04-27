<?php

namespace CreditCommons;

/**
 * Description of TransactionInterface
 */
interface TransactionInterface {
  const STATE_INITIATED = 'init'; //internal use only
  const STATE_VALIDATED = 'validated';
  const STATE_PENDING = 'pending';
  const STATE_COMPLETED = 'completed';
  const STATE_ERASED = 'erased';
  const STATE_TIMEDOUT = 'timedout';//internal use only

  // I don't think this is needed because json_encode and json_decode handles it all.
  const urlencodeFields = ['payer', 'payee', 'involving', 'description'];

  public function getWorkflow() : Workflow;
  // Other functions
}

