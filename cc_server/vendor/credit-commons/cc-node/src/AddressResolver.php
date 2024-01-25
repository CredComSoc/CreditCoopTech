<?php
namespace CCNode;

use CreditCommons\AccountStoreInterface;
use CreditCommons\Exceptions\PathViolation;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CCNode\Accounts\Remote;
use CCNode\Accounts\User;
use function CCNode\accountStore;
use function CCNode\load_account;

/**
 * Convert all errors into an stdClass, which includes a field showing
 * which node caused the error.
 *
 * Consider branch called a/b/c/d/e where a is the trunk and e is an end account.
 * From anywhere in the tree
 * resolveToLocalAccount(b/c/d) returns the d account on node c
 * resolveToNode(b/c/d) returns a requester for d node from any where in the tree.
 * These work from anywhere because they include the top level branch name.
 * It is never necessary to name the trunk node.
 * Meanwhile on node c to reach node d account e, you could pass anything from a/b/c/d/e to just d/e
 *
 */
class AddressResolver {

  private $accountStore;
  private $nodeName;
  private $trunkwardName;
  private $cache = [];

  function __construct(AccountStoreInterface $accountStore, string $absolute_path) {
    global $cc_user;
    $this->accountStore = $accountStore;
    $parts = explode('/', $absolute_path);
    $this->nodeName = array_pop($parts);
    $this->trunkwardName = array_pop($parts);
  }

  static function create() {
    global $cc_config;
    return new static(accountStore(), $cc_config->absPath);
  }

  /**
   * Resolve string to local or remote account.
   * for addressing transactions
   * @param string $rel_path
   * @return User
   * @throws PathViolation
   */
  function localOrRemoteAcc(string &$rel_path) : User {
    if (empty($rel_path)) {

    }
    if (substr($rel_path, -1) == '/') {
      throw new PathViolation(relPath: $rel_path, context: 'localOrRemoteAcc');
    }
    $acc = $this->getLocalAccount($rel_path);
    if (is_null($acc)) {
      throw new DoesNotExistViolation(type: 'user', id: $rel_path);
    }
    return $acc;
  }

  /**
   * Resolve string to node and fragment
   * @param string $rel_path
   * @return User|NULL
   */
  function nodeAndFragment(string &$rel_path) : User|NULL {
    $parts = explode('/', $rel_path);
    if (count($parts) > 1) {
      return $this->remoteNode($rel_path);
    }
    return NULL;
  }

  /**
   * Don't worry about the end of the path, just find the local account
   *
   * @global type $user
   * @param string $given_path
   * @return User|NULL
   * @throws PathViolation
   */
  function getLocalAccount(string &$given_path) : User|NULL {
    global $cc_user;
    if (!isset($this->cache[$given_path])) {
      $path_parts = explode('/', $given_path);
      $pos = array_search($this->nodeName, $path_parts);
      if ($pos !== FALSE) {
        $path_parts = array_slice($path_parts, $pos+1);
      }
      // the account name is now the first path part
      $acc_id = array_shift($path_parts);
      if (count($path_parts) == 1 and empty($path_parts[0])) {
        $rel_path = '/';
      }
      else {
        $rel_path = implode('/', $path_parts);
      }

      if ($acc_id == '') {
        // the current node
        $this->cache[$given_path] = NULL;
      }
      elseif ($this->accountStore->has($acc_id) and $acc_id <> $this->trunkwardName) {
        $this->cache[$given_path] = load_account($acc_id, $rel_path);
      }
      // Load the trunkward account always with the full given path.
      //elseif ($pos == FALSE and $this->trunkwardName and ($cc_user->id <> $this->trunkwardName)) {
      elseif ($pos == FALSE and $this->trunkwardName) {
        $acc = load_account($this->trunkwardName, $given_path);
        $this->cache[$given_path] = $acc;
      }
      else {
        // There is no local account and no trunkward account.
        throw new PathViolation(relPath: $given_path, context: 'localAccount');
      }
    }
    return $this->cache[$given_path];
  }

  /**
   * @param string $rel_path
   * @return Remote|NULL
   * @throws PathViolation
   */
  function remoteNode(string &$rel_path) : Remote|NULL {
    $acc = $this->getLocalAccount($rel_path);
    if (is_null($acc)) {
      return NULL;
    }
    elseif (!$acc instanceOf Remote) {
      throw new PathViolation(relPath: $rel_path, context: 'remoteNode');
    }
    return $acc;

  }

}
