<?php

namespace CreditCommons;

use CreditCommons\TransactionInterface;

interface CreditCommonsInterface {

  /**
   * The names of the operations correspond with the API calls
   * @note, this info is replicated by the slim functions.
   */
  const OPERATIONS = [
    'absolutePath' => ['get', '/absolutepath'],
    'accountNameFilter' => ['get', '/accounts/{fragment}'],
    'accountHistory' => ['get', '/account/history/{acc_id}'],
    'accountLimits' => ['get', '/account/limits/{acc_id}'],
    'accountSummary' => ['get', '/account/summary/{acc_id}'],
    'about' => ['get', '/about'],
    'filterTransactions' => ['get', '/transactions/'],
    'filterTransactionEntries' => ['get', '/entries/'],
    'getTransaction' => ['get', '/transaction/{uuid}'],
    'getEntries' => ['get', '/entries/{uuid}'],
    'handshake' => ['get', '/handshake'],
    'newTransaction' => ['post', '/transaction'],
    'permittedEndpoints' => ['options', '/'],
    'relayTransaction' => ['post', '/transaction/relay'],
    'stateChange' => ['patch', '/transaction/{uuid}/{dest_state}'],
    'workflows' => ['get', '/workflows']
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
  function getAbsolutePath() : array;

  /**
   * Check the integrity of the connection with the trunkwards node. Final
   * response format should be decided. A leaf implementation would return a list
   * of responses keyed by node name, where 0 means no response. A node
   * implementation would return an empty array or throw an Exception.
   *
   * @throws HashMismatchViolation
   */
  function handshake() : array;

  /**
   * Get a list of all leaf accounts on the current node and all trunkward nodes,
   * including matches ending with a / for other branches. Note that those
   * branches may or may not choose to expose their account names.
   *
   * @param string $rel_path
   *   The path to the node whose account are to be filtered
   * @param int $limit
   *   The filters, i.e. string fragment; bool local
   * @return array
   *   The names of all the matched accounts on the node
   *
   * @todo convert this to named arguments
   */
  public function accountNameFilter(string $rel_path = '', $limit = 10) : array;

  /**
   * Get a list of balances and times for an account.
   *
   * @param string $acc_id
   * @return array
   *   Balances, keyed by unixtime, starting when the account was opened and ending now.
   */
  function getAccountHistory(string $acc_id, int $samples = 0) : array;

  /**
   * Get a summary of one or all accounts on a node.
   *
   * @param string $acc_id
   * @return stdClass[]
   *   Each stdClass contains TradeStats objects, keyed as pending and completed
   */
  function getAccountSummary(string $acc_id = '') : array;

  /**
   * Get limits of one or all accounts on a node.
   *
   * @param string $acc_id
   * @return array
   *   Objects with min & max, keyed by local account name.
   */
  function getAccountLimits(string $acc_id) : array;

  /**
   * Add rows with the business logic and return rows with converted values.
   *
   * @param \CreditCommons\Entry[]
   *   Entries added by downstream nodes.
   */
  public function buildValidateRelayTransaction(TransactionInterface $transaction) : array;

  /**
   * @param array $params
   *   Fields to filter on can be 'payer', 'payee', 'involving', 'before',
   *   'after', 'uuid', 'quant', 'description', 'states', 'type', 'is_primary',
   *   'uuid'. Other keys are 'entries', 'sort', 'dir', 'limit', and 'offset'
   *   See OpenAPI spec for more details.
   *
   * @return array
   *   Transactions and meta->transitions keyed by uuid @todo Out of date???
   */
  function filterTransactions(array $params = []) : array;

  /**
   * @param array $params
   *   Fields to filter on can be 'payer', 'payee', 'involving', 'before',
   *   'after', 'uuid', 'quant', 'description', 'states', 'type', 'is_primary',
   *   'uuid'. Other keys are 'entries', 'sort', 'dir', 'limit', and 'offset'
   *   See OpenAPI spec for more details.
   *
   * @return EntryFull[]
   */
  function filterTransactionEntries(array $params = []) : array;

  /**
   * Retrieve one transaction, by uuid. No output type is given because any
   * implementation might want to upcast the transaction to its own internal object.
   *
   * @param string $uuid
   *
   * @return array
   *   Transaction and meta->transitions keyed by uuid
   */
  public function getTransaction(string $uuid) : array;

  /**
   * Retrieve one transaction as entries, by uuid. No links
   *
   * @param string $uuid
   *
   * @return array
   *   Transaction Entries
   */
  public function getTransactionEntries(string $uuid) : array;

  /**
   * Change the state of the the transaction, or delete it.
   *
   * @param string $uuid
   * @param string $target_state
   *   One of the enumerated values or string 'null' to delete a transaction in validated state.
   *
   * @return int
   *   TRUE if a new version of the transaction was written. FALSE if the transaction was deleted.
   *
   * @throws WorkflowViolation
   *
   * @note Transactions involving remote accounts must not be deleted because it would break the hash chain.
   */
  function transactionChangeState(string $uuid, string $target_state) : void;

  /**
   * Get the supported workflows, localised.
   *
   * @return array[]
   *   arrays of workflows, grouped by ancestor node.
   */
  function getWorkflows() : array;

  /**
   * Retrieve the conversion rate of 1: to the remote node's rate.
   *
   * @param string $node_path
   * @return array
   *   The currency symbol and the exchange rate relative to 1
   */
  function about(string $node_path) : \stdClass;

}
