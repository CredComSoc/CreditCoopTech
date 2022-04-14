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
    return new RestAPI($this->url, $config['node_name'], $this->getLastHash());
  }

  /**
   * The below functions might work better somewhere else.
   */

  public function getHistory($samples = 0) : array {
    // N.B. Branchward nodes may refuse permission
    return $this->API()->getHistory($this->transversalPath, $samples);
  }

  /**
   * {@inheritDoc}
   */
  function getAccountSummary() : array {
    // N.B. Branchward nodes may refuse permission
    return $this->API()->getStats($this->givenPath);
  }


  /**
   * {@inheritDoc}
   * @todo this functions returns a slightly different format on branchwards and trunkwards accounts.
   */
  static function getAccountSummaries() : array {
    $all_accounts = parent::getAccountSummaries();
    $map = $this->API()->accounts([], TRUE);
    // Add this node's summaries to the trunkwards data
    $all_accounts[$this->id]->parents = $map;
    return $all_accounts;
  }

  function getRelPath() : string {
    die('Need to calculate the relative path of remote account');
  }

}

