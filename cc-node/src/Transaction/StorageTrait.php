<?php

namespace CCNode\Transaction;

use CCNode\Accounts\Trunkward;
use CCNode\Transaction\Entry;
use CCNode\Transaction\EntryTrunkward;
use CCNode\Accounts\Remote;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\CCOtherViolation;
use CCNode\Db;

trait StorageTrait {

  static $timeFormat = 'Y-m-d H:i:s';

  /**
   * @param type $uuid
   * @return \Transaction
   */
  static function loadByUuid($uuid) : Transaction {
    global $user;
    // Select the latest version
    $q = "SELECT id as txID, uuid, written, version, type, state "
      . "FROM transactions "
      . "WHERE uuid = '$uuid' "
      . "ORDER BY version DESC "
      . "LIMIT 0, 1";
    $tx = Db::query($q)->fetch_object();
    if (!$tx) {
      throw new DoesNotExistViolation(type: 'transaction', id: $uuid);
    }
    $q = "SELECT payee, payer, description, quant, trunkward_quant, author, metadata, is_primary FROM entries "
      . "WHERE txid = $tx->txID "
      . "ORDER BY id ASC";
    $result = Db::query($q);
    if ($result->num_rows < 1) {
      throw new CCFailure("Database entries table has no rows for $uuid");
    }
    while ($row = $result->fetch_object()) {
      if ($tx->state == 'validated' and $row->author <> $user->id and !$user->admin and $row->is_primary) {
        // deny the transaction exists to all but its author and admins
        // Note this is a bit misleading but permission error has fields that we can't populate from here.
        throw new CCOtherViolation("Transaction $uuid has not yet been confirmed by its creator");
      }
      $row->metadata = unserialize($row->metadata);
      $tx->entries[] = $row;
    }
    $t_class = Entry::upcastAccounts($tx->entries);

    // All these values should be validated, so no need to use static::create
    $transaction = $t_class::create($tx);

    return $transaction;
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
    global $user;
    if ($this->state == 'validated') {
      // No user would ever need more than one transaction in validated state.
      $this->deleteValidated($user->id);
    }

    $now = date(self::$timeFormat);
    $this->written = $now;
    $this->version++;

    $query = "INSERT INTO transactions (uuid, version, type, state, scribe, written) "
    . "VALUES ('$this->uuid', $this->version, '$this->type', '$this->state', '$user->id', '$this->written')";
    $success = Db::query($query);

    $new_id = Db::query("SELECT LAST_INSERT_ID() as id")->fetch_object()->id;
    $this->writeEntries($new_id);
    $this->responseMode = TRUE; // Feels awkward but is still the best place for this.
    return $new_id;
  }

  function deleteValidated(string $acc_id) {
    $result = Db::query("SELECT id FROM transactions where state = 'validated' and scribe = '$acc_id'")->fetch_object();
    if ($result) {
      Db::query("DELETE FROM transactions WHERE id = $result->id");
      Db::query("DELETE FROM entries WHERE txid = $result->id");
    }
  }

  /**
   * suitable for calling by cron.
   */
  static function cleanValidated() {
    $cutoff_moment = date(self::$timeFormat, time() - \CCNode\getConfig('validated_window'));

    $result = Db::query("SELECT id FROM transactions where state = 'validated' and written < '$cutoff_moment'");
    foreach ($result->fetch_all() as $row) {
      $ids[] = $row[0];
    }
    $in = '('.implode(',', $ids).')';
    Db::query("DELETE FROM transactions WHERE id IN $in");
    Db::query("DELETE FROM entries WHERE txid = $in");
  }

  /**
   *
   * @param int $id
   */
  protected function writeHashes(int $id) {
    $payee_hash = $payer_hash = '';
    if ($this->entries[0]->payee instanceOf Remote) {
      $acc = $this->entries[0]->payee;
      $entries = $this->filterFor($acc, $acc instanceOf Trunkward);
      $payee_hash = $this->getHash($acc, $entries);
    }
    if ($this->entries[0]->payer instanceOf Remote) {
      $acc = $this->entries[0]->payer;
      $entries = $this->filterFor($acc, $acc instanceOf Trunkward);
      $payer_hash = $this->getHash($acc, $entries);
    }
    $query = "UPDATE transactions SET payee_hash = '$payee_hash', payer_hash = '$payer_hash' WHERE id = $id";
    Db::query($query);
  }

  /**
   *
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
   * Save an entry to the entries table.
   * @param int $txid
   * @param Entry $entry
   * @return int
   *   the new entry id
   * @note No database errors are anticipated.
   */
  private function insertEntry(int $txid, Entry $entry) : int {
    // Calculate metadata for local storage
    foreach (['payee', 'payer'] as $role) {
      $$role = $entry->{$role}->id;
      if ($entry->{$role} instanceof Remote) {
        $entry->metadata->{$$role} = $entry->{$role}->givenPath;
      }
    }
    $metadata = serialize($entry->metadata);
    $desc = Db::connect()->real_escape_string($entry->description);
    $primary = $entry->primary??0;
    $trunkward_quant = $entry instanceOf EntryTrunkward ? $entry->trunkward_quant : 0;
    $q = "INSERT INTO entries (txid, payee, payer, quant, trunkward_quant, description, author, metadata, is_primary) "
      . "VALUES ($txid, '$payee', '$payer', '$entry->quant', '$trunkward_quant', '$desc', '$entry->author', '$metadata', '$primary')";
    if ($this->id = Db::query($q)) {
      return (bool)$this->id;
    }
  }


