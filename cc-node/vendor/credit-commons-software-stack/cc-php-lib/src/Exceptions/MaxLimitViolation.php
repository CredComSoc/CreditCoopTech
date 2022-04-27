<?php

namespace CreditCommons\Exceptions;

/**
 * Pass $acc_id, $limit, $projected
 */
final class MaxLimitViolation extends TransactionLimitViolation {

  function makeMessage() : string {
    $this->diff = $this->limit - $this->projected;
    return "This transaction would put $this->acc_id $this->diff above the maxium balance on $this->node";
  }
}
