<?php

namespace CCServer\Tests;

/**
 * Tests the API functions of a node without touching remote nodes.
 * @todo Test transversal Errors.
 *   - HashMismatchFailure
 *   - UnavailableNodeFailure Is this testable? Maybe with an invalid url?
 *
 * @todo Slim returns 404 for invalid paths but needs to be changed to 400 CCOtherViolation
 *
 */
class MultiNodeTest extends SingleNodeTest {

  function __construct() {
    global $local_accounts, $foreign_accounts_grouped, $foreign_accounts, $remote_accounts, $cc_config;
    parent::__construct();
    $this->truncate('credcom_leaf');
    $this->truncate('credcom_branch1');
    $this->truncate('credcom_branch2');
    $this->truncate('credcom_trunk');
    file_put_contents('ccleaf.debug', '');
    file_put_contents('../cc-branch1/ccbranch1.debug', '');
    file_put_contents('../cc-trunk/cctrunk.debug', '');
    $this->nodePath = explode('/', $cc_config->absPath);

    if (!$local_accounts) {
      //because __construct is called many times.
      $local_user = reset($this->normalAccIds);
      // Find all the accounts we can in what is presumably a limited testing tree and group them by node
      $local_and_trunkward = $this->sendRequest("account/names?limit=50", 200, $local_user)->data;
      foreach ($local_and_trunkward as $path_name) {
        if (strpos($path_name, '/')) {
          $all_accounts[] = $path_name;//local
        }
        else {//remote
          $this->getLeafwardAccounts($path_name, $all_accounts);
          $remote_accounts[] = $path_name;
        }
      }
      // Group the accounts
      foreach ($all_accounts as $path) {
        if (substr($path, -1) == '/')continue; // don't include connector accounts.
        if ($pos = intval(strrpos($path, '/'))) {
          $node_path = substr($path, 0, $pos);
          $foreign_accounts_grouped[$node_path][] = $path;
          $foreign_accounts[] = $path;
        }
        else {
          $local_accounts[] = $path;
        }
      }
      shuffle($foreign_accounts);
    }
  }

  private function getLeafwardAccounts($acc_path, &$all_accounts) {
    $local_user = reset($this->normalAccIds);
    $results = $this->sendRequest("account/names?acc_path=$acc_path", 200, $local_user);
    foreach ($results->data as $result) {
      if (substr($result, -1) <> '/') {
        $all_accounts[] = $result;
      }
      elseif (count(explode('/', $result)) < 3) {// Coz of OpenAPI optional arguments workaround
        $this->getLeafwardAccounts ($result, $all_accounts);
      }
    }
    $all_accounts = array_unique($all_accounts);
    // @todo test the bool 'local' queryparam
  }

  function testHandshake() {
    $this->sendRequest('handshake', 200, reset($this->adminAccIds));
  }

  function testWorkflows() {
    parent::testWorkflows();
    // should find a way of testing that the inherited workflows combine properly.
  }

  function testConversion() {
    global $foreign_accounts_grouped;
    // so just test the API
    foreach ($foreign_accounts_grouped as $node_path => $accounts) {
      $this->sendRequest("about?node_path=$node_path", 200);
      // Can't tell from here what the conversion rate should be in any given setup.
    }
  }

  function testBadTransactions() {
    parent::testBadTransactions();
    global $local_accounts, $foreign_accounts_grouped, $remote_accounts;
    $admin = reset($this->adminAccIds);
    $obj = [
      'description' => 'test 3rdparty bad',
      'quant' => 2,
      'type' => '3rdparty',
      'metadata' => ['foo' => 'bar']
    ];
    $foreign_node = reset($foreign_accounts_grouped);
    // Try to trade with two foreign accounts
    $obj['payer'] = reset($foreign_node);
    $obj['payee'] = end($foreign_node);
    $this->sendRequest('transaction', 'SameAccountViolation', $admin, 'post', json_encode($obj));

    // Try to trade with a mirror account.
    $obj['payee'] = reset($local_accounts);
    $obj['payer'] = reset($remote_accounts);
    $this->sendRequest('transaction', 'SameAccountViolation', $admin, 'post', json_encode($obj));
  }

