<?php
/**
 * Reference implementation of a credit commons node
 *
 * @todo Update to slim 4 using https://github.com/thephpleague/openapi-psr7-validator/issues/136
 *
 */
namespace CCNode;

use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\HashMismatchFailure;
use CreditCommons\Exceptions\UnavailableNodeFailure;
use CreditCommons\Exceptions\WrongAccountViolation;
use CreditCommons\NodeRequester;
use CreditCommons\Account;
use CreditCommons\CreditCommonsInterface;
use CCNode\Accounts\Trunkward;
use CCNode\Accounts\User;
use CCNode\Accounts\Branch;
use CCNode\Slim3ErrorHandler;
use CCNode\PermissionMiddleware;
use CCNode\AddressResolver;
use CCNode\AccountStore;
use CCNode\Accounts\Remote;
use CCNode\Workflows;
use CCNode\Transaction\Transaction;
use CCNode\Transaction\StandaloneEntry;
use CCNode\Transaction\NewTransaction;
use CCNode\Transaction\TransversalTransaction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

// Slim4 (when the League\OpenAPIValidation is ready)
//use Slim\Factory\AppFactory;
//use Psr\Http\Message\ServerRequestInterface;
//$app = AppFactory::create();
//$app->addErrorMiddleware(true, true, true);
//$app->addRoutingMiddleware();
//$errorMiddleware = $app->addErrorMiddleware(true, true, true);
//// See https://www.slimframework.com/docs/v4/middleware/error-handling.html
//// Todo this would be tidier in a class of its own extending Slim\Handlers\ErrorHandler.
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

$app = new App();
$app;
$c = $app->getContainer();
$getErrorHandler = function ($c) {
  return new Slim3ErrorHandler();
};
$c['errorHandler'] = $getErrorHandler;
$c['phpErrorHandler'] = $getErrorHandler;

/**
 * Default HTML page. (Not part of the API)
 */
$app->get('/', function (Request $request, Response $response) {
  header('Location: index.html');
  exit;
});

/**
 * Implement the Credit Commons API methods
 */
$app->options('/{id:.*}', function (Request $request, Response $response, $args) {
  global $user;
  $endpoints = empty($args['id']) ? permitted_operations($user) : [];
  return json_response($response, $endpoints);
}
)->setName('permittedEndpoints')->add(PermissionMiddleware::class);

$app->get('/workflows', function (Request $request, Response $response) {
  // Todo need to instantiate workflows with the Trunkward requester if there is one.
  return json_response($response, (new Workflows())->loadAll());
}
)->setName('workflows')->add(PermissionMiddleware::class);

$app->get('/handshake', function (Request $request, Response $response) {
  global $user;
  $results = [];
  // This ensures the handshakes only go one level deep.
  if ($user instanceOf Accounts\User) {
    $remote_accounts = AccountStore()->filterFull(local: FALSE);
    if($trunkw = getConfig('trunkward_acc_id')) {
      $remote_accounts[] = AccountStore()->fetch($trunkw);
    }
    foreach ($remote_accounts as $acc) {
      if ($acc->id == $user->id) {
        continue;
      }
      try {
        $acc->handshake();
        $results[$acc->id] = 'ok';
      }
      catch (UnavailableNodeFailure $e) {
        $results[$acc->id] = 'UnavailableNodeFailure';
      }
      catch (HashMismatchFailure $e) {
        $results[$acc->id] = 'HashMismatchFailure';
      }
      catch(\Exception $e) {
        $results[$acc->id] = get_class($e);
      }
    }
  }
  return json_response($response, $results);
}
)->setName('handshake')->add(PermissionMiddleware::class);

$app->get('/absolutepath', function (Request $request, Response $response) {
  $node_names[] = getConfig('node_name');
  if ($trunkward = API_calls()) {
    $node_names = array_merge($trunkward->getAbsolutePath(), $node_names);
  }
  return json_response($response, $node_names, 200);
}
)->setName('absolutePath')->add(PermissionMiddleware::class);

