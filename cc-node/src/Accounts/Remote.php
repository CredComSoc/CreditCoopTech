<?php

namespace CCNode\Accounts;

use CCNode\Accounts\User;
use CCNode\Transaction\Transaction;
use CreditCommons\NodeRequester;
use CCNode\Db;
use CreditCommons\Exceptions\CCFailure;

/**
 * An account on another node, represented by an account on the current node.
 */
class Remote extends User implements RemoteAccountInterface {

  /**
   * The path from the node this account references, to a leaf account
   * @var string
   */
  public string $givenPath = '';
  private float $trunkwardConversionRate = 0;

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
    if ($id == \CCNode\getConfig('trunkward_acc_id')) {
      $rate = \CCNode\getConfig('conversion_rate');
      if ($rate <> 1) {
        $this->trunkwardConversionRate = \CCNode\getConfig('conversion_rate');
      }
    }
  }

  static function create(\stdClass $data) : User {
    static::validateFields($data);
    return new static($data->id, $data->min, $data->max, $data->url);
  }

  /**
   * {@inheritdoc}
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

  /**
   * {@inheritdoc}
   */
  public function API() : NodeRequester {
    return new NodeRequester($this->url, \CCNode\getConfig('node_name'), $this->getLastHash());
  }

  /**
   * {@inheritdoc}
   */
  public function buildValidateRelayTransaction(Transaction $transaction) : array {
    $rows = $this->API()->buildValidateRelayTransaction($transaction);
    $this->convertTrunkwardEntries($rows);
    return $rows;
  }

  /**
   * {@inheritdoc}
   */
  public function handshake() : string {
    try {
      $this->API()->handshake();
      return 'ok';
    }
    catch (CCFailure $e) {// fails to catch.
      return get_class($e);
    }
  }

  /**
   * {@inheritdoc}
   */
  function autocomplete() : array {
    return $this->api()->accountNameFilter($this->relPath());
  }

  /**
   * {@inheritdoc}
   */
  public function getTransaction(string $uuid, $full = TRUE) : \stdClass {
    $result = $this->API()->getTransaction($uuid, $this->relPath(), $full);
    $this->convertTrunkwardEntries($result->entries);
    $result->responseMode = TRUE;
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  function getAccountSummary() : \stdClass {
    if ($this->relPath()) {
      $result = $this->API()->getAccountSummary($this->relPath());
      $this->convertSummary($result);
    }
    else {
      $result = parent::getAccountSummary();
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  function getLimits() : \stdClass {
    if ($this->relPath()) {
      $result = $this->API()->getAccountLimits($this->relPath());
      $this->convertLimits($result);// convert values from trunkward nodes.
    }
    else {
      $result = parent::getLimits();
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  function getHistory(int $samples = -1) : array {
    if ($path = $this->relPath()) {
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
   * {@inheritdoc}
   */
  function relPath() : string {
    $parts = explode('/', $this->givenPath);
    // remove everything including the node name.
    $pos = array_search($this->id, $parts);
    if (FALSE !== $pos) {
      $parts = array_slice($parts, $pos+1);
    }
    return implode('/', $parts);
  }

  /**
   * Check if this Account points to a remote account, rather than a remote node.
   * @return bool
   *
   * @todo refactor Address resolver so this isn't necessary in Entry::upcastAccounts
   */
  public function isAccount() {
    return substr($this->givenPath, -1) <> '/';
  }

  /**
   * These methods belong to remote Nodes rather than remote accounts.
   */

  /**
   * {@inheritdoc}
   */
  function getAccountSummaries() : array {
    $summaries = $this->API()->getAccountSummaries($this->relPath());
    foreach ($summaries as $acc_id => &$summary) {
      $this->convertSummary($summary);
    }
    return $summaries;
  }

  /**
   * {@inheritdoc}
   */
  function getAllLimits() : array {
    // why isn't this using the given path?
    $all_limits = (array)$this->API()->getAllAccountLimits($this->relPath());
    foreach ($all_limits as &$limits) {
      $this->convertLimits($limits);
    }
    return $all_limits;
  }

  /**
   * {@inheritdoc}
   */
  function filterTransactions(array $params = []) : array {
    $results = $this->API()->filterTransactions(fields: $params, node_path: $this->relPath());
    foreach ($results as &$result) {
      $this->convertTrunkwardEntries($result->entries);
      $result->responseMode = TRUE;
    }
    return $results;
  }

  /**
   * Convert the quantities if entries are coming from the trunk
   * @param array $entries
   */
  public function convertTrunkwardEntries(array &$entries) : void {
    if ($rate = $this->trunkwardConversionRate) {
      foreach ($entries as &$e) {
        $e->trunkward_quant = $e->quant;
        $e->quant = ceil($e->quant / $rate);
        $e->author = $this->id;
      }
    }
  }

  private function convertSummary(\stdClass $summary) : void {
    if ($rate = $this->trunkwardConversionRate) {
      $summary->pending->receiveTrunkward($rate);
      $summary->completed->receiveTrunkward($rate);
    }
    // @todo convert when Sending trunkward.
  }

  private function convertLimits(\stdClass &$data) : void {
    if ($rate = $this->trunkwardConversionRate) {
      $data->min = ceil($data->min / $rate);
      $data->max = ceil($data->max / $rate);
    }
    // @todo convert when Sending trunkward.
  }

}

