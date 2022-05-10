<?php

namespace CCNode;

use CCNode\Db;
use CreditCommons\Account;

/**
 * Provides all interaction between an account and the ledger.
 */
class Wallet {

  //Also possible: partners_global
  const TRADE_STATS = ['entries', 'partners_local', 'trades', 'gross_in', 'gross_out', 'balance', 'volume'];

   /**
    * @param Account $account
    */
  function __construct(Account $account) {
    $this->account = $account;
  }

  function __get($name) {
    if (isset($this->account->{$name})) {
      return $this->account->{$name};
    }
  }

  /**
   * @param mixed $samples
   *   NULL means just return the raw points. 0 means show a true time record
   *   with a stepped appearance. otherwise return the number of points to smooth to
   *   steps, points, smoothed = .
   * @return array
   *   Balances keyed by timestamp, oldest first
   *
   * @note It uses the transaction updated time, not the created time
   * @todo URGENT this reads all versions of transactions as separate transactions
   */
  function getHistory($samples = 0) : array {
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
      elseif($samples) {
        //
      }
      if (!$samples and $points) {
        // Finish the line from the last transaction until now.
        $points[date("Y-m-d H:i:s")] = end($points); //this date format corresponds to the mysql DATETIME
        // Note that since the first point is ALWAYS the first transaction in this
        // implementation, we don't create a create a point for initial 0 balance.
      }
    }
    return $points;
  }


  /**
   * @return array
   *   Two groups of stats, with keys 'completed' and 'pending'.
   */
  function getTradeStats() : array {
    $query = "SELECT uid2, income, expenditure, diff, volume, state, is_primary as isPrimary "
      . "FROM transaction_index "
      . "WHERE uid1 = '$this->id'";
    $result = Db::query($query);
    $vals = [
      'balance' => 0,
      'trades' => 0,
      'entries' => 0,
      'volume' => 0,
      'gross_in' => 0,
      'gross_out' => 0
    ];
    $stats = ['completed' => $vals, 'pending' => $vals];
    $pending_partners = $completed_partners = [];
    while ($row = $result->fetch_object()) {
      $stats['pending']['balance'] += $row->diff;
      $stats['pending']['gross_in'] += $row->income;
      $stats['pending']['gross_out'] += $row->expenditure;
      $stats['pending']['volume'] += $row->volume;
      $stats['pending']['entries']++;

      if ($row->state == 'completed') {
        $stats['completed']['balance'] += $row->diff;
        $stats['completed']['gross_in'] += $row->volume;
        $stats['completed']['gross_out'] += $row->expenditure;
        $stats['completed']['volume'] += $row->volume;
        $stats['completed']['entries']++;
      }
      if ($row->isPrimary) {
        $stats['pending']['trades']++;
        $pending_partners[] = $row->uid2;
        if ($row->state == 'completed') {
          $stats['completed']['trades']++;
          $completed_partners[] = $row->uid2;
        }
      }
    }
    $stats['completed']['partners'] = count(array_unique($completed_partners));
    $stats['pending']['partners'] = count(array_unique($pending_partners));
    return $stats;
  }

  // TODO move the below functions elsewhere.

  /**
   *
   * @param bool $include_virgins
   * @return array
   */
  static function getAllTradeStats($include_virgins = FALSE) : array {
    $results = [];
    // NB this is only the balances of accounts which have traded.
    $all_balances = static::getAllLocalTradeStats();
    foreach ($all_account_names as $name) {
      $results[$name] = $all_balances[$name]['completed'] ?? (object)$default;
    }
    if ($include_virgins) {
      $all_account_names = accountStore()->filter(['status' => TRUE]);
      foreach (static::TRADE_STATS as $stat) {
        $default[$stat] = 0;
      }
      foreach ($all_account_names as $name) {
        if (!isset($results[$name])) {
          $results[$name] = $default;
        }
      }

    }

    return $results;
  }

  /**
   * Get all the tradestats in the system with just one db query.
   * @return array
   */
  private static function getAllLocalTradeStats() : array {
    $balances = [];
    $result = Db::query("SELECT uid1, uid2, income, expenditure, diff, volume, state, is_primary FROM transaction_index WHERE income > 0");
    while ($row = $result->fetch_object()) {
      $balances[$row->uid1]['pending']->gross_in[] = $row->income;
      $balances[$row->uid2]['pending']->gross_out[] = $row->income;
      if ($row->is_primary) {
        $balances[$row->uid1]['pending']->partners_local[] = $row->uid2;
        $balances[$row->uid2]['pending']->partners_local[] = $row->uid1;
        $balances[$row->uid1]['pending']->trades[] = 1;
        $balances[$row->uid2]['pending']->trades[] = 1;
      }
      if ($row->state == 'completed') {
        $balances[$row->uid1]['completed']->gross_in[] = $row->income;
        $balances[$row->uid2]['completed']->gross_out[] = $row->income;
        if ($row->is_primary) {
          $balances[$row->uid1]['completed']->partners_local[] = $row->uid2;
          $balances[$row->uid2]['completed']->partners_local[] = $row->uid1;
          $balances[$row->uid1]['completed']->trades[] = 1;
          $balances[$row->uid2]['completed']->trades[] = 1;
        }
      }
    }
    foreach ($balances as &$states) {
      foreach ($states as &$data) {
        foreach (static::TRADE_STATS as $stat) {
          switch ($stat) {
            case 'entries':
              $val = count($data->partners_local);
              break;
            case 'partners_local':
              $val = count(array_unique($data->partners_local));
              break;
            case 'trades':
              $val = count($data->trades);
              break;
            case 'gross_in':
              $val = array_sum($data->gross_in);
              break;
            case 'gross_out':
              $val = array_sum($data->gross_out);
              break;
            case 'balance':
              $val = $data->gross_in - $data->gross_out;
              break;
            case 'volume':
              $val = $data->gross_in + $data->gross_out;
              break;
          }
          $data->{$stat} = $val;
        }
      }
    }
    return $balances;
  }

}
