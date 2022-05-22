<?php

namespace CCNode\Tests;

/**
 * Tests the API functions of a node without touching remote nodes.
 * @todo Test transversal Errors.
 *   - IntermediateledgerViolation
 *   - HashMismatchFailure
 *   - UnavailableNodeFailure Is this testable? Maybe with an invalid url?
 *   - Try to trade directly with a Remote account.
 *
 * @todo Invalid paths currently return 404 which isn't in the spec.
 *
 */
class SingleNodeTest extends TestBase {

  const SLIM_PATH = 'slimapp.php';
  const API_FILE_PATH = 'vendor/credit-commons-software-stack/cc-php-lib/docs/credit-commons-openapi-3.0.yml';

  function __construct() {
    global $config;
    parent::__construct();
    $config = parse_ini_file('./node.ini');
    $config['dev_mode'] = 0;
    $this->getApp();//also loads slimapp.php
    $this->loadAccounts('AccountStore/');
  }

  function testEndpoints() {
    $options = $this->sendRequest('', 200, '', 'options');
    $this->assertObjectHasAttribute("permittedEndpoints", $options);
    $this->assertObjectNotHasAttribute("accountSummary", $options);
    $this->assertObjectNotHasAttribute("filterTransactions", $options);
    $options = $this->sendRequest('', 200, reset($this->normalAccIds), 'options');
    $this->assertObjectHasAttribute("filterTransactions", $options);
    $this->assertObjectHasAttribute("accountSummary", $options);

    $nodes = $this->sendRequest('handshake', 200, reset($this->normalAccIds));
  }

  function testBadLogin() {
    // wrong login name
    $exception = $this->sendRequest('absolutepath', 'DoesNotExistViolation', 'noname');
    $this->assertEquals('account', $exception->type);
    //wrong password
    $acc_id = reset($this->normalAccIds);
    $temp = $this->passwords[$acc_id];
    $this->passwords[$acc_id] = 'blah';
    $this->sendRequest('absolutepath', 'PasswordViolation', $acc_id);
    $this->passwords[$acc_id] = $temp;
  }

  function testAccountNames() {
    $chars = substr(reset($this->adminAccIds), 0, 2);
    $this->sendRequest("accounts/names/$chars", 'PermissionViolation');
    $results = $this->sendRequest("accounts/names/$chars", 200, reset($this->normalAccIds));
    // should be a list of account names including 'a'
    foreach ($results as $acc_id) {
      $this->assertStringContainsString($chars, $acc_id, "$acc_id should contain $chars");
    }
    if (count($results) > 1){
      $second_result = $this->sendRequest("accounts/names/$chars?limit=1", 200, reset($this->normalAccIds));
      $this->assertEquals(1, count($second_result));
    }
  }

  function testWorkflows() {
    // By default this is only accessible for authenticated users.
    $wfs = $this->sendRequest('workflows', 200, reset($this->normalAccIds));
    $this->assertNotEmpty($wfs);
  }

  function testBadTransactions() {
    $admin = reset($this->adminAccIds);
    $obj = (object)[
      'payee' => reset($this->normalAccIds),
      'payer' => reset($this->normalAccIds),
      'description' => 'test 3rdparty',
      'quant' => 1,
      'type' => '3rdparty',
      'metadata' => ['foo' => 'bar']
    ];
    $this->sendRequest('transaction', 'WrongAccountViolation', $admin, 'post', json_encode($obj));
    $obj->payee = 'aaaaaaaaaaa';
    $this->sendRequest('transaction', 'PathViolation', $admin, 'post', json_encode($obj));
    $obj->payee = reset($this->adminAccIds);
    $obj->quant = 999999999;
    $this->sendRequest('transaction', 'TransactionLimitViolation', $admin, 'post', json_encode($obj));
    $obj->quant = 0;
    $this->sendRequest('transaction', 'CCViolation', $admin, 'post', json_encode($obj));
    $obj->quant = 1;
    $obj->type = 'zzzzzz';
    $this->sendRequest('transaction', 'DoesNotExistViolation', $admin, 'post', json_encode($obj));
    $obj->type = 'disabled';// this is the name of one of the default workflows, which exists for this test
    $this->sendRequest('transaction', 'DoesNotExistViolation', $admin, 'post', json_encode($obj));
    $obj->type = '3rdparty';
    $this->sendRequest('transaction', 'PermissionViolation', '', 'post', json_encode($obj));
  }

