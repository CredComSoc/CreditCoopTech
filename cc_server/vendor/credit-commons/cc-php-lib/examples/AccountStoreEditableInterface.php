<?php

namespace Examples;
use CreditCommons\AccountStoreInterface;


interface AccountStoreEditableInterface extends AccountStoreInterface {

  /**
   * Write the account settings.
   *
   * @param string $acc_id
   * @param stdClass $properties
   *   With properties 'min', 'max' and 'url' or 'key'
   */
  function update(string $acc_id, \stdClass $properties);

}
