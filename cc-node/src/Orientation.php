<?php

namespace CCNode;
use CreditCommons\Account;
use CCNode\Accounts\Remote;
use CreditCommons\Exceptions\UnavailableNodeFailure;
use CreditCommons\Exceptions\HashMismatchFailure;


/**
 * Handles everything pertaining to the position of the ledger in the tree.
 */
class Orientation {

  public $downstreamAccount;
  public $upstreamAccount;
  public $localRequest;
  public $trunkwardsAccount = NULL;

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


  function goingTrunkwards() : bool {
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
    global $config, $user;
    $results = [];
    if ($user instanceOf Accounts\User) {
      $remote_accounts = AccountStore()->filter(['status' => 1, 'local' => 0], TRUE);
      foreach ($remote_accounts as $acc) {
        try {
          $acc->API()->handshake();
          $results[$acc->id] = 'ok';
        }
        catch (UnavailableNodeFailure $e) {
          $results[$acc->id] = 'UnavailableNodeFailure';
        }
        catch (HashMismatchFailure $e) {
          $results[$acc->id] = 'HashMismatchFailure';
        }
        catch(\Exception $e) {
          die(get_class($e));
        }
      }
    }
    return $results;
  }

}
