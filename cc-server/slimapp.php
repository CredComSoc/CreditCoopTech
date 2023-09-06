<?php
/**
 * Reference implementation of a credit commons node
 *
 * @note Slim works with php8.0 and breaks with 8.1.
 * @todo Slim4 which is more likely to be upgradable with php but need to
 *   fiddle PSR7 validator https://github.com/thephpleague/openapi-psr7-validator/issues/136
 */

use CCServer\Slim3ErrorHandler;
use CCServer\PermissionMiddleware;
use CCServer\SetupMiddleware;
use CCServer\LoggingMiddleware;
use CCServer\CustomBusinessLogic;
use CCServer\DecorateResponse;
use CCNode\Transaction\TransversalTransaction;
use CCNode\Transaction\Transaction;
use CreditCommons\NewTransaction;
use CreditCommons\Exceptions\CCViolation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function CCNode\pager;
// Slim4 (when the League\OpenAPIValidation is ready)
//use Slim\Factory\AppFactory;
//use Psr\Http\Message\ServerRequestInterface;
//$app = AppFactory::create();
//$app->addErrorMiddleware(true, true, true);
//$app->addRoutingMiddleware();
//$errorMiddleware = $app->addErrorMiddleware(true, true, true);
//// See https://www.slimframework.com/docs/v4/middleware/error-handling.html
//// Todo this would be tidier in a class of its own extending Slim\Handlers\ErrorHandler.
//// Note that v4 has $app->addBodyParsingMiddleware();
////This handler converts the CCError exceptions into Json and returns them.
//$errorMiddleware->setDefaultErrorHandler(function (
//    ServerRequestInterface $request,
//    \Throwable $exception,
//    bool $displayErrorDetails,
//    bool $logErrors,
//    bool $logErrorDetails
//) use ($app) {
//    $response = $app->getResponseFactory()->createResponse();
//    if (!$exception instanceOf CCError) {
//      $exception = new CCFailure($exception->getMessage());
//    }
//    $response->getBody()->write(json_encode($exception, JSON_UNESCAPED_UNICODE));
//    return $response->withStatus($exception->getCode());
//});
$app = new \Slim\App();
$app->add(new DecorateResponse());
$app->add(new LoggingMiddleware());
$app->add(new SetupMiddleware());
$c = $app->getContainer();
$getErrorHandler = function ($c) {
  return new Slim3ErrorHandler();
};
$c['errorHandler'] = $getErrorHandler;
$c['phpErrorHandler'] = $getErrorHandler;

/**
 * Default HTML page. (Not part of the API)
 * Since this exists as an actual file, it should be handled by .htaccess
 */
$app->get('/', function (Request $request, Response $response) {
  header('Location: index.html');
  exit;
});

/**
 * Credit Commons API methods
 */
