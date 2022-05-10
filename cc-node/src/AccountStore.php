<?php

namespace CCNode;

use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Requester;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use CreditCommons\Account;

/**
 * Handle requests & responses from the ledger to the accountStore.
 */
class AccountStore extends Requester {

  function __construct($base_url) {
    parent::__construct($base_url);
    $this->options[RequestOptions::HEADERS]['Accept'] = 'application/json';
  }

  // Instantiate this object by calling AccountStore();
  function __invoke() {
    return new static();
  }


  /**
   * Filter on the account names.
   *
   * @param array $filters
   *   possible keys are status, local, chars, key
   * @param bool $full
   *   TRUE to return the loaded Account objects
   * @return array
   *   CreditCommons\Account[] or string[]
   */
  function filter(array $filters = [], $full = FALSE) : array {
    $path = 'filter';
    if ($full) {
      $path .= '/full';
    }
    // Ensure only known filters are passed
    $filters = array_intersect_key($filters, array_flip(['status', 'local', 'chars', 'auth']));
    $this->options[RequestOptions::QUERY] = $filters;
    $results = (array)$this->localRequest($path);
    if ($full) {
      array_walk($results, [$this, 'upcast']);
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
    return $this->upcast($result);
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
   * Resolve to an account on the current node.
   * @return Account
   * @param bool $existing
   *   TRUE if we know the account exists. Then unknown accounts either resolve
   *   to the BoT account or throw an exception
   * @todo why is this only used while making transactions?
   */
  public function resolveAddressToLocal(string $given_path, bool $existing) : Account {
    global $orientation, $config;
    $parts = explode('/', $given_path);
    // If it is one path item long.
    if (count($parts) == 1) {
      // If it exists on this node.
      if ($pol = $this->fetch($given_path)) {
        return $pol;
      }
      throw new AccountResolutionViolation(path: $given_path);
    }

    // A branchwards account, including the local node name
    $pos = array_search($config['node_name'], $parts);
    if ($pos !== FALSE and $branch_name = $parts[$pos+1]) {
      try {
        return $this->fetch($branch_name);
      }
      catch (DoesNotExistViolation $e) {}
    }
    // A branchwards or trunkwards account, starting with the account name on the local node
    $branch_name = reset($parts);
    try {
      return $this->fetch($branch_name);
    }
    catch (DoesNotExistViolation $e) {}

    // Now the path is either trunkwards, or invalid.
    if ($config['bot']['acc_id']) {
      // Don't have to 'try' because this account is known to exist.
      $trunkwardsAccount = $this->fetch($config['bot']['acc_id']);
      if ($existing) {
        return $trunkwardsAccount;
      }
      if ($orientation->isUpstreamBranch()) {
        return $trunkwardsAccount;
      }
    }
    throw new AccountResolutionViolation(type: 'account', id: $given_path);
  }

  /**
   * Determine what Account class has been fetched and instantiate it.
   *
   * @global type $orientation
   * @global type $config
   * @param \stdClass $data
   * @return Account
   */
  private function upcast(\stdClass $data) : Account {
    global $orientation, $config;
    $class = self::determineAccountClass(
      $data->id,
      $data->url??'',
      isset($orientation->upstreamAccount) ? $orientation->upstreamAccount->id : '',
      $config['bot']['acc_id']
    );
    return $class::create($data);
  }

  /**
   * Determine the class of the given Account, considering this node's position
   * in the ledger tree.
   *
   * @param string $acc_id
   * @param string $account_url
   * @param string $upstream_acc_id
   * @param string $BoT_acc_id
   * @return string
   */
  private static function determineAccountClass(string $acc_id, string $account_url = '', string $upstream_acc_id = '', string $BoT_acc_id = '') : string {
    if ($account_url) {
      $BoT = $acc_id == $BoT_acc_id;
      $upS = $acc_id == $upstream_acc_id;
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

}
