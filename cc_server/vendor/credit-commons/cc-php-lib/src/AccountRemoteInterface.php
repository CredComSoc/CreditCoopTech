<?php

namespace CreditCommons;

/**
 * Class representing a remote account (or remote node)
 */
interface AccountRemoteInterface {

  /**
   * Get the last hash pertaining to this account.
   *
   * @return array
   */
  function getLastHash() : string;

  /**
   * Check if this Account points to a remote account, rather than a remote node.
   *
   * @return bool
   *   TRUE if this object references a remote account, not a whole node
   *
   * @todo refactor Address resolver so this isn't necessary in Entry::upcastAccounts
   */
  public function isNode() : bool;

  /**
   * @return string
   *   'ok' or the class name of the error
   */
  function handshake() : string;
  
}
