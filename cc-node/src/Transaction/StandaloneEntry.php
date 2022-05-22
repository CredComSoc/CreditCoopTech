<?php
namespace CCNode\Transaction;

use CCNode\Db;

/**
 * Transaction entry in a flat format.
 */
class StandaloneEntry extends \CreditCommons\StandaloneEntry {

  /**
   * @param string $uuid
   * @return static[]
   */
  static function loadByUuid(string $uuid) : array {
    $query = "SELECT e.id FROM transactions t "
      . "INNER JOIN versions v ON t.uuid = v.uuid AND t.version = v.ver "
      . "LEFT JOIN entries e ON t.id = e.txid "
      . "WHERE t.uuid = '$uuid'";
    $result = Db::query($query);
    $entries = [];
    while ($row = $result->fetch_object()){
      $ids[] = $row->id;
    }
    return static::load($ids);
  }

  /**
   * Load a flat entry from the database, returning items in the order given.
   *
   * @param array $entry_ids
   * @return \static[]
   */
  static function load(array $entry_ids) : array {
    $query = "SELECT * FROM transactions t "
      . "INNER JOIN versions v ON t.uuid = v.uuid AND t.version = v.ver "
      . "LEFT JOIN entries e ON t.id = e.txid "
      . "WHERE e.id IN (".implode(',', $entry_ids).")";
    $entries = [];
    foreach (Db::query($query)->fetch_all(MYSQLI_ASSOC) as $row) {
      $data = (object)$row;
      // @todo Get the full paths from the metadata
      $data->metadata = unserialize($data->metadata);
      $entries[$data->id] = new static(
        $data->uuid,
        $data->payer,
        $data->payee,
        $data->quant,
        $data->type,
        $data->author,
        $data->state,
        $data->written,
        $data->description,
        $data->metadata
      );
    }
    foreach($entry_ids as $id) {
      $sorted[$id] = $entries[$id];
    }
    return $sorted;
  }

}
