<?php

namespace CreditCommons\Exceptions;

abstract class TransactionLimitViolation extends CCViolation {

  public function __construct(
    // The amount of the limit which would be exceeded
    public int $limit,
    // The projected balance
    public int $projected,
    public string $accId
  ) {
    parent::__construct();
  }

}
