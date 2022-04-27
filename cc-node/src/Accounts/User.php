<?php

namespace CCNode\Accounts;
use CreditCommons\Account;
use CCNode\Accounts\AccountSummaryTrait;

/**
 * Class representing a member of the ledger
 */
class User extends Account {
  use AccountSummaryTrait;

  function __construct(
    string $id,
    bool $status,
    int $min,
    int $max,
    public bool $admin
  ) {
    parent::__construct($id, $status, $min, $max);
  }

  static function create(\stdClass $data) : Account {
    $data->admin = $data->admin??FALSE;
    static::validateFields($data);
    return new static($data->id, $data->status, $data->min, $data->max, $data->admin);
  }

  function isAdmin() : bool {
    return $this->admin;
  }

  // Can this go in the base class?
  function getRelPath() : string {
    return $this->id;
  }


}