$app->get('/accounts/names[/[{acc_path:.*$}]]', function (Request $request, Response $response, $args) {
  global $user;
  $path = $args['acc_path']??'';
  //$path = urldecode($args['acc_path'])??'';
  $node_name = getConfig('node_name');
  $remote_node = AddressResolver::create()->nodeAndFragment($path);
  if ($remote_node) {// Match names on a specific node.
    $acc_ids = $remote_node->autocomplete();
    if ($remote_node instanceOf Branch and !getConfig('trunkward_acc_id')) {
      foreach ($acc_ids as &$acc_id) {
        $acc_id = $node_name .'/'.$acc_id;
      }
    }
  }
  else {// Match names from here to the trunk.
    $trunkward_acc_id = getConfig('trunkward_acc_id');
    $trunkward_names = [];
    if ($trunkward_acc_id and $user->id <> $trunkward_acc_id) {
      $trunkward_names = load_account($trunkward_acc_id, $path)->autocomplete();
    }
    // Local names.
    $filtered = accountStore()->filterFull(fragment: trim($path, '/'));
    $local = [];
    foreach ($filtered as $acc) {
      $name = $acc->id;
      // Exclude the logged in account
      if ($name == $user->id) continue;
      if ($acc instanceOf Remote) $name .= '/';
      if ($user instanceOf Remote) {
        $local[] = $node_name."/$name";
      }
      else {
        $local[] = $name;
      }
    }
    $acc_ids = array_merge($trunkward_names, $local);
  }
  //if the request is from the trunk prefix all the results. (rare)
  $limit = $request->getQueryParams()['limit'] ??'10';
  return json_response($response, array_slice($acc_ids, 0, $limit));
}
)->setName('accountNameFilter')->add(PermissionMiddleware::class);

$app->get('/account/summary[/{acc_path:.*$}]', function (Request $request, Response $response, $args) {
  if ($path = $args['acc_path']??'') {
    $account = AddressResolver::create()->nearestNode($path);
    if ($account instanceof Remote and !$account->isAccount()) {// All the accounts on a remote node
      $result = $account->getAccountSummaries();
    }
    else {// A single account on any node
      $result = $account->getAccountSummary();
    }
  }
  else {// All accounts on the current node.
    $result = Transaction::getAccountSummaries(TRUE);
  }
  return json_response($response, $result);
}
)->setName('accountSummary')->add(PermissionMiddleware::class);

$app->get('/account/limits[/{acc_path:.*$}]', function (Request $request, Response $response, $args) {
  if ($path = $args['acc_path']??'') {
    $account = AddressResolver::create()->nearestNode($path);
    if ($account instanceof Remote and !$account->isAccount()) {// All the accounts on a remote node
      $result = $account->getAllLimits();
    }
    else {// A single account on any node
      $result = $account->getLimits();
    }
  }
  else {// All accounts on the current node.
    $result = accountStore()->allLimits(TRUE);
  }
  return json_response($response, $result);
}
)->setName('accountLimits')->add(PermissionMiddleware::class);

$app->get('/account/history/{acc_path:.*$}', function (Request $request, Response $response, $args) {
  $path = $args['acc_path'];
  $account = AddressResolver::create()->localOrRemoteAcc($path);
  $params = $request->getQueryParams() + ['samples' => -1];
  $result = $account->getHistory($params['samples']);//@todo refactor this.
  $response->getBody()->write(json_encode($result));
  return $response->withHeader('Content-Type', 'application/json');
}
)->setName('accountHistory')->add(PermissionMiddleware::class);

$uuid_regex = '[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}';
// Retrieve one transaction
$app->get('/transaction/{uuid:'.$uuid_regex."}[/{node_path}]", function (Request $request, Response $response, $args) {
  $params = $request->getQueryParams();
  $entries = $params['entries']??'false';
  unset($params['entries']);
  if (!empty($args['node_path'])) {
    $account = AddressResolver::create()->remoteNode($args['node_path']);
    if ($account instanceOf Remote) {
      $result = $account->getTransaction($args['uuid'], $entries == 'true');
    }
    else throw new WrongAccountViolation($args['node_path']);
  }
  elseif ($entries == 'true') {
    $result = array_values(StandaloneEntry::loadByUuid($args['uuid']));
  }
  else {// format = full (default)
    $result = Transaction::loadByUuid($args['uuid']);
    $result->responseMode = TRUE;// there's nowhere tidier to do this.
  }
  return json_response($response, $result, 200);
}
)->setName('getTransaction')->add(PermissionMiddleware::class);