$app->options('/', function (Request $request, Response $response, $args) {
  global $node;
  $body = [
    'data' => empty($args['id']) ? $node->getOptions() : []
  ];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('permittedEndpoints')->add(PermissionMiddleware::class);


$app->get('/workflows', function (Request $request, Response $response) {
  global $cc_workflows; //is created when $node is instantiated
  $contents = ['data' => $cc_workflows];
  $response->getBody()->write(json_encode($contents, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('workflows')->add(PermissionMiddleware::class);

$app->get('/handshake', function (Request $request, Response $response) {
  global $node;
  $contents = ['data' => $node->handshake()];
  $response->getBody()->write(json_encode($contents, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('handshake')->add(PermissionMiddleware::class);

$app->get('/absolutepath', function (Request $request, Response $response) {
  global $node;
  $body = ['data' => $node->getAbsolutePath()];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('absolutePath')->add(PermissionMiddleware::class);

$app->get("/about", function (Request $request, Response $response, $args) {
  // get the downstream rate and multiply it by the current rate
  global $node;
  $query_params = $request->getQueryParams();
  if (!isset($query_params['node_path'])) {
    throw new CCViolation('Missing query param: node_path');
  }
  $body = ['data' => $node->about($query_params['node_path'])];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('about')->add(PermissionMiddleware::class);//must be logged in to know which way to convert the price.

$app->get("/account/names", function (Request $request, Response $response, $args) {
  global $node;
  $query_params = $request->getQueryParams();
  $limit = $query_params['limit'] ??'10';
  // Assuming the limit is 10.
  $names = $node->accountNameFilter($query_params['acc_path']??'', $limit);
  $body = ['data' => $names];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('accountNameFilter')->add(PermissionMiddleware::class);

$app->get("/account/summary", function (Request $request, Response $response, $args) {
  global $node;
  $query_params = $request->getQueryParams();
  $acc_path = $query_params['acc_path']??'';
  $body = ['data' => $node->getAccountSummary($acc_path)];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('accountSummary')->add(PermissionMiddleware::class);

$app->get("/account/limits", function (Request $request, Response $response, $args) {
  global $node;
  $query_params = $request->getQueryParams();
  $acc_path = $query_params['acc_path']??'';
  $body = ['data' => $node->getAccountLimits($acc_path)];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('accountLimits')->add(PermissionMiddleware::class);

$app->get("/account/history", function (Request $request, Response $response, $args) {
  global $node;
  $query_params = $request->getQueryParams();
  $acc_path = $query_params['acc_path'];
  unset($query_params['acc_path']);
  if (!$acc_path) {
    throw new CCViolation ('Missing query param: acc_path');
  }
  $params = $query_params + ['samples' => -1];
  $points = $node->getAccountHistory($acc_path, $params['samples']);
  $times = array_keys($points);
  $body = [
    'data' => $points,
    'meta' => [
      'min' => min($points),
      'max' => max($points),
      'points' => count($points),
      'start' => min($times),
      'end'=> max($times)]
  ];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('accountHistory')->add(PermissionMiddleware::class);

$uuid_regex = '[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}';
// Retrieve one transaction
$app->get('/transaction/{uuid:'.$uuid_regex.'}', function (Request $request, Response $response, $args) {
  global $node;
  $uuid = array_shift($args);
  [$transaction, $transitions] = $node->getTransaction($uuid);
  $body = [
    'data' => $transaction->jsonDisplayable(),
    'meta' => ['transitions' => [$transaction->uuid => $transitions]]
  ];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('getTransaction')->add(PermissionMiddleware::class);

// Retrieve one transaction as StandaloneEntries.
$app->get('/entries/{uuid:'.$uuid_regex.'}', function (Request $request, Response $response, $args) {
  global $node;
  $uuid = array_shift($args);
  $body = ['data' => $node->getTransactionEntries($uuid)];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('getEntries')->add(PermissionMiddleware::class);

// The client sends a new transaction
$app->post('/transaction', function (Request $request, Response $response) {
  global $cc_user, $node, $cc_workflows;
  $data = json_decode(strval($request->getBody()));
  // Validate the input and create UUID
  $new_transaction = NewTransaction::create($data, $cc_workflows, $cc_user->id);
  $transaction = Transaction::createFromNew($new_transaction); // in state 'init'

  $additional_entries = $node->buildValidateRelayTransaction($transaction);
  $status_code = $transaction->version < 1 ? 200 : 201; // Depends on workflow
  $body = [
    'data' => $transaction->jsonDisplayable(),
    'meta' => ['transitions' => $transaction->transitions()]
  ];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response->withStatus($transaction->state == 'validated' ? 200 : 201);
}
)->setName('newTransaction')->add(PermissionMiddleware::class);

// An upstream node is sending a new transaction.
$app->post('/transaction/relay', function (Request $request, Response $response) {
  global $cc_user, $node, $cc_config;
  $data = json_decode(strval($request->getBody()));
  // Convert the incoming amounts as soon as possible.
  $transaction = TransversalTransaction::createFromUpstream($data);
  $confirm = $transaction->getWorkflow()->creation->confirm;
  $additional_entries = $node->buildValidateRelayTransaction($transaction);
  $body = ['data' => array_values($additional_entries)];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response->withStatus($transaction->state == 'validated' ? 200 : 201);
}
)->setName('relayTransaction')->add(PermissionMiddleware::class);

$app->patch('/transaction/{uuid:'.$uuid_regex.'}/{dest_state}', function (Request $request, Response $response, $args) {
  global $node;
  $node->transactionChangeState($args['uuid'], $args['dest_state']);
  if ($args['dest_state'] == 'null') {
    return $response->withStatus(200);
  }
  return $response->withStatus(201);
}
)->setName('stateChange')->add(PermissionMiddleware::class);

// Filter transactions
$app->get("/transactions", function (Request $request, Response $response, $args) {
  global $node;
  $params = $request->getQueryParams() + ['sort' => 'written', 'dir' => 'desc', 'limit' => 25, 'offset' => 0];
  [$count, $transactions, $transitions] = $node->filterTransactions($params);

  $body = [
    'data' => array_map(function ($t){return $t->jsonDisplayable();}, $transactions),
    'meta' => [
      'number_of_results' => $count,
      'current_page' => ($params['offset'] / $params['limit']) + 1,
      'transitions' => $transitions
    ],
    'links' => pager('/transactions', $params, $count)
  ];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('filterTransactions')->add(PermissionMiddleware::class);

// Filter transaction entries
$app->get("/entries", function (Request $request, Response $response, $args) {
  global $node;
  $params = $request->getQueryParams() + ['sort' => 'written', 'dir' => 'desc', 'offset' => 0, 'limit' => 25];
  [$count, $entries] = $node->filterTransactionEntries($params);
  // $entries should be keyed by id
  $body = [
    'data' => $entries, // this is interpreted in the client not as a list but as an object
    'meta' => [
      'number_of_results' => $count,
      'current_page' => ($params['offset'] / $params['limit']) + 1,
    ],
    'links' => pager('/entries', $params, $count)
  ];
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response;
}
)->setName('filterTransactionEntries')->add(PermissionMiddleware::class);


return $app;
