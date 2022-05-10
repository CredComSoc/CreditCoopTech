<?php

namespace CCNode\Accounts;

/**
 * Class representing an account
 */
abstract class Branch extends Remote {

  /**
   *
   * @param stdClass $account
   *   converted json from the AccountStorage Account class
   * @param string $given_path
   */
  function __construct(\stdClass $account, string $given_path = '') {
    parent::__construct($account, $given_path);
    if ($given_path) {
      $parts = explode('/', $given_path);
      $pos = array_search($this->id, $parts);
      if ($branchwards = array_slice($parts, $pos + 1)) {
        $tail = implode('/', $branchwards);
        $this->relative .= "/$tail";
      }
    }
  }

}
