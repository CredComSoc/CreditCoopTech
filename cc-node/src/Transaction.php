<?php

namespace CCNode;

use CCNode\Entry;
use CCNode\BlogicRequester;
use CCNode\Workflows;
use CreditCommons\Workflow;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\MaxLimitViolation;
use CreditCommons\Exceptions\MinLimitViolation;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\CCViolation;
use CreditCommons\Exceptions\WorkflowViolation;
use CreditCommons\TransactionInterface;
use CreditCommons\BaseTransaction;
use CreditCommons\Account;
use CreditCommons\TransversalEntry;

class Transaction extends BaseTransaction implements \JsonSerializable {

  /**
   * Create a new transaction from a few required fields defined upstream.
   * @param stdClass $data
   *   well formatted payer, payee, description & quant and array of stdClass entries.
   * @return \static
   */
  public static function createFromUpstream(\stdClass $data) : BaseTransaction {
    global $user;
    $data->author = $user->id;
    $data->state = TransactionInterface::STATE_INITIATED;
    $data->entries[0]->primary = 1;
    $data->entries = static::createEntries($data->entries, $user);
    $class = static::determineTransactionClass($data->entries);
    return $class::create($data);
  }

  /**
   * @param array $entries
   * @return boolean
   *   TRUE if these entries imply a TransversalTransaction
   */
  protected static function determineTransactionClass(array $entries) : string {
    foreach ($entries as $entry) {
      if ($entry instanceOf TransversalEntry) {
        return 'CCNode\TransversalTransaction';
      }
    }
    return 'CCNode\Transaction';
  }

  /**
   * @param type $uuid
   * @return \Transaction
   */
  static function loadByUuid($uuid) : Transaction {
    global $orientation, $user;
    $q = "SELECT id as txID, uuid, created, updated, version, type, state FROM transactions "
      . "WHERE uuid = '$uuid' "
      . "ORDER BY version DESC "
      . "LIMIT 0, 1";
    $tx = Db::query($q)->fetch_object();
    if (!$tx) {
      throw new DoesNotExistViolation(type: 'transaction', id: $uuid);
    }
    $q = "SELECT payee, payer, description, quant, author, metadata FROM entries "
      . "WHERE txid = $tx->txID "
      . "ORDER BY id ASC";
    $result = Db::query($q);
    if ($result->num_rows < 1) {
      throw new CCFailure("Database entries table has no rows for $uuid");
    }
    while ($row = $result->fetch_object()) {
      if ($tx->state == 'validated' and $row->author <> $user->id and !$user->admin) {
        // deny the transaction exists to all but its author and admins
        throw new DoesNotExistViolation(type: 'transaction', id: $uuid);
      }
      $row->metadata = unserialize($row->metadata);
      $entry_rows[] = $row;
    }
    $tx->entries = static::createEntries($entry_rows);
    $class = static::determineTransactionClass($tx->entries);
    // All these values should be validated, so no need to use static::create
    $transaction = $class::create($tx);

    return $transaction;
  }

  /**
   * Call the business logic, append entries.
   * Validate the transaction in its workflow's 'creation' state
   */
  function buildValidate() : void {
    global $loadedAccounts, $config, $user;

    $workflow = $this->getWorkflow();
    if (empty($desired_state)) {
      $desired_state = $workflow->creation->state;
    }
    if (!$workflow->canTransitionToState($user->id, $this, $desired_state, $user->admin)) {
      throw new WorkflowViolation(
        acc_id: $user->id,
        type: $this->type,
        from: $this->state,
        to: $desired_state,
      );
    }
    // Add fees, etc by calling on the blogic service
    if ($config['blogic_service_url']) {
      $fees = (new BlogicRequester($config['blogic_service_url']))->appendTo($this);
      // @todo. Validate these since they came from another microservice
      foreach ($fees as $row) {
        $this->entries[] = Entry::create($row)->additional();
      }
    }
    foreach ($this->sum() as $acc_id => $info) {
      $account = load_account($acc_id);
      $ledgerAccountInfo = (new Wallet($account))->getTradeStats();
      $projected = $ledgerAccountInfo['pending']['balance'] + $info->diff;
      if ($projected > $this->payee->max) {
        throw new MaxLimitViolation(acc_id: $acc_id, limit: $this->payee->max, projected: $projected);
      }
      elseif ($projected < $this->payer->min) {
        throw new MinLimitViolation(acc_id: $acc_id, limit: $this->payer->min, projected: $projected);
      }
    }
    $this->state = TransactionInterface::STATE_VALIDATED;
  }

