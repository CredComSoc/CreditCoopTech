<?php

namespace CreditCommons;
use CreditCommons\Transaction;
use CreditCommons\TradeStats;

interface CreditCommonsInterface {

  const OPERATIONS = [
    'permittedEndpoints' => ['options', '/'],
    'handshake' => ['get', '/handshake'],
    'workflows' => ['get', '/workflows'],
    'absoluteAddress' => ['get', '/address'],
    'accountNameAutocomplete' => ['get', '/accounts/{fragment}'],
    'accountHistory' => ['get', '/account/history/{acc_id}'],
    'accountLimits' => ['get', '/account/limits/{acc_id}'],
    'accountSummary' => ['get', '/account/summary/{acc_id}'],
    'newTransaction' => ['post', '/transaction'],
    'getTransaction' => ['get', '/transaction/{uuid}/{format}'],
    'relayTransaction' => ['post', '/transaction/relay'],
    'filterTransactions' => ['get', '/transactions/filter'],
    'stateChange' => ['patch', '/transaction/{uuid}/{dest_state}'],
    'trunkwardNodes' => ['get', '/trunkwards']
  ];

  /**
   * Get the methods/endpoints permitted to the current user, keyed by the
   * operationId of the documentation
   * @return array
   */
  function getOptions() : array;

  /**
   * Get the list of node names back to the trunk of the tree.
   * @return []
   *   Names of nodes names between here and the top of the tree.
   */
  function getTrunkwardNodeNames() : array;

  /**
   * Check the integrity of the connection with the trunkwards node.
   *
   * @throws HashMismatchViolation
   */
  function handshake() : void;

  /**
   * Apply for a new account to be put on the ledger.
   * @param string $name
   * @return bool
   *   TRUE on success
   * @throws DoesNotExistViolation, BadCharactersViolation
   */
  //public function join(string $name, string $url = '') : bool;

  /**
   * Add rows with the business logic and return rows with converted values.
   *
   * @param ClientEntry $entry
   * @return ClientEntry[]
   *   Any new Entries added by downstream nodes.
   */
  function submitNewTransaction(BaseNewTransaction $transaction) : \stdClass;

  /**
   * Add rows with the business logic and return rows with converted values.
   *
   * @param Transaction $transaction
   * @return ClientEntry[]
   *   Any new Entries added by downstream nodes.
   */
  public function buildValidateRelayTransaction(Transaction $transaction) : array;

  /**
   * @param array $fields
   *
   * @return string[]
   *   The UUIDs.
   */
  function filterTransactions(array $fields = []) : array;


  public function getTransaction(string $uuid, $format = 'full') : \stdClass;

  /**
   * Get a list of balances and times for an account.
   * @param string $acc_id
   * @return array
   *   Balances, keyed by unixtime, starting when the account was opened and ending now.
   */
  function getAccountHistory(string $acc_id, int $samples = 0) : array;

  /**
   * @param string $acc_id
   * @return stdClass
   *   The class containing two TradeStats objects, keyed as pending and completed
   */
  function getAccountSummary(string $acc_id = '') : \stdClass;
  function getAccountSummaries(string $given_path = '') : array;

  /**
   * Get a list of all accounts on the ledger and all parents.
   *
   * @param string $fragment
   * @param bool $tree
   * @return array
   *   The relative addresses of all the accounts
   */
  function accountNameAutocomplete(string $fragment = '') : array;

  /**
   *
   * @param string $acc_id
   * @return \stdClass
   *   with properties 'min' and 'max'
   */
  function getAccountLimits(string $acc_id) : \stdClass;


  /**
   * @param string $uuid
   * @param string $target_state
   *
   * @return void
   *
   * @throws WorkflowViolation
   */
  function transactionChangeState(string $uuid, string $target_state) : void;

  /**
   * Get the supported workflows, localised.
   *
   * @return array[]
   *   arrays of workflows, grouped by ancestor node.
   */
  function getWorkflows() : array;

}
