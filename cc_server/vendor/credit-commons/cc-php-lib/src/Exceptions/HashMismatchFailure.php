<?php

namespace CreditCommons\Exceptions;

/**
 * Violation for when the hashes of two ledgers don't match.
 */
final class HashMismatchFailure extends CCFailure {

  public function __construct(
    public string $remoteNode,
    // The hash on the local node.
    public string $localHash,
    public string $remoteHash,
    public string $translated = 'blah'
  ) {
    parent::__construct(translated: $this->makeMessage());
  }

  function makeMessage(): string  {
    return "The hash from node $this->remoteNode ($this->remoteHash) does not match the local hash ($this->localHash) for that account.";
  }

}
