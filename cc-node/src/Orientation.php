<?php

namespace CCNode;
use CreditCommons\Account;
use Accounts\Remote;


/**
 * Handles everything pertaining to the position of the ledger in the tree.
 */
class Orientation {

  public $downstreamAccount;
  public $upstreamAccount;
  public $localRequest;
  private $trunkwardsAccount = NULL;

  /**
   * FALSE for request, TRUE for response mode
   * @var Bool
   */
  public $responseMode;

  function __construct() {
    global $config, $user;
    $this->responseMode = 0;
    $this->upstreamAccount = $user;
    if (!empty($config['bot']['acc_id'])) {
      $this->trunkwardsAccount = load_account($config['bot']['acc_id']);
    }
  }


  /**
   * Any remote account which isn't the upstreamAccount is marked as a downstream account
   */
  function addAccount(Account $acc) : void {
    if ($acc instanceOf Remote and $acc->id != $this->upstreamAccount->id) {
      // The upstream account is the current user, so any other remote account is downstream.
      $this->downstreamAccount = $acc;
    }
  }

  function getRequester() {
    if ($this->downstreamAccount) {
      return API_calls($this->downstreamAccount);
    }
  }


  function orientToTrunk() : bool {
    if ($this->trunkwardsAccount) {
      $this->downstreamAccount = $this->trunkwardsAccount;
    }
    return isset($this->trunkwardsAccount);
  }

  function isUpstreamBranch() {
    if ($this->trunkwardsAccount) {
      if ($ups = $this->upstreamAccount) {
        if ($ups->id <> $this->trunkwardsAccount->id)
          return TRUE;
      }
      else {
        return TRUE;
      }
    }
  }

  /**
   * Ledger orientation functions. Used for converting transactions to send.
   */
  // return TRUE or FALSE
  function goingDownstream() : bool {
    return $this->downstreamAccount && !$this->responseMode && !$this->localRequest;
  }
  function goingUpstream() : bool {
    return $this->upstreamAccount && $this->responseMode && !$this->localRequest;
  }

  // return TRUE, FALSE
  function goingTrunkwards() {
    return $this->trunkwardsAccount and (
      $this->downstreamAccount == $this->trunkwardsAccount && !$this->responseMode
      or
      $this->upstreamAccount == $this->trunkwardsAccount && $this->responseMode
    ) && !$this->localRequest;
  }

  function upstreamIsTrunkwards() : bool {
    return $this->trunkwardsAccount->id == $this->upstreamAccount->id;
  }


  function adjacentAccount() {
    if (!$this->responseMode) {
      return $this->downstreamAccount;
    }
    else{
      return $this->upstreamAccount ?? 'client';
    }
  }

  /**
   * Check that all the remote nodes are online and the ratchets match
   * @return array
   *   Linked nodes keyed by response_code.
   */
  function handshake() : array {
    global $config;
    $results = [];
    $active_accounts = AccountStore()->filter(['status' => 1, 'class' => 'remote'], TRUE);
    foreach ($active_accounts as $account) {
      if ($account instanceof Remote) {
        //Make sure we load the remote version by giving a path longer than 1 part.
        list($code) = $account->API()->handshake();
        $results[$code][] = $account->id;
      }
    }
    return $results;
  }

}
