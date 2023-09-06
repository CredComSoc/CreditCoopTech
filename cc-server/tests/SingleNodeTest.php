<?php

namespace CCServer\Tests;

/**
 * Tests the API functions of a node without touching remote nodes.
 * @todo Invalid paths currently return 404 which isn't in the spec.
 */
class SingleNodeTest extends TestBase {

  const SLIM_PATH = 'slimapp.php';
  //const API_FILE_PATH = 'vendor/credit-commons/cc-php-lib/docs/credit-commons-openapi-3.0.yml';
  const API_FILE_PATH = 'vendor/credit-commons/cc-php-lib/docs/credit-commons-v0.2.openapi3.yml';

  function __construct() {
    global $cc_config;
    parent::__construct();
    $cc_config = new \CCNode\ConfigFromIni(parse_ini_file('node.ini'));
    // Clear the database for (single node test only).
    if ($cc_config->devMode and get_called_class() == get_class()) {
      $this->truncate();
    }
    $cc_config->devMode = 1;
    $this->loadAccounts();
  }

  function testEndpoints() {
    $result = (array)$this->sendRequest('', 200, '', 'options')->data;
    $this->assertArrayHasKey("permittedEndpoints", $result);
    $this->assertArrayNotHasKey("accountSummary", $result);
    $this->assertArrayNotHasKey("filterTransactions", $result);
    $result = (array)$this->sendRequest('', 200, reset($this->normalAccIds), 'options')->data;
    $this->assertArrayHasKey("filterTransactions", $result);
    $this->assertArrayHasKey("accountSummary", $result);
    $result = $this->sendRequest('handshake', 200, reset($this->normalAccIds));
  }

  function testBadLogin() {
    // Wrong login name
    $error = $this->sendRequest('absolutepath', 'DoesNotExistViolation', 'noname');
    $this->assertEquals('account', $error->type);
    // Wrong password
    $acc_id = reset($this->normalAccIds);
    $temp = $this->passwords[$acc_id];
    $this->passwords[$acc_id] = 'blah';
    $this->sendRequest('absolutepath', 'PasswordViolation', $acc_id);
    $this->passwords[$acc_id] = $temp;
  }

  function testAccountNames() {
    $chars = substr(reset($this->adminAccIds), 0, 2);
    $results = $this->sendRequest("account/names?acc_path=$chars", 200, reset($this->normalAccIds));
    // Should be a list of account names including 'a'
    foreach ($results->data as $acc_id) {
      $this->assertStringContainsString($chars, $acc_id);
    }
    if (count($results->data) > 1){
      $second_result = $this->sendRequest("account/names?acc_path=$chars&limit=1", 200, reset($this->normalAccIds));
      $this->assertEquals(1, count($second_result->data));
    }
  }

  function testWorkflows() {
    // By default this is only accessible for authenticated users.
    $wfs = $this->sendRequest('workflows', 200, reset($this->normalAccIds));
    $this->assertNotEmpty($wfs);
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
    $result = $this->sendRequest('transaction', 201, $admin, 'post', json_encode($obj));
    $transaction = $result->data;
    // Check the transaction is written
    $this->assertNotNull($transaction->uuid);
    $this->assertEquals($payee, $transaction->entries[0]->payee);
    $this->assertEquals($payer, $transaction->entries[0]->payer);
    $this->assertEquals('test 3rdparty', $transaction->entries[0]->description);
    $this->assertEquals('3rdparty', $transaction->type);
    $this->assertEquals('completed', $transaction->state);
    $this->assertEquals('1', $transaction->version);
    $this->assertIsObject($transaction->entries[0]->metadata);
    // Easier to work with arrays
    $this->assertEquals(['foo' => 'bar'], (array)$transaction->entries[0]->metadata);

    // Try to retrieve a transaction that doesn't exist.
    $error = $this->sendRequest('transaction/ada5b4f0-33a8-4807-90c7-3aa56ae1c741', 'DoesNotExistViolation', $admin);
    $this->assertEquals('transaction', $error->type);
    $this->assertEquals('ada5b4f0-33a8-4807-90c7-3aa56ae1c741', $error->id);
    // Try to retrieve a transaction that does exist.
    $this->sendRequest('transaction/'.$transaction->uuid.'', 200, $admin);
  }

