<?php

namespace Examples;

use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Account;
use CreditCommons\AccountStoreInterface;
use Examples\AccountManager;
use Examples\Record;
use Examples\UserRecord;

/**
 * Handle requests & responses from the ledger to the DefaultAccountStore.
 * Stores all accounts in a json file at the web root.
 * @note this assumes the same classes as in CCNode
 */
class AccountStore implements AccountStoreInterface {

  private $accountManager;

  function __construct(
    public string $trunkwardAccName = ''
  ) {
    $filename = 'accountstore.json';
    if (!file_exists($filename)) {
      $filename = '../'.$filename;
    }
    $this->accountManager = new AccountManager($filename);
  }

  /**
   * @inheritDoc
   */
  function compareAuthkey(string $name, string $auth) : bool {
    return $this->accountManager[$name]->key == $auth;
  }

  /**
   * @inheritDoc
   */
  function filter(
    string $fragment = NULL,
    bool $local = NULL,
    bool $admin = NULL,
    int $limit = 10,
    int $offset = 0,
    bool $full = TRUE,
  ) : array {
    $all = $this->accountManager->accounts;

    if ($this->trunkwardAccName) {
      unset($this->accountManager->accounts[$this->trunkwardAccName]);
    }
    if (!empty($fragment)) {
      $this->accountManager->filterByName($fragment);
    }
    if (!is_null($local)) {
      $this->accountManager->filterByLocal($local);
    }
    if (!is_null($admin)) {
      $this->accountManager->filterByAdmin($admin);
    }
    $results = array_slice($this->accountManager->accounts, $offset, $limit);
    $this->accountManager->accounts = $all;
    if ($full) {
      // Upcast to CCNode local accounts
      return array_map([$this, 'upcast'], $results);
    }
    else {
      return array_keys($results);
    }
  }

  /**
   * @inheritDoc
   */
  function fetch(string $name, string $rel_path = '') : Account {
    if ($this->accountManager->has($name)) {
      $acc = $this->accountManager[$name];
      return $this->upcast($acc, $rel_path);
    }
    throw new DoesNotExistViolation(type: 'account', id: $name);
  }

  /**
   * Get the transaction limits for all accounts.
   * @return array
   */
  function allLimits() : array {
    $limits = [];
    foreach ($this->filter() as $info) {
      $limits[$info->id] = (object)['min' => $info->min, 'max' => $info->max];
    }
    return $limits;
  }

  /**
   * @inheritDoc
   */
  public function has(string $name) : bool {
    return isset($this->accountManager->accounts[$name]);
  }

  /**
   * {@inheritDoc}
   */
  function anonAccount() : Account {
    $obj = (object)['id' => '-anon-', 'max' => 0, 'min' => 0, 'key' => ''];
    $anon = new UserRecord($obj);
    return $this->upcast($anon);
  }

  /**
   * Convert the AccountStore accounts into CCnode accounts
   *
   * @param Record $record
   * @return Account
   */
  function upcast(Record $record, string $rel_path = '') : Account {
    global $cc_user, $cc_config;
    $class = 'CCNode\Accounts\\';
    if (!empty($record->url)) {
      $upS = $cc_user ? ($record->id == $cc_user->id) : TRUE;
      $trunkward = $record->id == $this->trunkwardAccName;
      if ($trunkward and $upS) {
        $class .= 'UpstreamTrunkward';
      }
      elseif ($trunkward and !$upS) {
        $class .= 'DownstreamTrunkward';
      }
      elseif ($upS) {
        $class .= 'UpstreamBranch';
      }
      else {
        $class .= 'DownstreamBranch';
      }
    }
    else {
      $class .= $record->admin ? 'Admin' : 'User';
    }
    $account = $class::create($record->asObj());

    // Make the rel path relative to this account.
    $parts = explode('/', $rel_path);
    if ($parts and $parts[0] == $account->id) {
      array_shift($parts);
    }
    if ($parts) {
      $account->relPath = implode('/', $parts);
    }
    return $account;
  }
}
