<?php

namespace CreditCommons;

use CreditCommons\TransactionDisplay;
use CreditCommons\TransactionInterface;

/**
 * A transaction consists of several ledger entries sharing a common UUID, type and workflow state.
 * There is a primary entry, and many dependents.
 * Entries can be either transversal or local.
 * The transaction is either transversal or local, depending on the primary entry.
 * That means a local transaction cannot have transversal dependents.
 */
class Transaction extends TransactionDisplay implements TransactionInterface {

  /**
   * Retrieve the whole workflow object, determined by the transaction->type.
   * @return Workflow
   */
  function getWorkflow() : Workflow {
    // Can't implement this in cc-php-lib.
  }
}
