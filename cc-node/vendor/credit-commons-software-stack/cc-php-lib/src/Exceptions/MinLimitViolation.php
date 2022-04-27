<?php

namespace CreditCommons\Exceptions;

/**
 * Pass $acc_id, $limit, $projected
 */
final class MinLimitViolation extends TransactionLimitViolation {

  function makeMessage() : string {
    $this->diff = $this->limit - $this->projected;
    return "This transaction would put $this->acc_id $this->diff below the minium balance on $this->node";
  }
}