// Filter transactions
$app->get('/transactions[/[{node_path}]]', function (Request $request, Response $response, $args) {
  $params = $request->getQueryParams();
  $results = [];
  $entries = $params['entries']??'false';
  unset($params['entries']);
  $node_path = $args['node_path']??'';
  if ($node_path) {
    $account = AddressResolver::create()->remoteNode($node_path);
    if ($account instanceOf Remote) {
      // @todo this node_path needs to be shortened. Rebuild addressresolver...
      $results = $account->filterTransactions($request->getQueryParams());
    }
    else throw new WrongAccountViolation($node_path);
  }
  elseif ($uuids = Transaction::filter(...$params)) {// keyed by entries and $args['format'] == 'entry') {
    if ($entries == 'true') {
      $results = StandaloneEntry::load(array_keys($uuids));
    }
    else {
      $results = [];
      foreach (array_unique($uuids) as $uuid) {
        $results[$uuid] = Transaction::loadByUuid($uuid);
      }
    }
    // All entries are returned, even to a foreign node.
  }
  return json_response($response, array_values($results), 200);
}
)->setName('filterTransactions')->add(PermissionMiddleware::class);

// Create a new transaction
$app->post('/transaction', function (Request $request, Response $response) {
  global $user;
  $request->getBody()->rewind(); // ValidationMiddleware leaves this at the end.
  $data = json_decode($request->getBody()->getContents());
  // validate the input and create UUID
  $from_upstream = NewTransaction::prepareClientInput($data);
  $transaction = Transaction::createFromUpstream($from_upstream); // in state 'init'
  // Validate the transaction in its workflow's 'creation' state
  $transaction->buildValidate();
  $status_code = $transaction->insert();
  // Send the whole transaction back via jsonserialize to the user.
  return json_response($response, $transaction, $status_code);
}
)->setName('newTransaction')->add(PermissionMiddleware::class);

// Relay a new transaction
$app->post('/transaction/relay', function (Request $request, Response $response) {
  global $user;
  $request->getBody()->rewind(); // ValidationMiddleware leaves this at the end.
  $data = json_decode($request->getBody()->getContents());
  // Convert the incoming amounts as soon as possible.
  $user->convertTrunkwardEntries($data->entries);
  $transaction = TransversalTransaction::createFromUpstream($data);
  $transaction->buildValidate();
  $status_code = $transaction->insert();
  // Return only the additional entries which are relevant to the upstream node.
  // @todo this could be more elegant.
  $additional_entries = array_filter(
    $transaction->filterFor($user),
    function($e) {return $e->isAdditional();}
  );
  // $additional_entries via jsonSerialize
  return json_response($response, $additional_entries, 201);
}
)->setName('relayTransaction')->add(PermissionMiddleware::class);

$app->patch('/transaction/{uuid:'.$uuid_regex.'}/{dest_state}', function (Request $request, Response $response, $args) {
  $status_code = Transaction::loadByUuid($args['uuid'])->changeState($args['dest_state']);
  debug('$status_code = '.$status_code);
  return $response->withStatus($status_code);
}
)->setName('stateChange')->add(PermissionMiddleware::class);

global $config;
if ($config and $config['dev_mode']) {
  // this stops execution on ALL warnings and returns CCError objects
  set_error_handler( "\\CCNode\\exception_error_handler" );
}

return $app;

/**
 * Load an account from the accountStore.
 *
 * @staticvar array $fetched
 * @param string $acc_id
 *   The account id or empty string to load a dummy account.
 * @return CreditCommons\Account
 * @throws DoesNotExistViolation
 *
 * @todo make sure this is used whenever possible because it is cached.
 * @todo This doesn't seem like a good place to throw a violation.
 */
function load_account(string $local_acc_id = NULL, string $given_path = NULL) : Account {
  static $fetched = [];
  if (!isset($fetched[$local_acc_id])) {
    if (strpos(needle: '/', haystack: $local_acc_id)) {
      throw new CCFailure("Can't load unresolved account name: $local_acc_id");
    }
    if ($local_acc_id and $acc = accountStore()->has($local_acc_id)) {
      $fetched[$local_acc_id] = accountStore()->fetch($local_acc_id);
    }
    else {
      throw new DoesNotExistViolation(type: 'account', id: $local_acc_id);
    }
  }
  // Sometimes an already loaded account turns out to have a relative path.
  if ($given_path) {
    $fetched[$local_acc_id]->givenPath = $given_path;
  }
  return $fetched[$local_acc_id];
}


