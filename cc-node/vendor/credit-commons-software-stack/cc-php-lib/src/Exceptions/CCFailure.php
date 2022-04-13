<?php

namespace CreditCommons\Exceptions;

/**
 * An error system for passing errors back upstream and translating them at the end.
 * In this package, a Violation means bad data from outside (4xx), and a Failure is an internal system failure (5xx)
 */
class CCFailure extends CCError {

  function __construct(
    string $message = ''
  ) {
    parent::__construct($message, 500);
  }

}