  function testBadTransactions() {
    global $cc_config;
    $admin = reset($this->adminAccIds);
    $obj = (object)[
      'payee' => reset($this->normalAccIds),
      'payer' => reset($this->normalAccIds),
      'description' => 'test 3rdparty',
      'quant' => 3,
      'type' => '3rdparty',
      'metadata' => ['foo' => 'bar']
    ];
    $obj->description = 'Two accounts the same';
    $this->sendRequest('transaction', 'SameAccountViolation', $admin, 'post', json_encode($obj));
    $obj->description = 'Missing party';
    unset($obj->payee);
    $this->sendRequest('transaction', 'InvalidFieldsViolation', $admin, 'post', json_encode($obj));
    // Nonexisting account name
    $obj->payee = 'aaaaaaaaaaa';
    $obj->description = 'Invalid party';
    $this->sendRequest('transaction', 'PathViolation', $admin, 'post', json_encode($obj));
    $obj->payee = reset($this->adminAccIds);
    // Only admins can send 3rd party transactions
    $this->sendRequest('transaction', 'PermissionViolation', '', 'post', json_encode($obj));
    $obj->description = 'Too huge quantity';
    $obj->quant = 9999999;
    $this->sendRequest('transaction', 'TransactionLimitViolation', $admin, 'post', json_encode($obj));
    if (!$cc_config->zeroPayments) {
      $obj->quant = 0;
      $obj->description = 'Zero quantity';
      $this->sendRequest('transaction', 'CCViolation', $admin, 'post', json_encode($obj));
    }
    $obj->quant = 1;
    $obj->description = 'Unknown transaction type';
    $obj->type = 'zzzzzz';
    $this->sendRequest('transaction', 'DoesNotExistViolation', $admin, 'post', json_encode($obj));
    $obj->description = 'Deprecated transaction type';
    $obj->type = 'disabled'; // This is the name of one of the default workflows, which exists for this test
    $this->sendRequest('transaction', 'DeprecatedWorkflowViolation', $admin, 'post', json_encode($obj));
    $obj->type = 'bill';
    unset($obj->payer);
    $obj->description = 'A bill must be sent by the payee';
    $this->sendRequest('transaction', 'InvalidFieldsViolation', end($this->normalAccIds), 'post', json_encode($obj));
  }

