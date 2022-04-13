<?php

namespace CreditCommons\Exceptions;

abstract class TransactionLimitViolation extends CCViolation {

  public function __construct(
    // The account name whose limits would be exceeded
    public string $acc_id,
    // The amount of the limit which would be exceeded
    public int $limit,
    // The projected balance
    public int $projected
  ) {
    parent::__construct();
  }

}
