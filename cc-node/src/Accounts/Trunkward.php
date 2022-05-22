<?php
namespace CCNode\Accounts;

/**
 * Class representing an account corresponding to an account on another ledger
 */
class Trunkward extends Remote {

  function foreignId() : string {
    return $this->relPath();
  }

  function relPath() : string {
    $parts = explode('/', $this->givenPath);
    if (reset($parts) == $this->id) {
      return substr($this->givenPath, strlen($this->id) + 1);
    }
    return $this->givenPath;
  }
}

