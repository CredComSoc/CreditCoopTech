<?php

namespace CCNode\Accounts;

use CCNode\Db;
use CCNode\TradeStats;

/**
 * Provides all interaction between an account and the ledger.
 */
trait AccountSummaryTrait {

  /**
   * @param mixed $samples
   *   NULL means just return the raw points. 0 means show a true time record
   *   with a stepped appearance. otherwise return the number of points to smooth to
   *   steps, points, smoothed = .
   * @return array
   *   Balances keyed by timestamp, oldest first. -1 samples will interpolate
   *   the data to give true balance over time, a stepped pattern. 0 samples will
   *   return the raw data, in the form of diagonal lines, any larger number will
   *   sample the data to make the message size smaller and rendering easier.
   *
   * @note Uses the transaction updated time, not the created time
   */
  function getHistory($samples = -1) : array {
    global $config;
    $points = [];
    Db::query("SET @csum := 0");
    $query = "SELECT updated, (@csum := @csum + diff) as balance "
      . "FROM transaction_index "
      . "WHERE uid1 = '$this->id' "
      . "ORDER BY updated ASC";
    $result = Db::query($query);
    // Database is storing timestamps as 'Y-m-d H:i:s.0'
    // make a first point at zero balance 1 sec before the first balance.
    if ($t = $result->fetch_object()) {
      $start_sec = (new \DateTime($t->updated))->modify('-5 seconds');
      // 2022-02-02 23:39:56.000000
      $points[$start_sec->format('Y-m-d H:i:s')] = 0;
      $points[$t->updated] = round($t->balance, $config['decimal_places']);
      while($t = $result->fetch_object()) {
        $points[$t->updated] = round($t->balance, $config['decimal_places']);
      }
      if ($samples === 0) {
        // the raw data would show diagonal lines
      }
      elseif($samples == -1) {
        $times = $values = [];
        // Make two values for each one in the keys and values.
        foreach ($points as $time => $bal) {
          $secs = strtotime($time);
          $times[] = date("Y-m-d H:i:s", $secs);
          $times[] = date("Y-m-d H:i:s", $secs+1);
          $values[] = $bal;
          $values[] = $bal;
        }
        // Now slide the arrays against each other to create steps.
        array_shift($times);
        array_pop($values);
        unset($points);
        $points = array_combine($times, $values);
      }
      else {
        // For large numbers of transactions can be reduced for ease of rendering.
      }
      if (!$samples and $points) {
        // Finish the line from the last transaction until now.
        $points[date("Y-m-d H:i:s")] = end($points); //this date format corresponds to the mysql DATETIME
        // Note that since the first point is ALWAYS the first transaction in this
        // implementation, we don't create a create a point for initial 0 balance.
      }
    }
    else {
      // Make a start and end point.
      // NB the start time of one year is arbitrary and should be determined by config
      $points[date("Y-m-d H:i:s", strtotime('-1 year'))] = 0;// Because the first transaction adds 2 points
      $points[date("Y-m-d H:i:s")] = 0;// Because the first transaction adds 2 points
    }
    return $points;
  }


  /**
   * @return CreditCommons\TradeStats[]
   *   Two groups of stats, with keys 'completed' and 'pending'.
   */
  function getAccountSummary() : \stdClass {
    $query = "SELECT uid2 as partner, income, expenditure, diff, volume, state, is_primary as isPrimary "
      . "FROM transaction_index "
      . "WHERE uid1 = '$this->id' and state in ('completed', 'pending')";
    $result = Db::query($query);
    $completed = TradeStats::builder();
    $pending = TradeStats::builder();

    while ($row = $result->fetch_object()) {
      // All transactions contribute to the pending stats.
      $pending->logTrade($row->diff, $row->partner, $row->isPrimary);
      if ($row->state == 'completed') {
        // Make stats summariing only completed transactions
        $completed->logTrade($row->diff, $row->partner, $row->isPrimary);
      }
    }
    return (object)[
      'completed' => $completed,
      'pending' => $pending
    ];
  }

  // TODO move the below functions elsewhere.

  /**
   *
   * @param bool $include_virgins
   * @return array
   */
  static function getAccountSummaries($include_virgins = FALSE) : array {
    $results = [];
        $balances = [];
    $result = Db::query("SELECT uid1, uid2, diff, state, is_primary "
      . "FROM transaction_index "
      . "WHERE income > 0");
    while ($row = $result->fetch_object()) {
      foreach ([$row->uid1, $row->uid2] as $uid) {
        if (!isset($balances[$uid])) {
          $balances[$uid] = (object)[
            'completed' => TradeStats::builder(),
            'pending' => TradeStats::builder()
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
      $all_account_names = accountStore()->filter(['status' => TRUE]);
      if (!isset($all_balances[$name])) {
        $balances[$name]->completed = TradeStats::builder();
        $balances[$name]->pending = TradeStats::builder();
      }
    }
    return $results;
  }



  /**
   * Get stats for all members
   * @return array
   *   Stats keyed by acc_id
   */
  private static function getAllLocalTradeStats() : array {

    return $balances;
  }

}
