<?php

namespace CCNode;


class Cron {

  function __construct(array $ini_array, string $acc_id) {
    $this->node = new \CCNode\Node($ini_array, $acc_id); // sets globals
  }

  function do() {
    $this->clearOldValidated();
    $this->cacheWorkflows();
  }

  function clearOldValidated() {
    $date = new DateTime('-1 day');
    $result = DB::query("SELECT id as c, uuid FROM transactions WHERE type = 'validated' and written < ".$date->format());
    while ($row = $result->fetch_object()) {
      Db:query("DELETE FROM transactions WHERE uuid = '$row->uuid'");
      Db:query("DELETE FROM entries WHERE txid = '$row->txID'");
    }
  }

  /**
   * @todo we need a place to store the cached workflows
   */
  function cacheWorkflows() {

  }

}
