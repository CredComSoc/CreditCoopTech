<?php

namespace CreditCommons\Exceptions;

/**
 * Replaces \Exception
 */
abstract class CCError Extends \Error {

  // These properties are used to reconstruct the object upstream.
  public string $node;
  public string $class;

  function __construct($message, $code) {
    parent::__construct($message, $code);
  }

  /**
   * This is only used when this class is called directly with $message
   * @return string
   */
  function makeMessage() :string {
    return $this->message;
  }

  /**
   * Before reconstructing the error make sure all the fields are present and
   * cast to the right type
   *
   * @note this is v similar to CreateFromValidatedStdClassTraitvalidateFields except it takes
   * an array and assumes no fields are missing
   */
  static function validateCastParams(array &$fields) {
    $class = get_called_class();
    $reflection = new \ReflectionClass($class);
    $errs = [];
    foreach ($reflection->getConstructor()->getParameters() as $param) {
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
          throw new CCFailure(message: "CCError does not recognise field type: $type");
      }
    }
  }

}