  function testTransactionLifecycle() {
    $results = $this->sendRequest("transactions", 200, reset($this->normalAccIds));
    $this->assertEquals(count($results->data), $results->meta->number_of_results);
    if (count($results->data) < 2) {
      $this->makeValidTransaction();
    }
    $admin = reset($this->adminAccIds);
    $payer = reset($this->normalAccIds);
    $payee = next($this->normalAccIds);
    if (!$payer) {
      print "Skipped testTransactionLifecycle. More than one non-admin user required";
      return;
    }

    $init_summary = $this->sendRequest("account/summary?acc_path=$payee", 200, $payee)->data->{$payee};
    // Check the balances first
    $transaction_description = 'test bill';
    $obj = (object)[
      'payer' => $payer,
      'description' => $transaction_description,
      'quant' => 5,
      'type' => 'bill',
      'metadata' => ['foo' => 'bar']
    ];
    // 'bill' transactions must be approved, and enter pending state.
    $result = $this->sendRequest('transaction', 200, $payee, 'post', json_encode($obj));
    $transaction = $result->data;
    $this->assertNotEmpty($result->meta->transitions);
    $this->assertNotEmpty('pending', $result->meta->transitions->pending);
    $this->assertEquals("validated", $transaction->state);
    $this->assertEquals('0', $transaction->version);
    // Check that only the creator ($payee) can see this transaction at version 0 state 'validated'
    $this->sendRequest("transaction/$transaction->uuid", 'PermissionViolation', $payer, 'get', 'Payer should NOT be able to see unconfirmed credit transaction');
    $this->sendRequest("transaction/$transaction->uuid?entries=true", 'PermissionViolation', $payer, 'get', 'Payer should NOT be able to see unconfirmed credit transaction entries');
    $this->sendRequest("transaction/$transaction->uuid", 200, $payee, 'get', 'Payee cannot see own unvalidated transaction');
    $this->sendRequest("transaction/$transaction->uuid?entries=true", 200, $payee, 'get', 'Payee cannot see own unvalidated transaction entries');
    $this->sendRequest("transaction/$transaction->uuid", 200, $admin);
    $this->sendRequest("transaction/$transaction->uuid?entries=true", 200, $admin);

    // Write the transaction
    $this->sendRequest("transaction/$transaction->uuid/pending", 201, $payee, 'patch');



    $pending_summary = $this->sendRequest("account/summary?acc_path=$payee", 200, $payee)->data->{$payee};

    $this->assertEquals(
      $pending_summary->pending->trades, //expected
      $init_summary->pending->trades + 1, // actual
      "Expected {$pending_summary->pending->trades} pending trades, found ".($init_summary->pending->trades + 1)
    );
    $this->assertEquals(
      $pending_summary->completed->balance,
      $init_summary->completed->balance
    );
    if (0) { // can't test this now that return transactions are formatted.
      // Get the amount of the transaction, including fees.
      list($income, $expenditure) = $this->transactionDiff($transaction, $payee);
      // Failed asserting that 4 matches expected 0
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
      // We can't easily test partners unless we clear the db first.
      // Admin confirms the transaction
      $this->sendRequest("transaction/$transaction->uuid/completed", 201, $admin, 'patch');
      $completed_summary = $this->sendRequest("account/summary?acc_path=$payee", 200, $payee)->data->{$payee};
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
    }
    // Filtering.
    $norm_user = reset($this->normalAccIds);
    $results = $this->sendRequest("transactions", 200, $norm_user);
    $first_uuid = reset($results->data)->uuid;
    $this->sendRequest("transaction/$first_uuid", 200, $norm_user);
    $this->sendRequest("entries/$first_uuid", 200, $norm_user);
    // No results
    $this->sendRequest("transactions?description=jdksksh", 200, $norm_user);
    $this->sendRequest("entries?description=jdksksh", 200, $norm_user);

    $results = $this->sendRequest("transactions?description=$transaction_description", 200, $norm_user);
    // Every result entry should contain the string
    $counts = [];
    foreach ($results->data as $transaction) {
      $counts[] = strpos($transaction->entries[0]->description, $transaction_description);
    }
    $this->assertContainsOnly('int', $counts, TRUE, 'Transaction did not filter by description.');

    $first_three = $this->sendRequest("transactions?limit=3&offset=0", 200, $norm_user)->data;
    $this->assertEquals(3, count($first_three), "Pager failed to return 3 transactions");
    // show the second transaction from the above list.
    $second = $this->sendRequest("transactions?limit=1&offset=1", 200, $norm_user)->data;
    $this->assertEquals(array_slice($first_three, 1, 1), $second, "The offset/limit queryparams don't work");

    $all_entries = $this->sendRequest("entries", 200, $norm_user);
    $entry_list = (array)$all_entries->data;
    $this->assertEquals(count($entry_list), $all_entries->meta->number_of_results);
    if (count($entry_list) < 3) {
      $this->makeValidTransaction();
    }
    $limited = $this->sendRequest("entries?limit=3&offset=0", 200, $norm_user)->data;
    $this->assertEquals(3, count((array)$limited), "Pager failed to return 3 entries");
    $limited = (array)$this->sendRequest("entries?limit=1&offset=1", 200, $norm_user)->data;
    $selected_entry = array_slice($entry_list, 1, 1);
    $this->assertEquals(array_pop($selected_entry), array_pop($limited), "The offset/limit queryparams didn't deliver 1,1");


    // Test the sort
    $results = $this->sendRequest("transactions?states=erased,complete", 200, $norm_user);
    $err = FALSE;
    $data = (array)$results->data;
    foreach ($data as $result) {
      if (!in_array($result->state, ['erased', 'completed'])) {
        $err = TRUE;
      }
    }
    $this->assertEquals(FALSE, $err, 'Failed to filter by 2 states.');
    $this->assertEquals(count($data), $results->meta->number_of_results);
    $this->assertNotNull($results->meta->transitions, 'no transitions on filter result');
    if (!empty($data)) {
      $uuid = $data[0]->uuid;
      $this->assertNotEmpty($result->meta->transitions->{$uuid});
    }

    $results = $this->sendRequest("transactions?involving=$payee", 200, $norm_user);
    foreach ($data as $res) {
      $entries = (array)$res->entries[0];
      $main_entry = $entries[0];
      // Every result should have $payee as either payee or payer;
      if (strpos($main_entry->payee, $payee) === FALSE and strpos($main_entry->payer, $payee) === FALSE) {
        $err = TRUE;
      }
    }
    $this->assertEquals(FALSE, $err, "Failed to filter by transactions involving $payee");
    // Erase the'test bill' transaction and check that stats are updated.
    $this->sendRequest("transaction/$transaction->uuid/erased", 201, $admin, 'patch');
    $erased_summaries = $this->sendRequest("account/summary?acc_path=$payee", 200, $payee)->data;
    $this->assertEquals($erased_summaries->{$payee}, $init_summary);
  }

