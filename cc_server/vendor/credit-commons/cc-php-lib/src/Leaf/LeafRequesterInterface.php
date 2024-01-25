<?php

namespace CreditCommons\Leaf;

use CreditCommons\CreditCommonsInterface;
use CreditCommons\NewTransaction;

interface LeafRequesterInterface extends CreditCommonsInterface{

  /**
   * There is currently no interface for the client Requester.
   *
   * @param NewTransaction $new_transaction
   * @return array
   *   the transaction and the meta->transitions
   */
  function submitNewTransaction(NewTransaction $new_transaction) : array;

}
