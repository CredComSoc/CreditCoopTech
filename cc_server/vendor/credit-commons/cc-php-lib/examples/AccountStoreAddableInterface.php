<?php

namespace Examples;


interface AccountStoreAddableInterface extends \CreditCommons\AccountStoreEditableInterface {

  /**
   * Create a new account
   *
   * @param string $acc_id
   * @param bool $remote
   * @param stdClass $properties
   */
  function insert(string $acc_id, bool $remote, \stdClass $properties);

}