  function test3rdParty() {
    parent::test3rdParty();
    global $local_accounts, $foreign_accounts_grouped, $remote_accounts;
    $admin = reset($this->adminAccIds);
    $foreign_node = reset($foreign_accounts_grouped);

    $obj = (object)[
      'payee' => end($foreign_node),
      'payer' => reset($foreign_node),
      'description' => 'test 3rdparty good',
      'quant' => 3,
      'type' => '3rdparty',
      'metadata' => ['foo' => 'bar']
    ];
    // test that admin can't even do a transaction between two foreign accounts
    $this->sendRequest('transaction', 'SameAccountViolation', $admin, 'post', json_encode($obj));
    $obj->payee = reset($foreign_node);
    $obj->payer = reset($local_accounts);
    $this->sendRequest('transaction', 201, $admin, 'post', json_encode($obj));

    // Test again for good measure.
    $foreign_node = end($foreign_accounts_grouped);
    $obj->payee = end($foreign_node);
    $obj->payer = end($local_accounts);
    $obj->description = 'test 3rdparty good again';
    $this->sendRequest('transaction', 201, $admin, 'post', json_encode($obj));
  }

  // Ensure that transactions passing accross this ledger but not involving leaf
  // accounts can't be manipulated.
  // @todo Find a way to filter for these purely transversal transactions.
  function _testImmutableTransversal() {
    global $remote_accounts;
    $admin = reset($this->adminAccIds);
    foreach ($remote_accounts as $acc_id) {
      $transversal = $this->sendRequest("transaction?", '200', $admin);
    }
  }

  function testTransactionLifecycle() {
    global $local_accounts, $foreign_accounts_grouped, $foreign_accounts, $remote_accounts;
    parent::testTransactionLifecycle();
    $admin = reset($this->adminAccIds);
    $obj = (object)[
      'payer' => end($local_accounts),
      'payee' => $foreign_accounts[array_rand($foreign_accounts)],
      'payee' => 'cctrunk/alice',
      'description' => 'test transversal bill ',
      'quant' => 4,
      'type' => 'credit',
      'metadata' => ['foo' => 'bar']
    ];
    $tx = $this->sendRequest('transaction', 200, $obj->payer, 'post', json_encode($obj))->data;
    $this->sendRequest("transaction/$tx->uuid/pending", 201, $obj->payer, 'patch');
  }

  function testRemoteRetrievals() {
    parent::testAccountSummaries();
    global $local_accounts, $foreign_accounts_grouped, $foreign_accounts, $remote_accounts;
    $user1 = reset($this->normalAccIds);
    // get all the account details from each node.
    // Note that these paths are both optional, and leafward nodes may also conceal them.
    foreach ($foreign_accounts_grouped as $node_path => $accounts) {
      $this->sendRequest("account/summary?acc_path=$node_path/", 200, $user1);
      $this->sendRequest("account/limits?acc_path=$node_path/", 200, $user1);
      $this->sendRequest("account/names?acc_path=$node_path/", 200, $user1);
    }

    // test 3 random addresses (with not more than one slash in)
    $i=0;
    while ($i < 3) {
      $rel_acc_path = next($foreign_accounts);
      $this->sendRequest("account/summary?acc_path=$rel_acc_path", 200, $user1);
      $this->sendRequest("account/limits?acc_path=$rel_acc_path", 200, $user1);
      // This won't work because its not in the API.
      // $this->sendRequest("account?acc_path=history/$rel_acc_path", 200, $user1);
      $i++;
    }
  }

  function testTrunkwards() {
    global $cc_config;
    if (empty($this->trunkwardId)) {
      $this->assertEquals(1, 1);
      return;
    }
    $this->sendRequest("absolutepath", 'PermissionViolation', '');
    $nodes = $this->sendRequest("absolutepath", 200, reset($this->normalAccIds));
    $this->assertGreaterThan(1, count($nodes), 'Absolute path did not return more than one node: '.reset($nodes));
    $this->assertEquals($cc_config->nodeName, end($nodes), 'Absolute path does not end with the current node.');
  }

}
