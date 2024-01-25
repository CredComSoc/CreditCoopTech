<?php

namespace CreditCommons\Exceptions;

/**
 * Unable to parse the given path; it cannot be matched to a local account and
 * there's no trunkward account to pass it to.
 */
class PathViolation extends CCViolation {

  public function __construct(
    public string $relPath,
    public string $context,
  ) {
    parent::__construct($this->makeMessage());
  }

  function makeMessage() : string {
    return "Unable to parse '$this->relPath' in context of $this->context";
  }
}
