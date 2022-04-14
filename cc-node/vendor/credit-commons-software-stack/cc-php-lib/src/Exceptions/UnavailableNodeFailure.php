<?php

namespace CreditCommons\Exceptions;

/**
 * Error class for if a remote node doesn't respond.
 */
final class UnavailableNodeFailure extends CCFailure {

  public function __construct(
    // The $url which failed to respond
    public string $url
  ) {
    parent::__construct();
  }

  function makeMessage() : string {
    return "No response from $this->url";
  }
}
