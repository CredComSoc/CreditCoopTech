<?php

namespace CreditCommons;

use \CreditCommons\CreateFromValidatedStdClassTrait;

/**
 * This object represents an account in the ledger. It does not form part of the
 * public API but is strongly implied to be included in the API library.
 *
 * @todo make a new interface to distinguish passive user objects.
 */
abstract class Account implements \JsonSerializable{
  use CreateFromValidatedStdClassTrait;

  function __construct(
    public string $id,
    public int $min,
    public int $max
  ) {}

  static function create(\stdClass $data) : Account {
    static::validateFields($data);
    return new static($data->id, $data->min, $data->max);
  }

  /**
   * {@inheritDoc}
   */
  function isNode() : bool {
    return FALSE;
  }

  /**
   * Get the limits for this, or a remote account.
   * @param bool $force_local
   *   TRUE to get the limits of the account on the local ledger, not any remote ledger
   * @return \stdClass
   */
  abstract function getLimits($force_local = FALSE) : \stdClass|array;

  /**
   * Get summary of this or a remote account.
   *
   * @param bool $force_local
   *   TRUE to get the summary of the account on the local ledger, not any remote ledger
   *
   * @return \stdClass
   *   The account Summary, not upcast. Two groups of stats, with keys 'completed' and 'pending'.
   */
  abstract function getSummary($force_local = FALSE) : \stdClass|array;

  /**
   * Authenticate the account against the string provided (from the request headers)
   *
   * @throw CCVIolation|HashMismatchFailure
   */
  abstract function authenticate(string $string);

  /**
   * return a path relative to the current node suitable for passing trunkwards
   */
  abstract function trunkwardPath() : string;

  /**
   * return a path relative to the current node suitable for passing leafwards
   */
  abstract function leafwardPath() : string;

  /**
   * Flatten the object
   */
  function __toString() {
    return $this->id;
  }

  function jsonSerialize(): mixed {
    return $this->trunkwardPath();
  }
}
