<?php

namespace CCNode;

use CCNode\Accounts\User;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Requester;
use CreditCommons\Account;
use CreditCommons\AccountStoreInterface;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Handle requests & responses from the ledger to the accountStore.
 */
class AccountStore extends Requester implements AccountStoreInterface {

  private $exists = [];

  /**
   * Accounts already retrieved.
   * @var Account[]
   */
  private $cached = [];

  function __construct($base_url) {
    parent::__construct($base_url);
    $this->options[RequestOptions::HEADERS]['Accept'] = 'application/json';
  }

  public static function create() : AccountStoreInterface {
    return new static(getConfig('account_store_url'));
  }

  /**
   * @inheritdoc
   */
  function checkCredentials(string $name, string $pass) : bool {
    try {
      $this->localRequest("creds/$name/$pass");
    }
    catch(ClientException $e) {
      // this would be a 400 error
      return FALSE;
    }
    return TRUE;
  }


  function filter(
    int $offset = 0,
    int $limit = 10,
    bool $local= NULL,
    string $fragment = NULL
  ) : array {
    $path = 'filter';
    if (isset($local)) {
      // covert to a path boolean... tho shouldn't guzzle do that?
      $this->options[RequestOptions::QUERY]['local'] = $local ? 'true' : 'false';
    }
    foreach(['fragment', 'offset', 'limit'] as $param) {
      if (isset($$param)) {
        $this->options[RequestOptions::QUERY][$param] = $$param;
      }
    }
    $results = (array)$this->localRequest($path);
    $pos = array_search(getConfig('trunkward_acc_id'), $results);
    if ($pos !== FALSE) {
      unset($results[$pos]);
    }
    return $results;
  }

  /**
   * @inheritdoc
   */
  function filterFull(
    int $offset = 0,
    int $limit = 10,
    bool $local = NULL,
    string $fragment = NULL
  ) : array {
    if (isset($local)) {
      // covert to a path boolean... tho shouldn't guzzle do that?
      $this->options[RequestOptions::QUERY]['local'] = $local ? 'true' : 'false';
    }
    $this->options[RequestOptions::QUERY]['full'] = 'true';

    foreach(['fragment', 'offset', 'limit'] as $param) {
      if (isset($$param)) {
        $this->options[RequestOptions::QUERY][$param] = $$param;
      }
    }
    // 404?
    $results = (array)$this->localRequest('filter/full');
    // remove the trunkward account
    foreach ($results as $key => $r) {
      if ($r->id == getConfig('trunkward_acc_id')) {
        unset($results[$key]);
        break;
      }
    }
    return array_map([$this, 'upcast'], $results);
  }

  /**
   * @inheritdoc
   */
  function fetch(string $name) : Account {
    $path = urlencode($name);
    try{
      $result = $this->localRequest($path);
    }
    catch (ClientException $e) {
      if ($e->getCode() == 404) {
        // N.B. the name might have been deleted because of GDPR
        throw new DoesNotExistViolation(type: 'account', id: $name);
      }
    }
    catch (\Exception $e) {
      throw new CCFailure("AccountStore returned a ".$e->getCode() ." from $name: ".$e->getMessage());
    }
    return $this->upcast($result);
  }

  /**
   * Get the transaction limits for all accounts.
   * @return array
   */
  function allLimits() : array {
    $limits = [];
    foreach ($this->filterFull() as $info) {
      $limits[$info->id] = (object)['min' => $info->min, 'max' => $info->max];
    }
    return $limits;
  }

  /**
   * @inheritdoc
   */
  public function has(string $name) : bool {
    if ((!in_array($name, $this->exists))) {
      try {

        $this->method = 'HEAD';
        $this->localRequest($name);
        $this->method = 'GET';
      }
      catch (\GuzzleHttp\Exception\RequestException $e) {
        $this->method = 'GET';
        return FALSE;
      }
      $this->exists[] = $name;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  protected function localRequest(string $endpoint = '') {
    $client = new Client(['base_uri' => $this->baseUrl, 'timeout' => 1]);
    if (!empty($this->fields) and !isset($this->options[RequestOptions::BODY])) {
      $this->options[RequestOptions::BODY] = http_build_query($this->fields);
    }
    try{
      $response = $client->{$this->method}($endpoint, $this->options);
    }
    catch (RequestException $e) {
      if ($e->getStatusCode() == 500) {
        throw new CCFailure($e->getMessage());
      }
      throw $e;
    }
    $contents = $response->getBody()->getContents();
    $result = json_decode($contents);
    if ($contents and is_null($result)) {
      throw new CCFailure('Non-json result from account service: '.$contents);
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public static function anonAccount() : Account {
    $obj = ['id' => '-anon-', 'max' => 0, 'min' => 0, 'status' => 1];
    return User::create((object)$obj);
  }

  /**
   * Determine what Account class has been fetched and instantiate it.
   *
   * @param \stdClass $data
   * @return Account
   */
  private function upcast(\stdclass $data) : Account {
    $class = self::determineAccountClass($data);
    $this->cached[$data->id] = $class::create($data);
    return $this->cached[$data->id];
  }

  /**
   * Determine the class of the given Account, considering this node's position
   * in the ledger tree.
   *
   * @param \stdClass $data
   * @param string $trunkward_acc_id
   * @return string
   */
  private static function determineAccountClass(\stdClass $data) : string {
    global $user;
    $trunkward_acc_id = \CCNode\getConfig('trunkward_acc_id');
    if (!empty($data->url)) {
      $upS = $user ? ($data->id == $user->id) : TRUE;
      $trunkward = $data->id == $trunkward_acc_id;
      if ($trunkward and $upS) {
        $class = 'UpstreamTrunkward';
      }
      elseif ($trunkward and !$upS) {
        $class = 'DownstreamTrunkward';
      }
      elseif ($upS) {
        $class = 'UpstreamBranch';
      }
      else {
        $class = 'DownstreamBranch';
      }
    }
    else {
      $class = $data->admin ? 'Admin' : 'User';
    }
    return 'CCNode\Accounts\\'. $class;
  }


}
