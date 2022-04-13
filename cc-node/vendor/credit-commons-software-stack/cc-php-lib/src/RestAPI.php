<?php

namespace CreditCommons;

use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use CreditCommons\Requester;
use CreditCommons\BaseNewTransaction;
use CreditCommons\Exceptions\CCError;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\UnavailableNodeFailure;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;

/**
 * Class for calling credit commons nodes, for use by nodes and clients.
 * - Adds extra headers to each downstream request
 * - Catches errors from Guzzle and throws them again as CC Errors.
 */
class RestAPI extends Requester implements CreditCommonsInterface {

  public $nodeName;

  /**
   * Build a request from an upstream node to a downstream node.
   */
  function __construct(string $downstream_node_url, string $current_node_name, string $last_hash) {
    parent::__construct($downstream_node_url);
    $this->nodeName = $current_node_name;
    // Authentication
    $this->options[RequestOptions::HEADERS]['CC-User'] = $this->nodeName;
    $this->options[RequestOptions::HEADERS]['CC-auth'] = $last_hash;
  }

  public function getOptions() : array {
    return (array)$this->setMethod('options')->request(200);
  }

  /**
   * {@inheritdoc}
   */
  public function getTrunkwardNodeNames() : array {
    return $this->request(200, 'trunkwards');
  }

  /**
   * {@inheritdoc}
   */
  public function handshake() : void {
    $this->request(200, 'handshake');
  }

  /**
   * {@inheritdoc}
   */
  public function submitNewTransaction(BaseNewTransaction $new_transaction) : \stdClass {
    $request = $this
      ->setMethod('post')
      ->setBody($new_transaction);

    if (empty($new_transaction->state)) {
      $transaction = $request->request(200, 'transaction');
    }
    else {
      $transaction = $request->request(201, 'transaction');
    }
    return $transaction;
  }

  /**
   * {@inheritdoc}
   */
  public function filterTransactions(array $fields = []) : array {
    // Send only valid fields
    $valid = ['payer', 'payee', 'involving', 'before', 'after', 'uuid', 'quant', 'description', 'state', 'type', 'is_primary', 'uuid'];
    $fields = array_intersect_key(array_filter($fields), array_flip($valid));
    $this->options[RequestOptions::QUERY] = $fields;
    return (array)$this->request(200, 'transaction');
  }

  /**
   * {@inheritdoc}
   */
  public function getAccountHistory(string $acc_id, int $samples = 0) : array {
    if ($samples) {
      $this->options[RequestOptions::QUERY] = ['samples' => $samples];
    }
    $path = 'account/history';
    if ($acc_id) {
      $path .= '/'.urlencode($acc_id);
    }
    $result = $this->request(200, $path);
    return (array)$result;
  }

  /**
   * {@inheritdoc}
   */
  function getAccountLimits(string $acc_id = '') : \stdClass {
    $path = 'account/limits';
    if ($acc_id) {
      $path .= '/'.urlencode($acc_id);
    }
    return $this->request(200, $path);
  }

