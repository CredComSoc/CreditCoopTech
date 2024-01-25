<?php

namespace CreditCommons;
use CreditCommons\Exceptions\InvalidFieldsViolation;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Account;

/**
 * Trait for validing stdClasses (converted from json) before making classes.
 */
trait CreateFromValidatedStdClassTrait {

  /**
   * Check an stdclass has the required fields and cast them to the right type.
   *
   * @param stdClass $obj
   *   Field types, keyed by name. Possible values are string, int, bool, array
   * @throws InvalidFieldsViolation
   *
   * @note this wouldn't be needed if we could control the type checking error of class:__construct
   * @todo this could also work in a base class for Transaction, Entry and Account
   */
  static function validateFields(\stdClass &$obj) {
    $class = get_called_class();
    $reflection = new \ReflectionClass($class);
    $errs = [];
    foreach ($reflection->getConstructor()->getParameters() as $param) {
      /** @var ReflectionParameter $param */
      $name = $param->getName();
      $type = $param->getType();
      if (!isset($obj->{$name})) {
        // HasDefault value doesn't seem to work unless this reflection is of an instantiated class.
        // But we can't instantiate the class because we are here to prepare the arguments.
        // So we can't throw errors here for missing fields, because they _might_ have a default value.
        // Missing fields will instead cause a class construction error.
        if (!$param->isDefaultValueAvailable()) {// never
          //$errs[$name] = "Missing $name in ".$class;
        }
        // Missing fields are not instantiated but defaults are set in create();
        $errs[$name] = "Missing $name from passed params";
        continue;
      }
      $val = $obj->{$name};
      switch($type) {
        case 'string':
          if ((string)$val == $val) {
            $obj->{$name} = (string)$val;
            continue 2;
          }
          break;
        case 'int':
          if (is_numeric($val) and (int)$val == $val) {
            $obj->{$name} = (int)$val;
            continue 2;
          }
          break;
        case 'float':
          if (is_numeric($val)) {
            $obj->{$name} = (float)$val;
            continue 2;
          }
          break;
        case 'bool':
          if ((bool)($val) == $val) {
            $obj->{$name} = (bool)$val;
            continue 2;
          }
          break;
        case 'array':
          if (is_iterable($val) or $val instanceOf \stdClass) {
            if (!is_array($val)) {
              $obj->{$name} = (array)$val;
            }
            continue 2;
          }
          break;
        case 'stdClass':
          if ($val instanceOf \stdClass) {
            $obj->{$name} = $val;
            continue 2;
          }
          break;
        case 'CreditCommons\Account':
          if ($val instanceOf Account) {
            continue 2;
          }
          break;
        //@todo, handle internal classes, though we may not be able to cast them
        default:
          throw new CCFailure(get_class() ." does not recognise '$type' field $name with value: ".print_r($val, 1));
      }
      if (gettype($val) == 'object') {
        $val = get_class($val);
      }
      $errs[$name] = gettype($val).' '.print_r($val, 1) ." should be a $type in ". get_called_class();
    }
    if ($errs) {
      throw new InvalidFieldsViolation(fields: $errs, type: get_called_class());
    }
  }

}

