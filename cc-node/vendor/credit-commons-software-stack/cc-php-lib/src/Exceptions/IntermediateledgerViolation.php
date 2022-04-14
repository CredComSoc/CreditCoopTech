<?php

namespace CreditCommons\Exceptions;

final class IntermediateledgerViolation extends CCViolation {

  function makeMessage() : string {
    return "Transversal transactions cannot be manipulated via intermediate ledgers";
  }

}