  /**
   * {@inheritdoc}
   */
  public function getAccountSummary(string $acc_id = '') : \stdClass {
    $path = 'account/summary';
    if ($acc_id) {
      $path .= '/'.($acc_id);
    }
    $results = $this->request(200, $path);
    $stats = new \stdClass;
    $stats->pending = \CreditCommons\TradeStats::create($results->pending);
    $stats->completed = \CreditCommons\TradeStats::create($results->completed);
    return $stats;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccountSummaries(string $given_path = '') : array {
    $path = 'accounts/summary';
    if ($given_path) {
      $path .= '/'.$given_path;
    }
    $results = $this->request(200, $path);
    foreach ((array)$results as $key => $set) {
      $stats[$key] = new \stdClass;
      $stats[$key]->pending = \CreditCommons\TradeStats::create($set->pending);
      $stats[$key]->completed = \CreditCommons\TradeStats::create($set->completed);
    }
    return $stats;
  }

  /**
   * {@inheritdoc}
   */
  public function getTransaction(string $uuid, $format = 'full') : \stdClass {
    return $this->request(200, "transaction/$uuid/$format");
  }

  /**
   * {@inheritdoc}
   */
  public function accountNameAutocomplete(string $fragment = '') : array {
    $path = 'accounts/filter';
    if ($fragment) {
      $path .= "/$fragment";
    }
    $account_names = $this->request(200, $path);
    return (array)$account_names;
  }

  /**
   * {@inheritdoc}
   */
  public function transactionChangeState(string $uuid, string $target_state) : void {
    $this
      ->setMethod('patch')
      ->request(201, "transaction/$uuid/$target_state");
  }

  /**
   * {@inheritdoc}
   */
  public function getWorkflows() : array {
    $results = (array)$this->request(200, 'workflows');
    foreach ($results as $node_name => $wfs) {
      foreach ($wfs as $def) {
        $workflow = new Workflow($def);
        if ($workflow->active) {
          $tree[$node_name][$workflow->getHash()] = $workflow;
        }
      }
    }
    if (empty($tree)) {
      throw new CCFailure('No active trunkwards workflows were found.');
    }
    return $tree;
  }

  /**
   * {@inheritdoc}
   */
  public function buildValidateRelayTransaction(Transaction $transaction) : array  {
    global $orientation;
    $additional_rows = $this
      ->setMethod('post')
      ->setBody($transaction)
      ->request(201, 'transaction/relay');
    // @todo Decide whether to add the entries here or return them
    $transaction->createEntries($additional_rows, $orientation->downstreamAccount);

    foreach ($additional_rows as $row) {
      $row->author = $orientation->downstreamAccount;
      $entries[] = Entry::create($row);
      $transaction->entries[] = Entry::create($row)->additional();
    }
    return $entries;
  }

  /**
   * Upcast an exception passed from downstream as a json object back to CCError
   * @param \stdClass $e
   *   Exception reconstructed from json response.
   * @return \CCError
   * @throws \Exception
   */
  static function reconstructCCException(\stdClass $e) : CCError {
    $error_class = "\CreditCommons\Exceptions\\$e->class";
    if (empty($e->class) or !class_exists($error_class)) {
      throw new \Exception('Unexpected exception type: '.print_r($error_class, 1));
    }
    // Because CCError  needed to extend throwable and throwable has a protected property 'message',
    // we mad a new public property content for violations and failures
    // of no specific type.
    $r = new \ReflectionClass($error_class);
    if (in_array($e->class, ['CCViolation', 'CCFailure'])) {
      $ccError = $r->newInstanceArgs(['message' => $e->message]);
    }
    else {
      $arg_names = array_diff_key(get_class_vars($error_class), ['node' => 1, 'class' => 1]);
      // Prepare an array of named args
      $args = [];
      foreach (array_keys($arg_names) as $var_name) {
        $args[$var_name] = $e->$var_name;
      }
      $error_class::validateCastParams($args);
      $ccError = $r->newInstanceArgs($args);
    }

    $ccError->node = $e->node;
    return $ccError;
  }

  /**
   * {@inheritDoc}
   */
  protected function request(int $required_code, string $endpoint = '/') {
    try{
      $client = new Client(['base_uri' => $this->baseUrl, 'timeout' => 1]);
      $response = $client->{$this->method}($endpoint, $this->options);
    }
    catch (ConnectException $e) {
      // The request timed out.
      throw new UnavailableNodeFailure(url: $this->baseUrl .'/'.$endpoint );
    }
    catch (ClientException $e) {// All 400 errors should be in our own format
      $contents = $e->getResponse()->getBody()->getContents();
      if ($e = json_decode($contents)) {
        throw $this->reconstructCCException($e);
      }
      die($endpoint . $contents);
    }
    catch (ServerException $e) {// All 500 errors should be in our own format
      $contents = $e->getResponse()->getBody()->getContents();
      if ($e = json_decode($contents)) {
        throw $this->reconstructCCException($e);
      }
      die('ServerException: '.$endpoint.$contents);// has never happenned yet...
    }
    catch (CCFailure $e) {
      throw $e;
    }
    catch (CCViolation $e) {
      throw $e;
    }
    catch (\Exception $e) {
      //print_r($client->);
      //  This is not likely to be caught
      $mess = "Unexpected error from $this->method $this->baseUrl/$endpoint: ";
      throw new CCFailure($e->getCode() ." $mess: ". $e->getMessage());
    }
    return $this->processResponse($response, $required_code);
  }

  /**
   * Process the Response object
   */
  protected function processResponse($response, int $required_code) {
    if ($response->getStatusCode() != $required_code) {
      throw new CCFailure("Unexpected status code ".$response->getStatusCode() .' to '.$_SERVER['REQUEST_URI']);
    }
    $raw_result = $response->getBody()->getContents();
    if ($raw_result and $response->getHeaderline('Content-Type') != 'application/json') {
      // @todo are ALL responses Json or are some null or some text?
      throw new CCFailure("Non-json response: '$raw_result'");
    }
    $result = json_decode($raw_result);
    if ($raw_result and $raw_result != 'null' and is_null($result)) {
      throw new CCFailure("Json expected but not delivered: '$raw_result'");
    }
    // All requests are for json objects.
    return $result;
  }

}
