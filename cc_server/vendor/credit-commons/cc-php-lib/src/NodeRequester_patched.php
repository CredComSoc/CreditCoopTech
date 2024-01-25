<?php

namespace CreditCommons;

use CreditCommons\Requester;
use CreditCommons\TradeStats;
use CreditCommons\TransactionInterface;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\CCError;
use CreditCommons\Exceptions\UnavailableNodeFailure;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;

// PATCHED
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\MaxLimitViolation;
use CreditCommons\Exceptions\MinLimitViolation;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
#require_once 'vendor/guzzlehttpd/psr7/src/Utils.php';
use GuzzleHttp\Psr7\Utils;
// Convert the outgoing credcom request to a clearingcentral v1 request.
global $middleware;
$middleware = function ($handler) {
  return function ($request, $options) use ($handler) {
    global $input,$v1_db_conn;
    // translate the credcom request to v1 request.
    /** \GuzzleHttp\Psr7\Request $request */
    $url_parts = parse_url($request->getUri());
    $url = $url_parts['scheme'].'://'.$url_parts['host'] . '/txrengine.asp';
    $dest_acc = $input->outgoing ? $input->buyer_nid : $input->seller_nid;
    $password_query = "SELECT password FROM exchanges WHERE nid = '$dest_acc'";
    $input->password = mysqli_query($v1_db_conn, $password_query)->fetch_object()->password;
    $url .= '?'.http_build_query($input);

    $request = $request->withMethod('get')
      ->withoutHeader('Accept')->withoutHeader('Content-Type')
      ->withoutHeader('Content-Length')->withoutHeader('CC-User')->withoutHeader('CC-Auth')
      ->withUri(new Uri($url))
      ->withBody(Utils::streamFor());
    $promise = $handler($request, $options);
    return $promise->then(
      function (Response $response) {
        // Get the numerical error 'response' from the json body.
        // If it is 1 then credcom wants 201 and []
        // Otherwise, convert the error into a credcom error.
        parse_str($response->getBody()->getContents(), $result);
        $result = (object)$result;
        switch ($result->response) {
          case 1:
            // Assume there's no new transactions.
            // Translate the response into 200 with empty array
            $body = ['data' => []];
            $code = 201;
            break;
          case 2: // user does not exist
            $body = new DoesNotExistViolation(type: 'user', id: $result->outgoing ? $result->seller_id : $result->buyer_id);
            $code = 400;
            break;
          case 3: // NID not recognised
            $body = new DoesNotExistViolation(type: 'exchange', id: $result->outgoing ? $result->seller_nid : $result->buyer_nid);
            $code = 400;
            break;
          case 4: // denied over limit, account locked, etc.
            $body = $result->outgoing ?
              new MaxLimitViolation(limit: 0, projected: 0, accId: $result->seller_id) :
              new MinLimitViolation(limit: 0, projected: 0, accId: $result->buyer_id);
            $code = 400;
            break;
          default:
            $body = new CCFailure(message: "Unexpected $result->response response from ".$url_parts['host']);
            $code = 500;
        }
        if (isset($code)) {
          $response = $response->withStatus($code);
        }
        if (isset($body)) {
          $response = $response->withBody(Utils::streamFor(json_encode($body)))
            ->withHeader('Content-Type', 'application/json');
        }

        return $response;
      }
    );
  };
};


/**
 * Class for calling credit commons nodes, for use by nodes and clients.
 * - Adds extra headers to each downstream request
 * - Catches errors from Guzzle and throws them again as CC Errors.
 */
class NodeRequester extends Requester implements CreditCommonsInterface {

  public $nodeName;
  protected bool $catch = FALSE;

