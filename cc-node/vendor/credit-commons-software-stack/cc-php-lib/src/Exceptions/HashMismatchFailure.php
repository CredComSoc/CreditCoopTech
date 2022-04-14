<?php

namespace CreditCommons\Exceptions;

final class HashMismatchFailure extends CCFailure {

  public function __construct(
    // The the name of the node whose hash doesn't match.
    public string $otherNode
  ) {
    parent::__construct();
  }

  function makeMessage(): string  {
    return "The ledger ratchet between $this->node and $this->remoteNode is broken.";
  }

}
