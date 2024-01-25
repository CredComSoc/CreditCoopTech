<?php

namespace CreditCommons;


interface AccountStoreInterface {

  /**
   * Compare the given credentials with the stored credentials.
   *
   * @param string $name
   * @param string $pass
   * @return bool
   *   TRUE if the credentials match
   */
  function compareAuthkey(string $name, string $pass) : bool;

  /**
   * Get a filtered list of account names.
   *
   * Filter on the account name fragments local vs remote, and admin vs non
   * admin. The trunkward account is always excluded from filtered results.
   *
   * @param string $fragment
   * @param bool $local
   * @param bool $admin
   * @param int $limit
   * @param int $offset
   * @return string[]
   */
  function filter(
    string $fragment = NULL,
    bool $local = NULL,
    bool $admin = NULL,
    int $limit = 10,
    int $offset = 0
  ) : array;

  /**
   * Get an account object by name.
   *
   * @param string $name
   *   Need to be clear if this is the local name or a path
   * @param string $remote_path
   * @return Account
   *   The account object
   * @throws DoesNotExistViolation
   * @throws CCFailure
   *
   */
  function fetch(string $name) : Account;

  /**
   * Check if an account exists
   * @param string $name
   * @param string $node_class
   * @return bool
   *   TRUE if the account exists
   */
  public function has(string $name) : bool;

  /**
   * Get an unauthenticated account object.
   * @return Account
   */
  public function anonAccount() : Account;

}
