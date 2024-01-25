<?php

namespace CreditCommons\Exceptions;

use CreditCommons\Exceptions\CCFailure;

/**
 *
 * An throwable exception with public properties which can be sent via json and
 * reconstructed upstream.
 */
abstract class CCError Extends \Error implements \JsonSerializable {

  // These properties may be populated in __construct() and overridden in convertException().
  public string $node;
  public string $class;
  public string $method;
  public string $path;
  public string $user;
  public array $trace;

  function __construct($message, $code) {
    global $error_context;
    parent::__construct(NULL, $code);
    $class_parts = explode('\\', get_called_class());
    $this->class = array_pop($class_parts);
    // @todo find a way to ensure this global is documented or forced to be set in cc-php-lib
    if (!$error_context) {
      die('$error_context global object has not been created.');
    }
    // Not needed by the client, which only reconstructs errors.
    // See NodeRequester::reconstructCCErr()
    $this->node = $error_context->node;
    $this->method = $error_context->method;
    $this->path = $error_context->path;
    $this->user = $error_context->user;
    $this->trace = $this->getTrace();
    $this->message = $message ?? $this->makeMessage();
  }

  /**
   * This is instead of the final method getMessage() which is required for any \throwable
   * @return string
   */
  abstract function makeMessage() : string;

  /**
   * Before reconstructing the error make sure all the fields are present and
   * cast to the right type
   *
   * @note this is v similar to CreateFromValidatedStdClassTraitvalidateFields
   * except it takes an array and assumes no fields are missing.
   * @note this is called in CreditCommons\nodeRequester\reconstructCCException
   */
  static function validateCastParams(array &$fields) : void {
    $errs = [];
    foreach (self::getReflection()->getConstructor()->getParameters() as $param) {
      /** @var ReflectionParameter $param */
      $name = $param->getName();
      $type = $param->getType();
      $val = $fields[$name];
      // cast all the values
      switch($type) {
        case 'string':
          if ((string)$val == $val) {
            $fields[$name] = (string)$val;
            continue 2;
          }
          break;
        case 'int':
          if (is_numeric($val) and (int)$val == $val) {
            $fields[$name] = (int)$val;
            continue 2;
          }
          break;
        case 'bool':
          if ((bool)($val) == $val) {
            $fields[$name] = (bool)$val;
            continue 2;
          }
          break;
        case 'array':
          if (is_iterable($val) or $val instanceOf \stdClass) {
            if (!is_array($val)) {
              $fields[$name] = (array)$val;
            }
            continue 2;
          }
        case 'CreditCommons\\Account':
          if ($val instanceOf \CreditCommons\Account) {
            continue 2;
          }
        default:
          throw new CCFailure("CCError does not recognise field type: $type");
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  function jsonSerialize() :array {
    $array = [];
    foreach (self::getReflection()->getProperties() as $param) {
      if (substr($param->class, 0, 24) == 'CreditCommons\Exceptions') {
        $name = $param->getName();
        // Do we need to handle better what happens if $this->node is not set?
        if (!isset($this->{$name}))continue;
        $array[$name] = $this->{$name};
      }
    }
    return $array;
  }

  /**
   * @param \Error $exception
   * @param string $method
   * @param string $path_inc_query_string
   * @param string $acc_user_id
   * @return static
   */
  public static function convertException(\Throwable $exception) : CCError {
    if ($exception instanceOf self) {// New or received from downstream.
      $cc_error = $exception;
    }
    else {// An error from elsewhere, output as a CCFailure.
      $cc_error = new CCFailure($exception->getMessage());
      $cc_error->trace = $exception->getTrace();
    }
    // If the Exception/Error came from a downstream node, adopt the downstream properties.
    if (isset($exception->node)) {
      $cc_error->node = $exception->node;
      $cc_error->method = $exception->method;
      $cc_error->path = $exception->path;
      $cc_error->user = $exception->user;
      $cc_error->message = $exception->getMessage();
    }
    return $cc_error;
  }

  /**
   * @return \ReflectionClass
   */
  private static function getReflection() {
    $class = get_called_class();
    return new \ReflectionClass($class);
  }

}
