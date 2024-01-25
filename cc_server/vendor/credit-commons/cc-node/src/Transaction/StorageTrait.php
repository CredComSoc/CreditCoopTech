<?php

namespace CCNode\Transaction;

use CCNode\Accounts\Trunkward;
use CCNode\Transaction\Entry;
use CCNode\Accounts\Remote;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\PermissionViolation;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\InvalidFieldsViolation;
use CCNode\Db;
use function CCNode\accountStore;


/**
 * Read and write transactions (and entries) from the database
 */
trait StorageTrait {

  static $timeFormat = 'Y-m-d H:i:s';

  /**
   * @param type $uuid
   * @return \Transaction
   */
  static function loadByUuid($uuid) : Transaction {
    global $cc_user, $cc_config;
    // Select the latest version
    $q = "SELECT id as txID, uuid, written, scribe, version, type, state "
      . "FROM transactions "
      . "WHERE uuid = '$uuid' "
      . "ORDER BY version DESC "
      . "LIMIT 0, 1";
    $tx = Db::query($q)->fetch_object();
    if (!$tx) {
      throw new DoesNotExistViolation(type: 'transaction', id: $uuid);
    }
    $divisor = pow(10, static::DECIMAL_PLACES);
    $q = "SELECT payee, payer, description, quant/$divisor as quant, trunkward_quant as trunkwardQuant, author, metadata, is_primary as isPrimary "
      . "FROM entries "
      . "WHERE txid = $tx->txID "
      . "ORDER BY id ASC";
    $result = Db::query($q);
    if ($result->num_rows < 1) {
      throw new CCFailure("Database entries table has no rows for $uuid");
    }
    while ($row = $result->fetch_object()) {
      if ($tx->state == 'validated' and $row->author <> $cc_user->id and !$cc_user->admin and $row->isPrimary) {
        // Deny the transaction exists to all but its author and admins
        throw new PermissionViolation();
      }
      $row->metadata = unserialize($row->metadata);
      // replace the full payee and payer path, ready to upcast the row.
      foreach (['payee', 'payer'] as $role) {
        if (isset($row->metadata->{$row->{$role}})) {
          $row->$role .= '/'.$row->metadata->{$row->{$role}};
        }
      }
      $tx->entries[] = $row;
    }

    return Transaction::create($tx);
  }

  /**
   * Write the transaction entries to the database.
   *
   * @return int
   *   The id of the new transaction version.
   *
   * @note No database errors are anticipated.
   */
  public function saveNewVersion() : int {
    global $cc_user;
    if ($this->state == 'validated') {
      // No user would ever need more than one transaction in validated state.
      $this->deleteValidatedByUser($cc_user->id);
    }

    $now = date(self::$timeFormat);
    $this->written = $now;
    $this->version++;

    $query = "INSERT INTO transactions (uuid, version, type, state, scribe, written) "
    . "VALUES ('$this->uuid', $this->version, '$this->type', '$this->state', '$cc_user->id', '$this->written')";
    $success = Db::query($query);

    $new_id = Db::query("SELECT LAST_INSERT_ID() as id")->fetch_object()->id;
    $this->writeEntries($new_id);

    Db::query("DELETE FROM transaction_index WHERE uuid = '$this->uuid'");
    if (in_array($this->state, static::COUNTED_STATES)) {
      $this->writeIndex($new_id);
    }
    $this->responseMode = TRUE; // Feels awkward but is still the best place for this.
    return $new_id;
  }

  /**
   * Write two rows to the index table for each transaction.
   *
   * @param int $new_id
   */
  private function writeIndex(int $new_id) {
    global $cc_config;
    $query = "INSERT INTO transaction_index (id, uuid, uid1, uid2, type, state, income, expenditure, diff, volume, written, is_primary) VALUES ";
    $divisor = pow(10, static::DECIMAL_PLACES);
    foreach ($this->entries as $e) {
      $quant = $e->quant * $divisor;
      $isPrimary = (int)$e->isPrimary;
      $rows[] = "($new_id, '$this->uuid', '$e->payer', '$e->payee', '$this->type', '$this->state', -$quant, $quant, -$quant, $quant, '$this->written', $isPrimary)";
      $rows[] = "($new_id, '$this->uuid', '$e->payee', '$e->payer', '$this->type', '$this->state', +$quant, -$quant, $quant, $quant, '$this->written', $isPrimary)";
    }
    Db::query($query . implode(', ', $rows));
  }


