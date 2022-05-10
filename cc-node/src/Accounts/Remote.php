<?php

namespace CCNode\Accounts;
use CreditCommons\AccountRemote;
use CreditCommons\RestAPI;
use CCNode\Db;

/**
 * Class representing a remote account, which authorises using its latest hash.
 */
class Remote extends AccountRemote {

  /**
   * Get the last hash pertaining to this account.
   *
   * @return array
   */
  function getLastHash() : string {
    $query = "SELECT hash "
      . "FROM hash_history "
      . "WHERE acc = '$this->id' "
      . "ORDER BY id DESC LIMIT 0, 1";
    if ($row = Db::query($query)->fetch_object()) {
      return (string)$row->hash;
    }
    else { //No hash because this account has never traded to. Security problem?
      return '';
    }
  }

  public function API() : RestAPI {
    global $config;
    return new RestAPI($this->url, $config['node_name'], $this->getLastHash);
  }

  /**
   * The below functions might work better somewhere else.
   */

  public function getHistory($samples = 0) : array {
    // N.B. Branchward nodes may refuse permission
    return API_calls($this)->getHistory($this->transversalPath, $samples);
  }

  /**
   * {@inheritDoc}
   */
  function getTradeStats() : array {
    // N.B. Branchward nodes may refuse permission
    return API_calls($this)->getStats($this->givenPath);
  }


  /**
   * {@inheritDoc}
   */
  static function getAllTradeStats(bool $details = TRUE) : array {
    $all_accounts = parent::getAllTradeStats($details);
    $map = API_calls($this)->accounts($details, TRUE);
    if ($details) {
      $all_accounts[$this->id]->parents = $map;
    }
    else {
      $all_accounts[$this->id] = $map;
    }

    return $all_accounts;
  }

  function getRelPath() {
    die('Need to calculate the relative path of remote account');
  }

}

