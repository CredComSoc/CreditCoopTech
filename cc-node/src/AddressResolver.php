<?php
namespace CCNode;

use CreditCommons\AccountStoreInterface;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\CCOtherViolation;
use CreditCommons\Exceptions\PathViolation;
use CCNode\Accounts\Remote;
use CCNode\Accounts\User;

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
  private $userId;

  function __construct(AccountStoreInterface $accountStore, string $absolute_path) {
    global $user;
    $this->accountStore = $accountStore;
    $parts = explode('/', $absolute_path);
    $this->nodeName = array_pop($parts);
    $this->trunkwardName = array_pop($parts);
    $this->userId = $user->id;
  }

  static function create() {
    return new static(accountStore(), getConfig('abs_path'));
  }


  // Resolve string to local or remote account.
  // for addressing transactions
  function localOrRemoteAcc(string &$rel_path) : User {
    if (substr($rel_path, -1) == '/') {
      throw new PathViolation(relPath: $rel_path, context: 'localOrRemoteAcc');
    }
    $acc = $this->nearestNode($rel_path);
    return $acc;
  }


  // resolve string to node and fragment
  function nodeAndFragment(string &$rel_path) : User|NULL {
    $parts = explode('/', $rel_path);
    if (count($parts) > 1) {
      return $this->remoteNode($rel_path);
    }
    return NULL;
  }

  // Don't worry about the end of the path, just find the local account
  function nearestNode(string &$given_path) : User {
    global $user;
    $path_parts = explode('/', $given_path);
    $pos = array_search($this->nodeName, $path_parts);
    if ($pos !== FALSE) {
      $path_parts = array_slice($path_parts, $pos+1);
    }
    $acc_id = reset($path_parts);
    if ($this->accountStore->has($acc_id)) {
      return \CCNode\load_account($acc_id, $given_path);
    }
    // the account name wasn't known, so load the trunkward account with the full given path.
    if ($pos == FALSE and $this->trunkwardName and $user->id <> $this->trunkwardName) {
      return \CCNode\load_account($this->trunkwardName, $given_path);
    }

    throw new PathViolation(relPath: $given_path, context: 'nearestNode');
  }

  function remoteNode(string &$rel_path) : Remote {
    $acc = $this->nearestNode($rel_path);
    if (!$acc instanceOf Remote) {
      throw new PathViolation(relPath: $rel_path, context: 'remoteNode');
    }
    return $acc;

  }

}
