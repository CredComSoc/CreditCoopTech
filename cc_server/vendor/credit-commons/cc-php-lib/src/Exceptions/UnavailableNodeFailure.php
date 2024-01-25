<?php

namespace CreditCommons\Exceptions;

/**
 * Error class for if a remote node doesn't respond.
 */
class UnavailableNodeFailure extends CCFailure {

  public function __construct(
    // The $url which failed to respond
    public string $unavailable_url
  ) {
    parent::__construct($this->makeMessage());
  }

  function makeMessage() : string {
    return "No response from $this->unavailable_url";
  }
}