  /**
   * @param string $target_state
   * @throws \Exception
   */
  function changeState(string $target_state) {
    $this->sign($target_state);
  }

  /**
   *
   * @global Account $user
   * @param string $target_state
   * @return $this
   * @throws WorkflowViolation
   */
  function sign(string $target_state) {
    global $user;
    if (!$this->getWorkflow()->canTransitionToState($user->id, $this, $target_state, $user->admin)) {
      throw new WorkflowViolation(
        acc_id: $user->id,
        type: $this->type,
        from: $this->state,
        to: $target_state,
      );
    }

    $this->state = $target_state;
    $this->saveNewVersion();
    return $this;
  }

  /**
   * Write the transaction entries to the database.
   *
   * @note No database errors are anticipated.
   */
  public function saveNewVersion() {
    global $user;
    $now = date("Y-m-d H:i:s");
    $this->version++;
    if ($this->version < 2) {
      $this->created = $now;
    }
    $this->updated = $now;

    $query = "INSERT INTO transactions (uuid, version, type, state, scribe, created, updated) "
    . "VALUES ('$this->uuid', $this->version, '$this->type', '$this->state', '$user->id', '$this->created', '$this->updated')";
    $success = Db::query($query);

    $connection = \CCNode\Db::connect();
    $new_id = $connection->query("SELECT LAST_INSERT_ID() as id")->fetch_object()->id;
    $this->writeEntries($new_id);
  }

  protected function writeEntries($new_txid) : void{
    if ($this->txID) {// this transaction has already been written in an earlier state
      $q = "UPDATE entries set txid = $new_txid WHERE txid = $this->txID";
      Db::query($q);
    }
    else {// this is the first time the transaction is written properly
      reset($this->entries)->primary = TRUE;
      foreach ($this->entries as $entry) {
        $this->insertEntry($new_txid, $entry);
      }
    }
  }

  /**
   * Save an entry to the entries table.
   * @param int $txid
   * @param Entry $entry
   * @return int
   *   the new entry id
   * @note No database errors are anticipated.
   */
  private function insertEntry(int $txid, Entry $entry) : int {
    foreach (['payee', 'payer'] as $role) {
      $$role = $entry->{$role}->id;
      if ($entry->{$role} instanceof RemoteAccount) {
        $entry->metadata[$$role] = $entry->{$role}->givenPath;
      }
    }
    $metadata = serialize($entry->metadata);
    $desc = Db::connect()->real_escape_string($entry->description);
    $primary = $entry->primary??0;
    $q = "INSERT INTO entries (txid, payee, payer, quant, description, author, metadata, is_primary) "
      . "VALUES ($txid, '$payee', '$payer', '$entry->quant', '$desc', '$entry->author', '$metadata', '$primary')";
    if ($this->id = Db::query($q)) {
      return (bool)$this->id;
    }
  }

  /**
   * Magic method. Look for any unknown properties to the first entry.
   * @param string $name
   * @return type
   */
  function __get($name) {
    $valid = ['payee', 'payer', 'description'];
    if (isset($this->entries[0]->$name)) {
      return $this->entries[0]->$name;
    }
    throw new CCFailure('Requested unknown property of Transaction: '.$name);
  }

  /**
   * Add up all the transactions and return the differences in balances for
   * every involved user.
   *
   * @param Transaction $transaction
   * @return array
   *   The differences, keyed by account name.
   */
  public function sum() : array {
    $accounts = [];
    foreach ($this->entries as $entry) {
      $accounts[$entry->payee->id] = $entry->payee;
      $accounts[$entry->payer->id] = $entry->payer;
      $sums[$entry->payer->id][] = -$entry->quant;
      $sums[$entry->payee->id][] = $entry->quant;
    }
    foreach ($sums as $localName => $diffs) {
      $accounts[$localName]->diff = array_sum($diffs);
    }
    return $accounts;
  }

