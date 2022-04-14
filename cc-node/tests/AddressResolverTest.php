<?php

namespace CCNode\Tests;

use CCNode\AccountStore;
use CCNode\AddressResolver;
use CCNode\Accounts\Branch;
use CCNode\Accounts\User;
use CCNode\Accounts\BoT;
use CreditCommons\Exceptions\DoesNotExistViolation;

/**
 * Test for the AddressResolver Class
 */
class AddressResolverTest extends \PHPUnit\Framework\TestCase {

  public static function setUpBeforeClass(): void {
    global $config, $addressResolver, $user, $local_accounts, $branch_accounts, $trunkwards_account, $node_name;
    $config = parse_ini_file(__DIR__.'/../node.ini');
    $node_name = $config['node_name'];
    //require_once __DIR__.'/../src/AccountStore.php';
    $accountStore = AccountStore::create();
    // For now set the user to anon. There are no permissions checks but
    // sometimes the addressresolves depends on whether the user is the BoT
    // account or not.
    $user = $accountStore->anonAccount();
    // Unfortunately testing framework doesn't pass queryParams so we must filter here
    $all_accounts = $accountStore->filter([], TRUE);

    foreach ($all_accounts as $acc) {
      if ($acc instanceOf User) {
        $local_accounts[] = $acc->id;
        $user = $acc;
      }
      elseif($acc instanceOf Branch) {
        $branch_accounts[] = $acc->id;
      }
      elseif ($acc instanceof BoT) {
        $trunkwards_account = $acc;
      }
    }
    $addressResolver = new AddressResolver($accountStore);
  }

  function testLocalAccounts() {
    global $trunkwards_account, $local_accounts, $node_name;
    $acc_name = end($local_accounts);
    $this->oneTest($acc_name, $acc_name);
    $this->oneTest("$node_name/$acc_name", $acc_name);
    if ($trunkwards_account) {
      $this->oneTest("$trunkwards_account->id/$node_name/$acc_name", $acc_name);
      $this->oneTest("anything/$trunkwards_account->id/$node_name/$acc_name", $acc_name);
    }
  }

  function testBranchAccounts() {
    global $user, $local_accounts, $branch_accounts, $trunkwards_account, $node_name;
    if ($branch_accounts) {
      $branch_name = reset($branch_accounts);
      $this->oneTest("$branch_name/anything", $branch_name, 'anything');
      $this->oneTest("$branch_name/anything/anything", $branch_name, 'anything/anything');
      $this->oneTest("$node_name/$branch_name", $branch_name);
      if ($trunkwards_account) {
        $this->oneTest("$trunkwards_account->id/$node_name/$branch_name", $branch_name);
        $this->oneTest("anything/$trunkwards_account->id/$node_name/$branch_name/anything", $branch_name, 'anything');
      }
    }
    else {
      print "There were no branchward accounts to test with";
      $this->oneTest("anything", 'DoesNotExistViolation');
    }
  }

  function testTrunkwardAccounts() {
    // need to test as branch, node, and BoT
    global $user, $local_accounts, $branch_accounts, $trunkwards_account, $node_name;
    if ($trunkwards_account) {
      $trunkw_id = $trunkwards_account->id;
      // as anon
      // the trunkwards account cannot be directly addressed.
      $this->oneTest($trunkw_id, 'DoesNotExistViolation');
      $this->oneTest("anything", 'DoesNotExistViolation');
      $this->oneTest("anything/$trunkw_id", $trunkw_id, "anything/$trunkw_id");
      $this->oneTest("anything/anything/$trunkw_id", $trunkw_id, "anything/anything/$trunkw_id");
      $this->oneTest("$node_name/anything", 'DoesNotExistViolation');
      $this->oneTest("$node_name/$trunkw_id", 'DoesNotExistViolation');

      // as trunkwards account
      $user = $trunkwards_account;
      $this->oneTest("anything/anything/$trunkw_id", 'DoesNotExistViolation');
      $this->oneTest("anything", 'DoesNotExistViolation');
      $this->oneTest("$trunkw_id/$node_name/zzz", 'DoesNotExistViolation');
    }
    else {
      print "There was no trunkward account to test with";
      $this->oneTest("anything", 'DoesNotExistViolation');
      $this->oneTest("$node_name/anything", 'DoesNotExistViolation');
    }
  }


  function oneTest($given_name, $expected, $expected_path = '') {
    global $addressResolver;
    try {
      list($account, $relative_path) = $addressResolver->resolveToLocalAccountName($given_name);
    }
    catch(DoesNotExistViolation $e) {
      $this->assertEquals($expected, 'DoesNotExistViolation');
      return;
    }
    catch(\Exception $e) {
      print_r($e);
      return;
    }
    $this->assertEquals($expected, $account->id, 'Resolved name is wrong');
    $this->assertEquals($expected_path, $relative_path, 'Relative path is wrong');
  }

}
