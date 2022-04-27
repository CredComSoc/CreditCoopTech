<?php

namespace CreditCommons\Exceptions;

final class InvalidFieldsViolation extends CCViolation {

  public function __construct(
    // The names of the invalid fields
    public array $fields,
    public string $type
  ) {
    parent::__construct();
  }


  function makeMessage() : string {
    foreach ($this->fields as $fieldname => $val) {
      $errs[] = "$fieldname problem: $val";
    }
    return "Invalid field data for $this->type: ".implode('; ', $errs);
  }
}