  /**
   * @param array $params
   *   valid keys: state, payer, payee, involving, type, before, after, description, format
   * @param string $format
   *   the name of the transaction format to return. entry, full, or uuid
   *
   * @return []
   *   Depending on 'format': full gives normal transactions with entries indexed
   *   by uuid; uuid gives a list of uuids; 'entry' gives a list of Extended entries
   *
   * @note It is not possible to filter by signatures needed or signed because
   * they don't exist, as such, in the db.
   */
  static function filter(array $params) : array {
    global $user;
    extract($params);
    // Get only the latest version of each row in the transactions table.
    $query = "SELECT e.id, t.uuid FROM transactions t "
      . "INNER JOIN versions v ON t.uuid = v.uuid AND t.version = v.ver "
      . "LEFT JOIN entries e ON t.id = e.txid";
    if (isset($payer)) {
      if ($col = strpos($payer, '/')) {
        $conditions[] = "metadata LIKE '%$payer%'";
      }
      else {
        // At the moment metadata only stores the real address of remote parties.
        $conditions[]  = "payer = '$payer'";
      }
    }
    if (isset($payee)) {
      if ($col = strpos($payee, '/')) {
        $conditions[] = "metadata LIKE '%$payee%'";
      }
      else {
        // At the moment metadata only stores the real address of remote parties.
        $conditions[]  = "payee = '$payee'";
      }
    }
    if (isset($author)) {
      $conditions[]  = "author = '$author'";
    }
    if (isset($involving)) {
      if ($col = strpos($involving, '/')) {
        $conditions[] = "( metadata LIKE '%$payer%'";
      }
      else {
        // At the moment metadata only stores the real address of remote parties.
        $conditions[]  = "(payee = '$involving' OR payer = '$involving')";
      }
    }
    if (isset($description)) {
      $conditions[]  = "description LIKE '%$description%'";
    }
    if (isset($before)) {
      $date = date("Y-m-d H:i:s", strtotime($before));
      $conditions[]  = "updated < '$date'";
    }
    if (isset($after)) {
      $date = date("Y-m-d H:i:s", strtotime($after));
      $conditions[]  = "updated > '$date'";
    }
    if (isset($state) and $state <> 'validated') {
      $conditions[]  = "state = '$state'";
    }
    if (isset($state) and $state == 'validated') {
      // only the author can see transactions in the validated state.
      $conditions[]  = "(state = '$state' and author = '$user->id')";
    }
    else {
      $conditions[] = 'version > 0';
    }
    if (isset($type)) {
      $conditions[]  = "type = '$type'";
    }
    if (isset($uuid)) {
      $conditions[]  = "t.uuid = '$uuid'";
    }
    if (isset($conditions)) {
      $query .= ' WHERE '.implode(' AND ', $conditions);
    }
    $query_result = Db::query($query);
    $results = [];
    while ($row = $query_result->fetch_object()) {
      $results[$row->id] = $row->uuid;
    }
    return $results;
  }

  /**
   * Export the transaction to json for transport.
   * - get the actions
   * - remove some properties.
   *
   * @return array
   *
   * @todo make transitions a property or function of the transaction object.
   */
  public function jsonSerialize() : array {
    global $user;
    return [
      'uuid' => $this->uuid,
      'updated' => $this->updated,
      'state' => $this->state,
      'type' => $this->type,
      'version' => $this->version,
      'entries' => $this->entries,
      'transitions' => $this->getWorkflow()->getTransitions($user->id, $this, $user->admin)
    ];
  }

  /**
   * Load this transaction's workflow from the local json storage.
   * @todo Sort out
   */
  public function getWorkflow() : Workflow {
    if ($w = (new Workflows())->get($this->type)) {
      return $w;
    }
    throw new DoesNotExistViolation(type: 'workflow', id: $this->type);
  }

  /**
   *
   * @param stdClass[] $rows
   *   Which are Entry objects flattened by json for transport.
   * @param string $author
   * @return Entry[]
   *   The created entries
   */
  protected static function createEntries(array $rows, Account $author = NULL) : array {
    global $config;
    $entries = [];
    foreach ($rows as $row) {
      if (!$row->quant and !$config['zero_payments']) {
        throw new CCViolation(message: "Zero transactions not allowed on this node.");
      }
      if ($author){
        $row->author = $author->id;
      }
      $row->payer = load_account($row->payer);
      $row->payee = load_account($row->payee);
      $entries[] = Entry::create($row);
    }
    return $entries;
  }

}
