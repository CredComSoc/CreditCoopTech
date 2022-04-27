<?php

namespace CreditCommons\Exceptions;

class PhpFailure extends CCFailure {

  public function __construct(
    // The PHP error code
    public string $code,
    // The url called
    public string $url,
    // The error message
    public string $content
  ) {
    parent::__construct();
  }


  function makeMessage() : string {
    $type = $this->convertCode($this->code);
    return "$this->node $this->url failed with $type: $this->content. See php error log for more details.";
  }

  function convertCode(int $code) : string {
    switch($code) {
      case 1:
        return 'E_ERROR';
      case 2:
        return 'E_WARNING';
      case 4:
        return 'E_PARSE';
      case 16:
        return 'E_CORE_ERROR';
      case 64:
        return 'E_COMPILE_ERROR';
      case 256:
        return 'E_USER_ERROR';
      case 4096:
        return 'E_RECOVERABLE_ERROR';
      default:
        return 'unidentified error code '.$code;
    }
  }

}