  /**
   * @param string $acc_id
   */
  function deleteValidatedByUser(string $acc_id) {
    $result = Db::query("SELECT id FROM transactions where state = 'validated' and scribe = '$acc_id' AND uuid <> '$this->uuid'")->fetch_object();
    if ($result) {
      Db::query("DELETE FROM transactions WHERE id = $result->id");
      Db::query("DELETE FROM entries WHERE txid = $result->id");
    }
  }

  /**
   * Suitable for calling by cron.
   */
  static function cleanValidated() {
    global $cc_config;
    $cutoff_moment = date(self::$timeFormat, time() - $cc_config->validatedWindow);

    $result = Db::query("SELECT id FROM transactions where state = 'validated' and written < '$cutoff_moment'");
    foreach ($result->fetch_all() as $row) {
      $ids[] = $row[0];
    }
    $in = '('.implode(',', $ids).')';
    Db::query("DELETE FROM transactions WHERE id IN $in");
    Db::query("DELETE FROM entries WHERE txid = $in");
  }

  /**
   * Write hashes for the remote payer and/or payee.
   * @param int $id
   */
  protected function writeHashes(int $id) {
    $payee = $this->entries[0]->payee;
    $payer = $this->entries[0]->payer;
    if ($payee instanceOf Remote) {
      $payee_hash = $this->makeHash($payee);
      Db::query("INSERT INTO hash_history (txid, acc_id, hash) VALUES ($id, '$payee->id', '$payee_hash')");
    }
    if ($payer instanceOf Remote) {
      $payer_hash = $this->makeHash($payer);
      Db::query("INSERT INTO hash_history (txid, acc_id, hash) VALUES ($id, '$payer->id', '$payer_hash')");
    }
  }