/**
 * Get the object with all the API calls, initialised with a remote account to call.
 *
 * @param Remote $account
 *   if not provided the balance of trade of account will be used
 * @return NodeRequester|NULL
 */
function API_calls(Remote $account = NULL) {
  if (!$account) {
    if ($bot = getConfig('trunkward_acc_id')) {
      $account = load_account($bot);
    }
    else {
      return NULL;
    }
  }
  return new NodeRequester($account->url, getConfig('node_name'), $account->getLastHash());
}

/**
 * Get the library of functions for accessing ledger accounts.
 */
function accountStore() : AccountStore {
  static $accountStore;
  if (!isset($accountStore)) {
    $accountStore = AccountStore::create();
  }
  return $accountStore;
}

/**
 * Populate a json response.
 *
 * @param Response $response
 * @param stdClass|array $body
 * @param int $code
 * @return Response
 */
function json_response(Response $response, $body = NULL, int $code = 200) : Response {
  if (is_scalar($body)){
    throw new CCFailure('Illegal value passed to json_response()');
  }
  $contents = json_encode($body, JSON_UNESCAPED_UNICODE);
//debug($contents);
  $response->getBody()->write($contents);
  return $response->withStatus($code)
    ->withHeader('Access-Control-Allow-Origin', '*')
    ->withHeader('Access-Control-Allow-Methods', 'GET')
    ->withHeader('Access-Control-Allow-Headers', 'content-type, cc-user, cc-auth')
    ->withHeader('Vary', 'Origin')
    ->withHeader('Content-Type', 'application/json');
}

/**
 * Get names config items, including some items that need to be processed first.
 *
 * @staticvar array $tree
 * @staticvar array $config
 * @param string $var
 * @return mixed
 */
function getConfig(string $var) {
  static $tree, $config;
  if (!isset($tree)) {
    $config = parse_ini_file('node.ini');
    $tree = explode('/', $config['abs_path']);
  }
  if ($var == 'node_name') {
    return end($tree);
  }
  elseif ($var == 'trunkward_acc_id') {
    end($tree);
    return prev($tree);
  }
  if (strpos($var, '.')) {
    list($var, $subvar) = explode('.', $var);
    return $config[$var][$subvar];
  }
  else return $config[$var];
}

/**
 * Access control for each API method.
 *
 * Anyone can see what endpoints they can user, any authenticated user can check
 * the workflows and the connectivity of adjacent nodes. But most operations are
 * only accessible to direct members and leafward member, making this node quite
 * private with respect to the rest of the tree.
 * @param Accounts\User $user
 * @return string[]
 *   A list of the api method names the current user can access.
 * @todo make this more configurable.
 */
function permitted_operations(User $user) : array {
  $permitted[] = 'permittedEndpoints';
  if ($user->id <> '-anon-') {
    $permitted[] = 'handshake';
    $permitted[] = 'workflows';
    $permitted[] = 'newTransaction';
    $permitted[] = 'absolutePath';
    $permitted[] = 'stateChange';
    $map = [
      'filterTransactions' => 'transactions',
      'getTransaction' => 'transactions',
      'accountHistory' => 'transactions',
      'accountLimits' => 'acc_summaries',
      'accountNameFilter' => 'acc_ids',
      'accountSummary' => 'acc_summaries'
    ];
    foreach ($map as $method => $perm) {
      if (!$user instanceOf Trunkward or getConfig("priv.$perm")) {
        $permitted[] = $method;
      }
    }
    if ($user instanceof Remote) {
      $permitted[] = 'relayTransaction';
    }
  }
  return array_intersect_key(CreditCommonsInterface::OPERATIONS, array_flip($permitted));
}

function exception_error_handler( $severity, $message, $file, $line ) {
  // all warnings go the debug log AND throw an error
  throw new CCFailure("$message in $file: $line");
}


/**
 * Write a message to a debug file.
 */
function debug($val) {
  $file = \CCNode\getConfig('node_name').'.debug';
  if (!is_scalar($val)) {
    $val = print_r($val, TRUE);
  }
  $written = file_put_contents(
    $file,
    date('H:i:s')."  $val\n",
    FILE_APPEND
  );
}