  function test3rdParty() {
    $this->assertGreaterThan('1', count($this->normalAccIds));
    $admin = reset($this->adminAccIds);
    $payee = reset($this->normalAccIds);
    $payer = next($this->normalAccIds);
    // This assumes the default workflow is unmodified.
    $obj = [
      'payee' => $payee,
      'payer' => $payer,
      'description' => 'test 3rdparty',
      'quant' => 1,
      'type' => '3rdparty',
      'metadata' => ['foo' => 'bar']
    ];
    $this->sendRequest('transaction', 'PermissionViolation', '', 'post', json_encode($obj));
    // Default 3rdParty workflow saves transactions immemdiately in completed state.
    $transaction = $this->sendRequest('transaction', 201, $admin, 'post', json_encode($obj));
    // Check the transaction is written
    $this->assertNotNull($transaction->uuid);
    $this->assertEquals($payee, $transaction->entries[0]->payee);
    $this->assertEquals($payer, $transaction->entries[0]->payer);
    $this->assertEquals('test 3rdparty', $transaction->entries[0]->description);
    $this->assertEquals('3rdparty', $transaction->type);
    $this->assertEquals('completed', $transaction->state);
    $this->assertEquals('1', $transaction->version);
    $this->assertIsObject($transaction->entries[0]->metadata);
    // easier to work with arrays
    $this->assertEquals(['foo' => 'bar'], (array)$transaction->entries[0]->metadata);

    // try to retrieve a transaction that doesn't exist.
    $error = $this->sendRequest('transaction/ada5b4f0-33a8-4807-90c7-3aa56ae1c741', 'DoesNotExistViolation', $admin);
    $this->assertEquals('transaction', $error->type);

    $this->sendRequest('transaction/'.$transaction->uuid.'', 200, $admin);
  }

