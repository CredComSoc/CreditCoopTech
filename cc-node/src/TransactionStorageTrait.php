<?php

namespace CCNode;

use CCNode\Entry;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\CCViolation;

trait TransactionStorageTrait {

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
    $q = "SELECT payee, payer, description, quant, author, metadata, is_primary FROM entries "
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
        throw new CCViolation('This transaction has not yet been confirmed by its creator');
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
   * @param array $params
   *   valid keys: state, payer, payee, involving, type, before, after, description, format
   *
   * @return string[]
   *   A list of transaction uuids
   *
   * @note It is not possible to filter by signatures needed or signed because
   * they don't exist, as such, in the db.
   */
  static function filter(array $params) : array {
    global $user;
    $valid_fields = ['payer', 'payee', 'limit', 'sort', 'involving', 'states', 'types', 'before', 'after', 'sort', 'metadata', 'author', 'description'];
    $params += ['sort' => 'created,desc'];
    if ($invalid = array_diff_key($params, array_flip($valid_fields))) {
      throw new CCViolation('Cannot filter on field: '.implode(',', array_keys($invalid)));
    }
    extract($params);
    // Get only the latest version of each row in the transactions table.
    $query = "SELECT e.id, t.uuid FROM transactions t "
      . "INNER JOIN versions v ON t.uuid = v.uuid AND t.version = v.ver "
      // Means we get one result for each entry, so we use array_unique at the end.
      . "LEFT JOIN entries e ON t.id = e.txid ";

//    $query = "SELECT e.id, t.uuid FROM entries e "
//      . "LEFT JOIN transactions t ON t.id = e.txid "
//      . "INNER JOIN versions v ON t.uuid = v.uuid AND t.version = v.ver ";
//      // Means we get one result for each entry, so we use array_unique at the end.";

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
    if (isset($before)) {
      $date = date("Y-m-d H:i:s", strtotime($before));
      $conditions[]  = "updated < '$date'";
    }
    if (isset($after)) {
      $date = date("Y-m-d H:i:s", strtotime($after));
      $conditions[]  = "updated > '$date'";
    }
    if (isset($states)) {
      $states = explode(',', $states);
      if (in_array('validated', $states)) {
        // only the author can see transactions in the validated state.
        $conditions[]  = "(state = 'validated' and author = '$user->id')";
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
    $query .= " ORDER BY $f ".strtoUpper($dir??'desc');

    if (isset($limit)) {
      $query .= " LIMIT $limit ";
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

}