  /**
   * Build a request from an upstream node to a downstream node.
   */
  function __construct(string $downstream_node_url, string $current_node_name, string $last_hash) {
    global $cc_user;
    parent::__construct($downstream_node_url);
    $this->nodeName = $current_node_name;
    // Authentication
    $this->setHeader('CC-User', $current_node_name);
    $this->setHeader('CC-Auth', $last_hash);
    if (!($cc_user instanceOf AccountRemoteInterface)){
      $this->catch = TRUE;
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getOptions() : array {
    return (array)$this->setMethod('options')->request()->data;
  }

  /**
   * {@inheritDoc}
   */
  public function getAbsolutePath() : array {
    return $this->request('absolutepath')->data;
  }

  /**
   * {@inheritDoc}
   */
  public function handshake() : array {
    $temp = $this->catch;
    $this->catch = TRUE;
    if ($hs = $this->request('handshake')) {
      $this->catch = $temp;
      return (array)$hs->data;
    }
    throw new CreditCommons\Exceptions\UnavailableNodeFailure($this->baseUrl);
  }

  /**
   * {@inheritDoc}
   */
  public function filterTransactions(array $params = []) : array {
    // Send only valid params
    $valid = ['payer', 'payee', 'involving', 'before', 'after', 'uuid', 'quant', 'description', 'states', 'type', 'is_primary', 'uuid'];
    $params = array_intersect_key($params, array_flip($valid));
    if (isset($params['entries']) and $params['entries'] <> 'false') {
      $params['entries'] = 'true';// this is how to send a boolean in a querystring.
    }
    else {
      unset($params['entries']);
    }
    $this->options[RequestOptions::QUERY] = $params;
    // NB there is also the meta property which SHOULD have the pager in it
    $result = $this->request('transactions');
    return [$result->data, $result->meta, $result->links];
  }

  /**
   * {@inheritDoc}
   */
  public function filterTransactionEntries(array $params = []) : array {
    // Send only valid params
    $valid = ['payer', 'payee', 'involving', 'before', 'after', 'uuid', 'quant', 'description', 'states', 'type', 'is_primary', 'uuid'];
    $params = array_intersect_key($params, array_flip($valid));
    $this->options[RequestOptions::QUERY] = $params;
    // NB there is also the meta property which SHOULD have the pager in it
    $result = $this->request('entries');
    return [$result->data, $result->links];
  }

  /**
   * {@inheritDoc}
   */
  public function getTransaction(string $uuid) : array {
    $result = $this->request("transaction/$uuid");
    return [$result->data, $result->meta->transitions];
  }

  /**
   * {@inheritDoc}
   */
  public function getTransactionEntries(string $uuid) : array {
    return $this->request("entries/$uuid")->data;
  }

  /**
   * {@inheritDoc}
   */
  public function accountNameFilter(string $acc_path = '', $limit = 10) : array {
    $path = "account/names";
    if ($acc_path) {
      $params[] = "acc_path=$acc_path";
    }
    $params[] = "limit=$limit";
    // Keyed array is interpreted as object by json_decode.
    return (array)$this->request("$path?".implode('&', $params))->data;
  }

  /**
   * {@inheritDoc}
   */
  function getAccountLimits(string $acc_path = '') : array {
    $path = 'account/limits';
    if ($acc_path) {
      $path .= '?acc_path='. $acc_path;
    }
    // Keyed array is interpreted as object by json_decode.
    return (array)$this->request($path)->data;
  }

  /**
   * {@inheritDoc}
   */
  public function getAccountSummary(string $acc_path = '') : array {
    $path = 'account/summary';
    if ($acc_path) {
      $path .= '?acc_path='. $acc_path;
    }
    $all_results = $this->request($path)->data;
    $all_stats = [];
    foreach ($all_results as $acc_id => $results) {
      $stats = new \stdClass;
      $stats->pending = TradeStats::create($results->pending);
      $stats->completed = TradeStats::create($results->completed);
      $all_stats[$acc_id] = $stats;
    }
    return $all_stats;
  }

  /**
   * {@inheritDoc}
   */
  public function getAccountHistory(string $acc_path, int $samples = 0) : array {
    if ($samples) {
      $this->options[RequestOptions::QUERY] = ['samples' => $samples];
    }
    $path = "account/history";
    if ($acc_path) {
      $path .= '?acc_path='. $acc_path;
    }
    $result = $this->request($path)->data;
    return (array)$result;
  }

  /**
   * {@inheritDoc}
   */
  public function transactionChangeState(string $uuid, string $target_state) : void {
    $expected = $target_state == 'null' ? 200 : 201;
    $this
      ->setMethod('patch')
      ->request("transaction/$uuid/$target_state");
  }

  /**
   * {@inheritDoc}
   */
  public function getWorkflows() : array {
    $results = (array)$this->request('workflows')->data;
    $tree = [];
    foreach ($results as $def) {
      $workflow = new Workflow($def);
      if ($workflow->active) {
        $tree[$workflow->getHash()] = $workflow;
      }
    }
    if (empty($tree)) {
      throw new CCFailure('No active trunkwards workflows were found.');
    }
    return $tree;
  }

  /**
   * {@inheritDoc}
   */
  public function buildValidateRelayTransaction(TransactionInterface $transaction) : array  {
    $rows = $this
      ->setMethod('post')
      ->setBody($transaction)// via jsonSerialize
      ->request('transaction/relay');
    return (array)$rows->data;
  }

  /**
   * {@inheritDoc}
   */
  public function about(string $node_path) : \stdClass {
    return $this->request("about?node_path=$node_path")->data;
  }

  /**
   * {@inheritDoc}
   */
  protected function request(string $endpoint = '/') :\stdClass|NULL {
    // Guzzle will strip the path off $this->baseUrl, so if is a path prefix
    // copy it from the baseUrl to the endpoint.
    $baseUrl = $this->baseUrl;
    $parts = parse_url($this->baseUrl);
    if (isset($parts['path'])) {
      $endpoint = $parts['path'] .'/'.$endpoint;
      $baseUrl = $parts['scheme'].'://'.$parts['host'];
      if (isset($parts['port'])){
        $baseUrl .= ':'.$parts['port'];
      }
    }
    $params = ['base_uri' => $baseUrl, 'timeout' => 2];

    // PATCHED: Add the middleware if the dest server is using clearingcentral API
    global $middleware, $v1_db_conn;
    $q = "SELECT `system` FROM exchanges where data_url = '$this->baseUrl'";
    $res = mysqli_query($v1_db_conn, $q)->fetch_object();
    switch ($res->system) {
      case 'ces':
      case 'ozces':
      case 'ices':
      case 'time and talents':
      case 'cforge':
        $handlerStack = \GuzzleHttp\HandlerStack::create();
        $handlerStack->push($middleware);
        $params['handler'] = $handlerStack;
    }
    try{
      $client = new Client($params);
      $response = $client->{$this->method}($endpoint, $this->options);
    }
    catch (ConnectException $e) {
      // The request timed out.
      throw new UnavailableNodeFailure(unavailable_url: "$this->baseUrl");
    }
    // This is Guzzle's way of throwing 40x and 50x errors, which includes credcom errors.
    catch (ClientException|ServerException $e) {// This includes CCError from downstream
      $contents = $e->getResponse()->getBody();
      if (!$this->catch and $contents) {
        if (!headers_sent()) {
          header("HTTP/1.1 ".$e->getCode());
          header("Content-Type: application/javascript");
        }
        die($contents);
      }
      elseif(strlen($contents)) {
        $error = json_decode($contents)->errors[0];
        throw self::reconstructCCErr($error);
      }
      else {
        $message = "Empty ". $e->getCode() ." response from $this->method $this->baseUrl/$endpoint";
        if (isset($this->options[RequestOptions::BODY])) {
          $message .= " with request body\n".$this->options[RequestOptions::BODY];
        }
        throw new CCFailure($message);
      }
    }
    catch (\Exception $e) { // Should never happen.
      // This is not likely to be caught
      $mess = "Unexpected error from $this->method $this->baseUrl/$endpoint: ";
      throw new CCFailure($e->getCode() ." $mess: ". $e->getMessage());
    }
    return $this->processResponse($response);
  }

  /**
   * Process the Response object
   */
  protected function processResponse($response) : \stdClass|NULL {
    $raw_result = strval($response->getBody());
    if ($raw_result and $response->getHeaderline('Content-Type') != 'application/json') {
      // @todo are ALL responses Json or are some null or some text?
      throw new CCFailure($response->getHeaderline('Content-Type') ." non-json response: '$raw_result'");
    }
    if ($raw_result == '[]') {
      $result = new \stdClass();
    }
    else{
      $result = json_decode($raw_result);
    }
    if ($raw_result and $raw_result != 'null' and is_null($result)) {
      throw new CCFailure("Json expected but not delivered: '$raw_result'");
    }
    return $result;
  }

  /**
   * Upcast an exception passed from downstream as a json object back to CCError
   * @param \stdClass $remote_error
   *   Exception reconstructed from json response.
   * @return \CCError
   * @throws \Exception
   */
  static function reconstructCCErr(\stdClass $remote_error) : CCError {
    $error_class = "\CreditCommons\Exceptions\\$remote_error->class";
    if (empty($remote_error->class) or !class_exists($error_class)) {
      throw new \Exception('Unexpected exception type: '.print_r($error_class, 1));
    }
    $base_properties = ['node', 'class', 'method', 'path', 'line', 'user', 'trace'];
    $other_arg_names = array_diff_key(
      get_class_vars($error_class),
      array_flip($base_properties)
    );
    // Prepare an array of named args.
    $args = [];
    foreach (array_keys($other_arg_names) as $arg_name) {
      $args[$arg_name] = $remote_error->{$arg_name};
    }
    $error_class::validateCastParams($args);
    $ccError = new $error_class(...$args);
    $ccError->node = $remote_error->node;
    $ccError->path = $remote_error->path;
    $ccError->method = $remote_error->method;
    $ccError->user = $remote_error->user;
    $ccError->trace = $remote_error->trace;// seems not to overwrite.
    return $ccError;
  }

}