  function testAccountSummaries() {
    $user1 = reset($this->normalAccIds);
    $all_limits = $this->sendRequest("account/limits", 200, $user1);
    // Currently there is no per-user access control around limits visibility.
    $limits = $this->sendRequest("account/limits?acc_path=$user1", 200, $user1)->data;
    $this->assertlessThan(0, reset($limits)->min, "Minimum account limit was not less than zero.");
    $this->assertGreaterThan(0, reset($limits)->max, "Maximum account limit was not greater than zero.");
    // account/summary/{acc_id} is already tested
    $all_summaries = $this->sendRequest("account/summary", 200, $user1);
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
      if ($e->payee == $payee) {
        $income += $e->quant;
      }
      elseif ($e->payer == $payee) {
        $expenditure += $e->quant;
      }
    }
    return [$income, $expenditure];
  }

  protected function truncate($db='') {
    if ($db) {
      $db .= '.';
    }
    \CCNode\Db::query("TRUNCATE TABLE {$db}entries");
    \CCNode\Db::query("TRUNCATE TABLE {$db}transactions");
    \CCNode\Db::query("TRUNCATE TABLE {$db}transaction_index");
    \CCNode\Db::query("TRUNCATE TABLE {$db}hash_history");
    \CCNode\Db::query("TRUNCATE TABLE {$db}log");
  }

  protected function makeValidTransaction() {
    $obj = (object)[
      'payer' => reset($this->normalAccIds),
      'description' => 'valid transaction',
      'quant' => 10,
      'type' => 'bill',
      'metadata' => ['foo' => 'bar']
    ];
    $payee = next($this->normalAccIds);
    $result = $this->sendRequest('transaction', 200, $payee, 'post', json_encode($obj));
    $uuid = $result->data->uuid;
    $this->sendRequest("transaction/$uuid/pending", 201, $payee, 'patch');
    $this->sendRequest("transaction/$uuid/completed", 201, $obj->payer, 'patch');
  }
}
