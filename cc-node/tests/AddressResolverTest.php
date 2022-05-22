<?php

namespace CCNode\Tests;

use CCNode\AddressResolver;
use CCNode\Accounts\Branch;
use CreditCommons\Exceptions\PathViolation;

/**
 * Test for the AddressResolver Class
 */
class AddressResolverTest extends \PHPUnit\Framework\TestCase {

  public static function setUpBeforeClass(): void {
    global $config, $addressResolver, $user, $local_accounts, $branch_accounts, $trunkward_account_id, $node_name;

    require_once __DIR__.'/../slimapp.php';
    $node_name = \CCNode\getConfig('node_name');
    $trunkward_acc_id = \CCNode\getConfig('trunkward_acc_id');
    $accountStore = \CCNode\accountStore();
    // For now set the user to anon. There are no permissions checks but
    // sometimes the addressresolves depends on whether the user is the Trunkward
    // account or not.
    $user = $accountStore->anonAccount();
    // Unfortunately testing framework doesn't pass queryParams so we must filter here
    $all_accounts = $accountStore->filterFull();
    foreach ($all_accounts as $acc) {
      if($acc instanceOf Branch) {
        $branch_accounts[] = $acc->id;
      }
      else {
        $local_accounts[] = $acc->id;
        $user = $acc;
      }
    }
    $addressResolver = new AddressResolver($accountStore, \CCNode\getConfig('abs_path'));
  }

  // test pathnames to actual accounts
  function testLocalOrRemoteAcc() {
    global $addressResolver, $trunkward_account_id, $local_accounts, $node_name;
    // test different kinds of path with each function.
    $local_account = reset($local_accounts);
    $acc = $addressResolver->localOrRemoteAcc($local_account);
    $this->assertInstanceOf('\CCNode\Accounts\User', $acc);
    $this->assertEquals('', $local_account);

    $local_account = $node_name .'/'. reset($local_accounts);
    $acc = $addressResolver->localOrRemoteAcc($local_account);
    $this->assertInstanceOf('\CCNode\Accounts\User', $acc);
    $this->assertEquals($local_account, '');

    $local_account = $trunkward_account_id .'/'. $node_name .'/'. reset($local_accounts);
    $acc = $addressResolver->localOrRemoteAcc($local_account);
    $this->assertInstanceOf('\CCNode\Accounts\User', $acc);
    $this->assertEquals($local_account, '');

    $local_account = $trunkward_account_id .'/'. $node_name .'/'. reset($local_accounts) . '/'.'blah/blah';
    $acc = $addressResolver->localOrRemoteAcc($local_account);
    $this->assertInstanceOf('\CCNode\Accounts\User', $acc);
    $this->assertEquals($local_account, 'blah/blah');

    $unknown_account = 'blah';
    $acc = $addressResolver->localOrRemoteAcc($unknown_account);
    $this->assertInstanceOf('\CCNode\Accounts\Trunkward', $acc);
    $this->assertEquals('blah', $acc->relPath());
    $this->assertEquals($unknown_account, $unknown_account);// unchanged

    $unknown_account = 'blah' .'/'. reset($local_accounts) . '/'.'blah/blah';
    $acc = $addressResolver->localOrRemoteAcc($unknown_account);
    $this->assertInstanceOf('\CCNode\Accounts\Trunkward', $acc);
    $this->assertEquals($unknown_account, $unknown_account);// unchanged

    $bad_account = reset($local_accounts) . '/';
    try {
      $addressResolver->localOrRemoteAcc($bad_account);
    }
    catch (PathViolation $e) {
      $this->assertInstanceOf('\CreditCommons\Exceptions\PathViolation', $e);
    }
  }

  // Test local or remote fragments for autocomplete.
  function testNodeAndFragment() {
    global $addressResolver, $trunkward_account_id, $local_accounts, $branch_accounts;

    $fragment = 'a';
    $acc = $addressResolver->nodeAndFragment($fragment);
    $this->assertNull($acc);
    $this->assertEquals($fragment, $fragment);// unchanged

    $local_name = reset($local_accounts);
    $acc = $addressResolver->nodeAndFragment($local_name);
    $this->assertNull($acc);
    $this->assertEquals($local_name, $local_name);// unchanged

    if ($branch_accounts) {
      $branch_account = reset($branch_accounts);
      $acc = $addressResolver->nodeAndFragment($branch_account);
      $this->assertInstanceOf('\CCNode\Accounts\Remote', $acc);
      $this->assertEquals($branch_account, '');

      $branch_path = reset($branch_accounts) .'/';
      $acc = $addressResolver->nodeAndFragment($branch_path);
      $this->assertInstanceOf('\CCNode\Accounts\Remote', $acc);
      $this->assertEquals($branch_path, '/');
    }

    if ($trunkward_account_id) {
      $foreign_node = $trunkward_account_id .'/';
      $acc = $addressResolver->nodeAndFragment($foreign_node);
      $this->assertInstanceOf('\CCNode\Accounts\Trunkward', $acc);
    }

    $foreign_node = 'blah';
    $acc = $addressResolver->nodeAndFragment($foreign_node);
    $this->assertNull($acc);
    $this->assertEquals($foreign_node, $foreign_node);

    $foreign_node = 'blah/a';
    $acc = $addressResolver->nodeAndFragment($foreign_node);
    $this->assertInstanceOf('\CCNode\Accounts\Trunkward', $acc);
    $this->assertEquals($foreign_node, $foreign_node);
  }

  // seems superfluous
  function _testNearestNode() {
    global $addressResolver, $trunkward_account_id, $local_accounts, $branch_accounts;
  }

  function testRemoteNode() {
    global $addressResolver, $trunkward_account_id, $local_accounts, $branch_accounts;

    $local_acc = reset($local_accounts);
    try {
      $acc = $addressResolver->remoteNode($local_acc);
      $this->assertEquals(1, 0);
    }
    catch (PathViolation $e) {
      $this->assertInstanceOf('\CreditCommons\Exceptions\PathViolation', $e);
    }

    if ($branch_accounts) {
      $branch_node = reset($branch_accounts) .'/';
      $acc = $addressResolver->remoteNode($branch_node);
      $this->assertInstanceOf('\CCNode\Accounts\Remote', $acc);

      $remote_node = reset($branch_accounts) .'/blah';
      $acc = $addressResolver->remoteNode($remote_node);
      $this->assertInstanceOf('\CCNode\Accounts\Trunkward', $acc);
      $this->assertEquals($remote_node, $remote_node);// unchanged
    }

    $remote_node = 'blah';
    $acc = $addressResolver->remoteNode($remote_node);
    $this->assertInstanceOf('\CCNode\Accounts\Trunkward', $acc);
    $this->assertEquals($remote_node, $remote_node);// unchanged
  }

}
