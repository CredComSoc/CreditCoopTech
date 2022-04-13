<?php

namespace CCNode;

use CCNode\Accounts\User;
use CCNode\Accounts\BoT;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Requester;
use CreditCommons\Account;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Handle requests & responses from the ledger to the accountStore.
 *
 * Instantiate this object by calling AccountStore::Create();
 */
class AccountStore extends Requester {

  /**
   * Accounts already retrieved.
   * @var Account[]
   */
  private $cached = [];

  function __construct($base_url) {
    parent::__construct($base_url);
    $this->options[RequestOptions::HEADERS]['Accept'] = 'application/json';
  }

  static function create() {
    global $config;
    return new static($config['account_store_url']);
  }

  function checkCredentials($name, $pass) : bool {
    try {
      $this->localRequest("creds/$name/$pass");
    }
    catch(ClientException $e) {
      // this would be a 400 error
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Filter on the account names.
   *
   * @param array $filters
   *   possible keys are status, local, chars, auth
   * @param bool $full
   *   TRUE to return the loaded Account objects
   * @return array
   *   CreditCommons\Account[] or string[]
   */
  function filter(array $filters = [], $full = FALSE) : array {
    // Pointless to try to make use of the $this->cached here.
    $path = 'filter';
    if ($full) {
      $path .= '/full';
    }
    // Ensure only known filters are passed
    $filters = array_intersect_key($filters, array_flip(['status', 'local', 'chars']));
    $filters['local'] = $filters['local']?'true':'false';
    $filters['status'] = $filters['status']?'true':'false';
    $this->options[RequestOptions::QUERY] = $filters;
    $results = (array)$this->localRequest($path);
    if ($full) {
      $results = array_map([$this, 'upcast'], $results);
    }
    return $results;
  }

  /**
   * Get an account object by name.
   *
   * @param string $name
   *   Need to be clear if this is the local name or a path
   * @param string $view_mode
   * @return stdClass|string
   *   The account object
   *
   * @todo rename this to load
   */
  function fetch(string $name) : Account {
    if (!isset($this->cached[$name])) {
      $path = urlencode($name);
      try{
        $result = $this->localRequest($path);
      }
      catch (\Exception $e) {
        if ($e->getCode() == 404) {
          // N.B. the name might have been deleted because of GDPR
          throw new DoesNotExistViolation(type: 'account', id: $name);
        }
        else {
          throw new CCFailure("AccountStore returned an invalid error code looking for $name: ".$e->getCode());
        }
      }
      $this->cached[$name] = $this->upcast($result);
    }
    return $this->cached[$name];
  }

  // Returns FALSE for the BoT account
  public function has($name, $node_class = '') : bool {
    try {
      $acc = $this->fetch($name);
    }
    catch (DoesNotExistViolation $e) {
      return FALSE;
    }
    if ($acc instanceOf BoT) {
      return FALSE;
    }
    if ($node_class) {
      $class = '\CCNode\Accounts\\'.$node_class;
      return $acc instanceOf $class;
    }
    return TRUE;
  }

  /**
   * {@inheritDoc}
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
        throw new CCFailure(message: $e->getMessage());
      }
      throw $e;
    }
    $contents = $response->getBody()->getContents();
    return json_decode($contents);
  }

  /**
   * Determine what Account class has been fetched and instantiate it.
   *
   * @global type $config
   * @param \stdClass $data
   * @return Account
   */
  private function upcast(\stdclass $data) : Account {
    global $config;
    $class = self::determineAccountClass($data, $config['bot']['acc_id']);
    $this->cached[$data->id] = $class::create($data);
    return $this->cached[$data->id];
  }

  /**
   * Determine the class of the given Account, considering this node's position
   * in the ledger tree.
   *
   * @param \stdClass $data
   * @param string $BoT_acc_id
   * @return string
   */
  private static function determineAccountClass(\stdClass $data, string $BoT_acc_id = '') : string {
    global $user;
    if (!empty($data->url)) {
      $upS = $data->id == $user->id;
      $BoT = $data->id == $BoT_acc_id;
      if ($BoT and $upS) {
        $class = 'UpstreamBoT';
      }
      elseif ($BoT and !$upS) {
        $class = 'DownstreamBoT';
      }
      elseif ($upS) {
        $class = 'UpstreamBranch';
      }
      else {
        $class = 'DownstreamBranch';
      }
    }
    else {
      $class = 'User';
    }
    return 'CCNode\Accounts\\'. $class;
  }


  static function anonAccount() {
    $obj = ['id' => '<anon>', 'max' => 0, 'min' => 0, 'status' => 1];
    return User::create((object)$obj);
  }


}