  function testTransactionLifecycle() {
    $admin = reset($this->adminAccIds);
    $payer = reset($this->normalAccIds);
    $payee = next($this->normalAccIds);
    if (!$payer) {
      print "Skipped testTransactionLifecycle. More than one non-admin user required";
      return;
    }
    // Check the balances first
    $init_summary = $this->sendRequest("account/summary/$payee", 200, $payee);
    $transaction_description = 'test bill';
    $obj = (object)[
      'payee' => $payer,
      'payer' => $payee,
      'description' => $transaction_description,
      'quant' => 10,
      'type' => 'bill',
      'metadata' => ['foo' => 'bar']
    ];
    // the payer and payee are the wrong way around.
    $this->sendRequest('transaction', 'WorkflowViolation', $payee, 'post', json_encode($obj));
    $obj->payee = $payee;
    $obj->payer = $payer;
    // 'bill' transactions must be approved, and enter pending state.
    $transaction = $this->sendRequest('transaction', 200, $payee, 'post', json_encode($obj));
    $this->assertNotEmpty($transaction->transitions);
    $this->assertContains('pending', $transaction->transitions);
    $this->assertEquals("validated", $transaction->state);
    $this->assertEquals('0', $transaction->version);
    // check that nobody else can see this transaction
    $this->sendRequest("transaction/$transaction->uuid", 'CCViolation', $payer);
    $this->sendRequest("transaction/$transaction->uuid", 200, $admin);

    // write the transaction
    $this->sendRequest("transaction/$transaction->uuid/pending", 'PermissionViolation', '', 'patch', json_encode($obj));
    $this->sendRequest("transaction/$transaction->uuid/pending", 201, $payee, 'patch');

    $pending_summary = $this->sendRequest("account/summary/$payee", 200, $payee);
    // Get the amount of the transaction, including fees.
    list($income, $expenditure) = $this->transactionDiff($transaction, $payee);
    $this->assertEquals(
      $pending_summary->pending->balance,
      $init_summary->pending->balance + $income - $expenditure
    );
    $this->assertEquals(
      $pending_summary->pending->volume,
      $init_summary->pending->volume + $income + $expenditure
    );
    $this->assertEquals(
      $pending_summary->pending->gross_in,
      $init_summary->pending->gross_in + $income
    );
    $this->assertEquals(
      $pending_summary->pending->gross_out,
      $init_summary->pending->gross_out + $expenditure
    );
    $this->assertEquals(
      $pending_summary->pending->trades,
      $init_summary->pending->trades + 1
    );
    $this->assertEquals(
      $pending_summary->completed->balance,
      $init_summary->completed->balance
    );
    // We can't easily test partners unless we clear the db first.
    // Admin confirms the transaction
    $this->sendRequest("transaction/$transaction->uuid/completed", 201, $admin, 'patch');
    $completed_summary = $this->sendRequest("account/summary/$payee", 200, $payee);
    $this->assertEquals(
      $completed_summary->completed->balance,
      $init_summary->completed->balance + $income - $expenditure
    );
    $this->assertEquals(
      $completed_summary->completed->volume,
      $init_summary->completed->volume + $income + $expenditure
    );
    $this->assertEquals(
      $completed_summary->completed->gross_in,
      $init_summary->completed->gross_in + $income
    );
    $this->assertEquals(
      $completed_summary->completed->gross_out,
      $init_summary->completed->gross_out + $expenditure
    );
    $this->assertEquals(
      $completed_summary->completed->trades,
      $init_summary->completed->trades +1
    );

    // Filtering.
    $norm_user = reset($this->normalAccIds);
    $this->sendRequest("transactions", 'PermissionViolation', '');
    $results = $this->sendRequest("transactions", 200, $norm_user);
    $first_uuid = reset($results)->uuid;
    $this->sendRequest("transaction/$first_uuid", 'PermissionViolation', '');
    $this->sendRequest("transaction/$first_uuid", 200, $norm_user);
    $this->sendRequest("transaction/$first_uuid?entries=true", 200, $norm_user);
    $results = $this->sendRequest("transactions?entries=true&description=$transaction_description", 200, $norm_user);
    // test that every result entry contains the string
    $counts = [];
    foreach ($results as $standaloneEntiry) {
      $counts[] = strpos($standaloneEntiry->description, $transaction_description);
    }
    $this->assertContainsOnly('int', $counts, TRUE, 'Transaction did not filter by description ');
    $all_entries = $this->sendRequest("transactions?entries=true", 200, $norm_user);
    if (count($all_entries) < 3) {
      echo 'Unable to test offset?pager=0,3 with only '.count($all_entries). 'entries saved.';
    }
    else {
      $limited = $this->sendRequest("transactions?entries=true&pager=0,3", 200, $norm_user);
      $this->assertEquals(3, count($limited), "Pager failed to return 3 entries");
    }
    $limited = $this->sendRequest("transactions?entries=true&pager=1,1", 200, $norm_user);
    $this->assertEquals(array_slice($all_entries, 1, 1), $limited, "The offset/limit queryparams don't work");

    // test the sort
    $results = $this->sendRequest("transactions?states=erased,complete", 200, $norm_user);
    $err = FALSE;
    foreach ($results as $result) {
      if (!in_array($result->state, ['erased', 'completed'])) {
        $err = TRUE;
      }
    }
    $this->assertEquals(FALSE, $err, 'Failed to filter by 2 states.');

    $results = $this->sendRequest("transactions?involving=$payee", 200, $norm_user);
    foreach ($results as $res) {
      if (strpos($res->entries[0]->payer, $payee) !== FALSE and strpos($res->entries[0]->payee, $payee) !== FALSE) {
        $err = TRUE;
      }
    }
    $this->assertEquals(FALSE, $err, "Failed to filter by transactions involving $payee");
    // Erase and check that stats are updated.
    $this->sendRequest("transaction/$transaction->uuid/erased", 201, $admin, 'patch');
    $erased_summary = $this->sendRequest("account/summary/$payee", 200, $payee);
    $this->assertEquals($erased_summary, $init_summary);
  }

  function testAccountSummaries() {
    $user1 = reset($this->normalAccIds);
    //  Currently there is no per-user access control around limits visibility.
    $limits = $this->sendRequest("account/limits/$user1", 200, $user1);
    $this->assertlessThan(0, $limits->min, "Minimum account limit was not less than zero.");
    $this->assertGreaterThan(0, $limits->max, "Maximum account limit was not greater than zero.");
    // account/summary/{acc_id} is already tested
    // OpenAPI doesn't allow optional parameters
    $this->sendRequest("account/summary", 'PermissionViolation', '');
    $this->sendRequest("account/summary", 200, $user1);
  }

  private function checkTransactions(array $all_transactions, array $filtered_uuids, array $conditions) {
    foreach ($all_transactions as $t) {
      $pass = FALSE;
      foreach ($conditions as $key => $value) {
        $pass = $t->{$key} == $value;
        if (!$pass) {
          break;
        }
      }
      if ($pass) {
        $uuids[] = $t->uuid;
      }
    }
    $this->assertEquals($uuids, $filtered_uuids);
  }

  private function transactionDiff($transaction, string $payee) : array {
    $income = $expenditure = 0;
    foreach ($transaction->entries as $e) {
      if ($e->payee == $payee) $income += $e->quant;
      elseif ($e->payer == $payee) $expenditure += $e->quant;
    }
    return [$income, $expenditure];
  }
}