  //This can only be done on transactions in state validated and possibly local transactions.
  function delete() {
    Db::query("DELETE FROM transactions WHERE uuid = '$this->uuid'");
    Db::query("DELETE FROM entries WHERE txid = '$this->txID'");
  }

  /**
   * @param array $params
   *   valid keys: state, payer, payee, involving, type, before, after, description, format
   *
   * @return string[]
   *   A list of transaction uuids
   *
   * @note It is not possible to filter by signatures needed or signed because
   * they don't exist, as such, in the db.
   * @note you can't filter on metadata here.
   */
  static function filter(
    string $sort = 'written,desc',
    string $pager = '0,10',
    string $payer = NULL,
    string $payee = NULL,
    string $involving = NULL,
    string $author = NULL,
    string $description = NULL,
    string $states = NULL,//comma separated
    string $state = NULL,//todo should we pass an array here?
    string $types = NULL,//comma separated
    string $type = NULL,//todo should we pass an array here?
    string $before = NULL,
    string $after = NULL,
  ) : array {
    global $user;
    if (isset($state) and !isset($states)) {
      $states = [$state];
    }
    if (isset($type) and !isset($types)) {
      $type = [$type];
    }
    // Get only the latest version of each row in the transactions table.
    $query = "SELECT e.id, t.uuid FROM transactions t "
      . "INNER JOIN versions v ON t.uuid = v.uuid AND t.version = v.ver "
      // Means we get one result for each entry, so we use array_unique at the end.
      . "LEFT JOIN entries e ON t.id = e.txid ";
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
      $conditions[]  = "e.description LIKE '%$description%'";
    }
    // NB this uses the date the transaction was last written, not created.
    // to use the created date would require joining the current query to transactions where version =1
    if (isset($before)) {
      $date = date(self::$timeFormat, strtotime($before));
      $conditions[]  = "written < '$date'";
    }
    if (isset($after)) {
      $date = date(self::$timeFormat, strtotime($after));
      $conditions[]  = "written > '$date'";
    }
    if (isset($states)) {
      if (is_string($states)) {
        $states = explode(',', $states);
      }
      if (in_array('validated', $states)) {
        // only the author can see transactions in the validated state.
        $conditions[]  = "(state = 'validated' AND author = '$user->id')";
      }
      else {
        // transactions with version 0 are validated but not confirmed by their creator.
        // They don't really exist and could be deleted, and can only be seen by their creator.
        $conditions[] = 't.version > 0';
      }
      $conditions[] = self::manyCondition('state', $states);
    }
    else {
      $conditions[] = "state <> 'erased'";
    }
    $conditions[] ="(t.version > 0 OR e.author = '$user->id')";
    if (isset($types)) {
      $conditions[] = self::manyCondition('type', $types);
    }
    if (isset($uuid)) {
      $conditions[]  = "t.uuid = '$uuid'";
    }
    if (isset($conditions)) {
      $query .= ' WHERE '.implode(' AND ', $conditions);
    }
    list($f, $dir) = explode(',', $sort);
    // @todo check that $f is a field.
    $query .= " ORDER BY $f ".strtoUpper($dir??'desc').", is_primary DESC";

    if (isset($pager)) {
      $query .= " LIMIT $pager ";
    }

    $query_result = Db::query($query);
    $results = [];
    while ($row = $query_result->fetch_object()) {
      $results[$row->id] = $row->uuid;
    }
    return $results;
  }

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
    Db::query("SET @csum := 0");
    $query = "SELECT written, (@csum := @csum + diff) as balance "
      . "FROM transaction_index "
      . "WHERE uid1 = '$acc_id' "
      . "ORDER BY written ASC";
    return Db::query($query);
  }

  static function accountSummary($acc_id) : \mysqli_result {
    $query = "SELECT uid2 as partner, income, expenditure, diff, volume, state, is_primary as isPrimary "
      . "FROM transaction_index "
      . "WHERE uid1 = '$acc_id' and state in ('completed', 'pending')";
    return Db::query($query);
  }

  /**
   *
   * @param bool $include_virgins
   * @return array
   */
  static function getAccountSummaries($include_virgins = FALSE) : array {
    $results = $all_balances = [];
        $balances = [];
    $result = Db::query("SELECT uid1, uid2, diff, state, is_primary "
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
    if ($include_virgins) {
      $all_account_names = \CCNode\accountStore()->filter();
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

}
