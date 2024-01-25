<?php

namespace CCNode\Accounts;

use CCNode\Accounts\User;
use CCNode\Transaction\Transaction;
use CCNode\Db;
use CreditCommons\NodeRequester;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\HashMismatchFailure;


/**
 * An account on another node, represented by an account on the current node.
 */
abstract class Remote extends User implements RemoteAccountInterface {

  /**
   * The path from the node this account references, to a leaf account
   * @var string
   */
  public string $relPath = '';
  private string $lastHash;

  function __construct(
    string $id,
    int $min,
    int $max,
    /**
     * The url of the remote node
     * @var string
     */
    public string $url
  ) {
    parent::__construct($id, $min, $max, FALSE);
  }

  static function create(\stdClass $data, string $rel_path = '') : User {
    static::validateFields($data);
    $acc = new static($data->id, $data->min, $data->max, $data->url);
    $acc->relPath = $rel_path;
    return $acc;
  }

  /**
   * {@inheritDoc}
   */
  public function isNode() : bool {
    return empty($this->relPath) or substr($this->relPath, -1) == '/';
  }

  /**
   * {@inheritDoc}
   */
  function getLastHash() : string {
    if (!isset($this->lastHash)) {
      $this->lastHash = '';
      $query = "SELECT hash FROM hash_history WHERE acc_id = '$this->id' ORDER BY txid DESC LIMIT 0, 1";
      /** @var \mysqli_result $result */
      $result = Db::query($query);
      if ($result->num_rows) {
        $this->lastHash = $result->fetch_object()->hash;
      }
    }
    return $this->lastHash;
  }

  /**
   * {@inheritDoc}
   */
  public function relayTransaction(Transaction $transaction) : array {
    return $this->API()->buildValidateRelayTransaction($transaction);
  }

  /**
   * {@inheritDoc}
   */
  public function handshake() : string {
    try {
      $this->API()->handshake();
      return 'ok'; // @todo shouldn't this return nothing or fail?
    }
    catch (CCFailure $e) {// fails to catch.
      return get_class($e);
    }
  }

  /**
   * {@inheritDoc}
   */
  function autocomplete() : array {
    return $this->API()->accountNameFilter($this->relPath);
  }

  /**
   * {@inheritDoc}
   */
  function getSummary($force_local = FALSE) : \stdClass {
    if ($force_local) {
      $result = parent::getSummary();
    }
    else {
      // An account on another (branchward) node
      $result = $this->API()->getAccountSummary($this->relPath);
      $result = reset($result);
    }
    return $result;
  }

  /**
   * {@inheritDoc}
   */
  function getAllSummaries() : array {
    // the relPath should have a slash at the end of it.
    return $this->API()->getAccountSummary($this->relPath);
  }

  /**
   * {@inheritDoc}
   */
  function getLimits($force_local = FALSE) : \stdClass {
    if ($this->relPath) {
      $result = $this->API()->getAccountLimits($this->relPath);
      // Always returns an array
      $result = reset($result);
    }
    else {
      $result = parent::getLimits();
    }
    return $result;
  }

  function getAllLimits() : array {
    // the relPath should always have a slash at the end of it.
    return $this->API()->getAccountLimits($this->relPath);
  }

  /**
   * {@inheritDoc}
   */
  function getHistory(int $samples = -1) : array {
    if ($path = $this->relPath) {
      $result = (array)$this->api()->getAccountHistory($path, $samples);
      if ($rate = $this->trunkwardConversionRate) {
        $result = array_map(function ($v) use ($rate) {return ceil($v/$rate);}, $result);
      }
    }
    else {
      $result = (array)parent::getHistory($samples);
    }
    return $result;
  }

  /**
   * {@inheritDoc}
   */
  protected function API() : NodeRequester {
    global $cc_config;
    return new NodeRequester($this->url, $cc_config->nodeName, $this->getLastHash());
  }

  /**
   * {@inheritDoc}
   */
  function authenticate(string $remote_hash) {
    $local_hash = $this->getLastHash();
    if ($remote_hash == $local_hash) {
      return;
    }
    throw new HashMismatchFailure($this->id, $local_hash, $remote_hash);
  }

  function __toString() {
    return $this->id . '/'.$this->relPath;
  }


  /**
   * {@inheritDoc}
   */
  function getConversionRate() : \stdClass {
    return $this->api()->about($this->relPath);
  }

}