  /**
   * Update the entries table for subsequent versions of a transaction
   * @param int $new_txid
   * @return void
   */
  protected function writeEntries(int $new_txid) : void {
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
   * Save an entry to the entries table for the first time.
   * @param int $txid
   * @param Entry $entry
   * @return int
   *   the new entry id
   * @note No database errors are anticipated.
   */
  private function insertEntry(int $txid, Entry $entry) : int {
    global $cc_config;
    // Calculate metadata for local storage
    foreach (['payee', 'payer'] as $role) {
      $$role = $name = $entry->{$role}->id;
      if ($entry->{$role} instanceof Remote) {
        $entry->metadata->{$name} = $entry->{$role}->relPath;
      }
    }
    $metadata = serialize($entry->metadata);
    $desc = Db::connect()->real_escape_string($entry->description);
    $primary = $entry->primary??0;
    $trunkward_quant = 0;
    if ($entry->payer instanceOf Trunkward or $entry->payee instanceof Trunkward) {
      $trunkward_quant = $entry->trunkwardQuant;
    }
    $quant = $entry->quant * pow(10, static::DECIMAL_PLACES);
    $q = "INSERT INTO entries (txid, payee, payer, quant, trunkward_quant, description, author, metadata, is_primary) "
      . "VALUES ($txid, '$payee', '$payer', $quant, $trunkward_quant, '$desc', '$entry->author', '$metadata', '$primary')";
    return $this->id = Db::query($q);
  }

  /**
   * {@inheritDoc}
   */
  function delete() {
    Db::query("DELETE FROM transactions WHERE uuid = '$this->uuid'");
    Db::query("DELETE FROM entries WHERE txid = '$this->txID'");
  }

  /**
   * @return []
   *   0: A list of transaction uuids, 1: total number of results.
   */
  static function filter(array $params) : array {
    $results = [];
    $sort = $params['sort']??'id';
    $dir = $params['dir']??'desc';
    $limit = $params['limit']??100;
    $offset = $params['offset']??0;
    unset($params['sort'], $params['dir'], $params['limit'], $params['offset']);
    $query = "SELECT t.uuid "
      . "FROM entries e "
      . "JOIN transactions t ON e.txid = t.id "
      . "INNER JOIN (SELECT MAX(id) as id, MAX(version) as version, uuid FROM transactions GROUP BY uuid) v on v.uuid = t.uuid "
      . static::filterConditions(...$params)
      . " GROUP BY t.uuid ";
    $result = Db::query($query);
    $count = mysqli_num_rows($result);
    if ($count) {
      $query .= " ORDER BY max($sort) ". strtoUpper($dir).", ";
      $query .= " MAX(e.id) ".strtoUpper($dir);
      $query .= " LIMIT $offset, $limit";
      $query_result = Db::query($query);
      while ($row = $query_result->fetch_object()) {
        $results[] = $row->uuid;
      }
    }
    else {
      $count = 0;
    }
    return [$results, $count];
  }

  /**
   *
   * @param array $params
   * @return array
   *   0 is a list of uuid and 1 is the total number of results.
   */
  static function filterEntries(array $params) : array {
    $results = [];
    $sort = $params['sort'];
    $dir = $params['dir'];
    $limit = $params['limit'];
    $offset = $params['offset'];
    unset($params['sort'], $params['dir'], $params['limit'], $params['offset']);
    // Get only the latest version of each row in the transactions table.
    $query = "SELECT e.id, t.uuid
      FROM entries e
      LEFT JOIN transactions t on t.id = e.txid
      INNER JOIN (SELECT MAX(version) as version, MAX(id) as id, uuid FROM transactions GROUP BY uuid) t1 ON t.id = t1.id "
      . static::filterConditions(...$params);
    // count the results first, if there are any, run the query again with the pager.
    $count_query = str_replace('e.id, t.uuid', 'COUNT(t.uuid) as count', $query);
    $count = Db::query($count_query)->fetch_object()->count;
    if ($count) {
      $query .= " ORDER BY $sort ". strtoUpper($dir).", "
        . "  e.id ".strtoUpper($dir).", "
        . "  is_primary DESC "
        . " LIMIT $offset, $limit";
      $query_result = Db::query($query);
      while ($row = $query_result->fetch_object()) {
        $results[$row->id] = $row->uuid;
      }
    }
    return [$results, (int)$count];
  }

  /**
   * @param array $params
   *   Valid fields: payer, payee, involving, type, types, state, states, since, until, author, description
   *   Other params: sort (field name), dir (asc or desc), limit, offset
   *
   * @return string[]
   *   subclauses for the WHERE, to be joined by AND
   *
   * @note It is not possible to filter by signatures needed or signed because
   * they don't exist, as such, in the db.
   * @note you can't filter on metadata here.
   * @todo add a filter on quant
   */
  private static function filterConditions(
    string $payer = NULL,
    string $payee = NULL,
    string $involving = NULL,
    string $scribe = NULL,
    string $description = NULL,
    string $states = NULL,//comma separated
    string $state = NULL,//todo should we pass an array here?
    string $types = NULL,//comma separated
    string $type = NULL,//todo should we pass an array here?
    string $since = NULL,
    string $until = NULL) : string
  {
    global $cc_user;
    // Validation
    foreach (['since', 'until'] as $time) {
      if (isset($$time)) {
        if (preg_match(static::REGEX_DATE, $$time)) {
          continue;
        }
        elseif (preg_match(static::REGEX_DATE, $$time)) {
          't.'.$$time .= ' 00:00:00';
          continue;
        }
        throw new InvalidFieldsViolation(type: 'transactionFilter', fields: [$time]);
      }
    }
    if (isset($state) and !isset($states)) {
      $states = [$state];
    }
    if (isset($type) and !isset($types)) {
      $types = [$type];
    }
    $conditions = [];
    if (isset($payer)) {
      if ($col = strpos($payer, '/')) {
        $conditions[] = "e.metadata LIKE '%$payer%'";
      }
      else {
        // At the moment metadata only stores the real address of remote parties.
        $conditions[]  = "e.payer = '$payer'";
      }
    }
    if (isset($payee)) {
      if ($col = strpos($payee, '/')) {
        $conditions[] = "e.metadata LIKE '%$payee%'";
      }
      else {
        // At the moment metadata only stores the real address of remote parties.
        $conditions[]  = "e.payee = '$payee'";
      }
    }
    if (isset($scribe)) {
      $conditions[]  = "t.scribe = '$scribe'";
    }
    if (isset($involving)) {
      if ($col = strpos($involving, '/')) {
        $conditions[] = "( e.metadata LIKE '%$payer%'";
      }
      elseif (strpos($involving, ',')) {
        $items = explode(',', $involving);
        $as_string = implode("','", explode(',', $involving));
        // At the moment metadata only stores the real address of remote parties.
        $conditions[]  = "(e.payee IN ('$as_string') OR e.payer IN ('$as_string'))";
      }
      else {
        $conditions[]  = "(e.payee = '$involving' OR e.payer = '$involving')";
      }
    }
    if (isset($description)) {
      $conditions[]  = "e.description LIKE '%$description%'";
    }
    // NB this uses the date the transaction was last written, not created.
    // to use the created date would require joining the current query to transactions where version =1
    if (isset($until)) {
      $date = date(self::$timeFormat, strtotime($until));
      $conditions[]  = "t.written < '$date'";
    }
    if (isset($since)) {
      $date = date(self::$timeFormat, strtotime($since));
      $conditions[]  = "t.written > '$date'";
    }
    if (isset($states)) {
      if (is_string($states)) {
        $states = explode(',', $states);
      }
      if (in_array('validated', $states)) {
        // only the author can see transactions in the validated state.
        $conditions[]  = "(t.state = 'validated' AND e.author = '$cc_user->id')";
      }
      else {
        // transactions with version 0 are validated but not confirmed by their creator.
        // They don't really exist and could be deleted, and can only be seen by their creator.
        $conditions[] = 't.version > 0';
      }
      $conditions[] = self::manyCondition('t.state', $states);
    }
    else {
      $conditions[] = "t.state <> 'erased'";
      $conditions[] = "(t.state <> 'validated' OR (t.state = 'validated' AND e.author = '$cc_user->id'))";
    }
    $conditions[] ="(t.version > 0 OR e.author = '$cc_user->id')";
    if (isset($types)) {
      $conditions[] = self::manyCondition('t.type', $types);
    }
    if (isset($uuid)) {
      $conditions[]  = "t.uuid = '$uuid'";
    }
    $query = '';
    if ($conditions) {
     $query .= ' WHERE '.implode(' AND ', $conditions);
    }
    return $query;
  }

  /**
   * Database query builder helper
   * @param string $fieldname
   * @param array $vals
   * @return string
   */
  private static function manyCondition  (string $fieldname, array $vals) : string {
    if ($vals) {
      foreach ($vals as $s) {$strings[] = "'".$s."'";}
      return $fieldname .' IN ('.implode(',', $strings).') ';
    }
    return '';
  }

  /*
   * @todo decide whether to use transaction creation or written dates.
   * Written is easier with the current architecture.
   */
  static function accountHistory($acc_id) : \mysqli_result {
    global $cc_config;
    $divisor = pow(10, static::DECIMAL_PLACES);
    Db::query("SET @csum := 0");
    // Not very efficient!
    $query = "SELECT written, (@csum := @csum/$divisor + diff/$divisor) as balance "
      . "FROM transaction_index "
      . "WHERE uid1 = '$acc_id' "
      . "ORDER BY written ASC";
    return Db::query($query);
  }

  static function accountSummary($acc_id) : \mysqli_result {
    global $cc_config;
    $divisor = pow(10, static::DECIMAL_PLACES);
    $query = "SELECT uid2 as partner, income/$divisor as income, expenditure/$divisor as expenditure, diff/$divisor as diff, volume/$divisor as volume, state, is_primary as isPrimary "
      . "FROM transaction_index "
      . "WHERE uid1 = '$acc_id' and state in ('completed', 'pending')";
    return Db::query($query);
  }

  /**
   *
   * @param bool $include_virgin_wallets
   * @return array
   */
  static function getAccountSummaries($include_virgin_wallets = FALSE) : array {
    global $cc_config;
    $results = $balances = [];
    $divisor = pow(10, static::DECIMAL_PLACES);
    $result = Db::query("SELECT uid1, uid2, diff/$divisor as diff, state, is_primary "
      . "FROM transaction_index "
      . "WHERE income > 0");
    while ($row = $result->fetch_object()) {
      foreach ([$row->uid1, $row->uid2] as $uid) {
        if (!isset($balances[$uid])) {
          $balances[$uid] = (object)[
            'completed' => \CCNode\TradeStats::builder(),
            'pending' => \CCNode\TradeStats::builder()
          ];
        }
      }
      $balances[$row->uid1]->pending->logTrade($row->diff, $row->uid2, $row->is_primary);
      $balances[$row->uid2]->pending->logTrade(-$row->diff, $row->uid1, $row->is_primary);
      if ($row->state == 'completed') {
        $balances[$row->uid1]->completed->logTrade($row->diff, $row->uid2, $row->is_primary);
        $balances[$row->uid2]->completed->logTrade(-$row->diff, $row->uid1, $row->is_primary);
      }
    }
    if ($include_virgin_wallets) {
      // Excludes trunkward account.
      $all_account_names = accountStore()->filter(full: false);
      $missing = array_diff($all_account_names, array_keys($balances));
      foreach ($missing as $name) {
        $balances[$name] = (object)[
          'completed' => \CCNode\TradeStats::builder(),
          'pending' => \CCNode\TradeStats::builder()
        ];
      }
    }
    return $balances;
  }


  /**
   * @param string $uuid
   * @return static[]
   */
  static function loadEntriesByUuid(string $uuid) : array {
    global $cc_user;
    $ids = [];
    $query = "SELECT distinct (e.id) as id, e.is_primary
      FROM entries e
      INNER JOIN transactions t ON t.id = e.txid
      INNER JOIN (SELECT MAX(version) as version, uuid FROM transactions group by uuid) t1 ON t1.version = t.version
      WHERE t.uuid = '$uuid'
      ORDER BY e.is_primary DESC, e.id ASC;";
    $result = Db::query($query);
    $entries = [];
    while ($row = $result->fetch_object()){
      $ids[] = $row->id;
    }
    if (empty($ids)) {
      throw new DoesNotExistViolation(type: 'transaction', id: $uuid);
    }
    $entries = static::loadEntries($ids);
    // We just check the first (primary) entry
    if (reset($entries)->state == 'validated' and reset($entries)->author <> $cc_user->id and !$cc_user->admin) {
      // deny the transaction exists to all but its author and admins
      throw new DoesNotExistViolation(type: 'transaction', id: $uuid);
    }
    return $entries;
  }



  /**
   * Load a flat entry from the database, returning items in the order given.
   *
   * @param array $entry_ids
   * @return \static[]
   */
  static function loadEntries(array $entry_ids) : array {
    $divisor = pow(10, static::DECIMAL_PLACES);
    if (empty($entry_ids)) {
      throw new CCFailure('No entry ids to load');
    }
    $query = "SELECT e.id as eid, e.*, t.* FROM entries e
      JOIN transactions t ON t.id = e.txid
      WHERE e.id IN (".implode(',', $entry_ids).")";
    $entries = [];
    foreach (Db::query($query)->fetch_all(MYSQLI_ASSOC) as $row) {
      $data = (object)$row;
      // @todo Get the full paths from the metadata
      $data->metadata = unserialize($data->metadata);
      $data->quant /= $divisor;
      $entries[] = EntryFull::create($data);
    }
    // Return the entries in the same order they were asked for.
    array_multisort($entry_ids, $entries);
    return $entries;
  }
}
