<?php

namespace CreditCommons\Exceptions;

/**
 * Replaces 403 status response
 */
class PermissionViolation extends CCViolation {

  public function __construct(
    // The $account which was not permitted
    public string $acc_id,
    // The REST method
    public string $method,
    // The endpoint
    public string $endpoint
  ) {
    parent::__construct();
  }

  function makeMessage() : string {
    $name = !empty($this->account) ? $this->account : 'anon';
    return "Node $this->node denied access to $this->method $this->url for $name";
  }
}
