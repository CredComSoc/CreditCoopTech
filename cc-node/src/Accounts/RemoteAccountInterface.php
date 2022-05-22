<?php

namespace CCNode\Accounts;

use CCNode\Accounts\Remote;
use CreditCommons\NodeRequester;
use CCNode\Transaction\Transaction;

/**
 * Class representing a remote account, which authorises using its latest hash.
 */
interface RemoteAccountInterface {

  /**
   * Get the last hash pertaining to this account.
   *
   * @return array
   */
  function getLastHash() : string;

  /**
   * @return NodeRequester
   *   Connection to the remote node
   */
  function API() : NodeRequester;

  /**
   * The path to the remote account relative to this account on the local ledger.
   * @return string
   */
  function relPath() : string;

  /**
   * @return string
   *   'ok' or the class name of the error
   */
  function handshake() : string;

  /**
   * Pass a new transaction to the downstream node for building and validation.
   *
   * @param Transaction $transaction
   * @return Any entries added by downstream nodes, with converted quants, but not upcast.
   */
  function buildValidateRelayTransaction(Transaction $transaction) : array;

  /**
   * Convert entry values (if upstream is trunkwards);
   * @param array $entries
   * @return void
   */
  public function convertTrunkwardEntries(array &$entries) : void;

  /**
   * Autocomplete fragment using account names on a remote node;
   *
   * @param string $fragment
   * @return string[]
   */
  function autocomplete() : array;

  /**
   * Retrieve a remote transaction (with responseMode - TRUE)
   *
   * @param string $uuid
   * @param bool $full
   * @return \stdClass
   */
  function getTransaction(string $uuid, bool $full = TRUE) : \stdClass;

  /**
   * Filter transactions on the remote node.
   *
   * @param array $params
   * @return array
   */
  function filterTransactions(array $params = []) : array;

  /**
   * Get all the account summaries on a remote node.
   *
   * @return array
   */
  function getAccountSummaries() : array;

  /**
   * @return []
   */
  function getAllLimits() : array;

}

