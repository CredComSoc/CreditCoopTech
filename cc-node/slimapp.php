<?php
/**
 * Reference implementation of a credit commons node
 *
 *
 * @todo find a way to notify the user if the trunkward node is offline.
 * @todo all calls to RestAPI() must be wrapped in try{}
 *
 */
namespace CCNode;

use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\CCError;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CreditCommons\Exceptions\HashMismatchFailure;
use CreditCommons\Exceptions\PermissionViolation;
use CreditCommons\Exceptions\AuthViolation;
use CreditCommons\CreditCommonsInterface;
use CreditCommons\AccountRemote;
use CreditCommons\RestAPI;
use CreditCommons\Account;
use CCNode\Accounts\BoT;
use CCNode\Workflows;
use CCNode\StandaloneEntry;
use CCNode\NewTransaction;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

$config = parse_ini_file('./node.ini');
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
//      $exception = new CCFailure([
//        'message' => $exception->getMessage()
//      ]);
//    }
//    $response->getBody()->write(json_encode($exception, JSON_UNESCAPED_UNICODE));
//    return $response->withStatus($exception->getCode());
//});

$app = new App();
$c = $app->getContainer();
$c['errorHandler'] = function ($c) {
  return new Slim3ErrorHandler();
};
$c['phpErrorHandler'] = function ($c) {
  return new Slim3ErrorHandler();
};

/**
 * Middleware to add the name of the current node to every response header.
 */
$app->add(function ($request, $response, $next) {
  global $config;
  $response = $next($request, $response);
	return $response->withHeader('Node-path', absolute_path());
});

/**
 * Default HTML page. (Not part of the API)
 */
$app->get('/', function (Request $request, Response $response) {
  $response->getBody()->write('It works!');
  return $response->withHeader('Content-Type', 'text/html');
});

/**
 * Implement the Credit Commons API methods
 */
$app->options('/', function (Request $request, Response $response) {
  // No access control
  check_permission($request, 'permittedEndpoints');
  return json_response($response, permitted_operations());
});

// @todo this isn't documented or implemented???
$app->get('/trunkwards', function (Request $request, Response $response) {
  check_permission($request, 'trunkwardNodes');
  $node_names = API_calls()->getTrunkwardNodeNames();
  return json_response($response, $node_names, 200);
});

$app->get('/workflows', function (Request $request, Response $response) {
  check_permission($request, 'workflows');
  // Todo need to instantiate workflows with the BoT requester if there is one.
  return json_response($response, (new Workflows())->loadAll());
});

$app->get('/handshake', function (Request $request, Response $response) {
  global $orientation, $config;
  check_permission($request, 'handshake');
  return json_response($response, $orientation->handshake());
});

$app->get('/accounts[/{fragment}]', function (Request $request, Response $response, $args) {
  check_permission($request, 'accountNameAutocomplete');
  $params = $request->getQueryParams();
  $remote_names = [];
  if (!empty($config['bot']['acc_id'])) {// $orientation might be cleaner
    //@todo pass this to the parent ledger
    throw new CCFailure(message: 'accounts/{fragment} not implemented for ledger tree.');
    $remote_names = API_calls()->accounts(@$args['fragment'], TRUE);
    // @todo Also we may want to query child ledgers.
  }
  $local_names = accountStore()->filter(['chars' => @$args['fragment'], 'status' => TRUE, 'local' => TRUE], FALSE);
  return json_response($response, array_slice(array_merge($local_names, $remote_names), 0, $params['limit']??10));
});

$app->get('/account/limits/{acc_id}', function (Request $request, Response $response, $args) {
  check_permission($request, 'accountHistory');
  $account = accountStore()->fetch($args['acc_id']);
  return json_response($response, (object)['min' => $account->min, 'max' => $account->max]);
});


$app->get('/account/summary/{acc_path}', function (Request $request, Response $response, $args) {
  global $orientation;
  check_permission($request, 'accountSummary');
  $params = $request->getQueryParams();
  $account = accountStore()->fetch($args['acc_path']);
  $result = (new Wallet($account))->getTradeStats();
  $orientation->responseMode = TRUE;
  $response->getBody()->write(json_encode($result));
  return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/account/history/{acc_path}', function (Request $request, Response $response, $args) {
  global $orientation;
  check_permission($request, 'accountHistory');
  $params = $request->getQueryParams();
  $account = accountStore()->fetch($args['acc_path']);
  $result = (new Wallet($account))->getHistory($params['samples']??0);

  $orientation->responseMode = TRUE;
  $response->getBody()->write(json_encode($result));
  return $response->withHeader('Content-Type', 'application/json');
});

// Create a new transaction
$app->post('/transaction', function (Request $request, Response $response) {
  global $orientation, $user;
  check_permission($request, 'newTransaction');
  $request->getBody()->rewind(); // ValidationMiddleware leaves this at the end.
  $data = json_decode($request->getBody()->getContents());
  // validate the input and create UUID
  NewTransaction::prepareClientInput($data);
  $transaction = Transaction::createFromUpstream($data); // in state 'init'
  // Validate the transaction in its workflow's 'creation' state
  $transaction->buildValidate();
  $orientation->responseMode = TRUE;
  $workflow = (new Workflows())->get($transaction->type);
  if ($workflow->creation->confirm) {
    // Send the transaction back to the user to confirm.
    $transaction->saveNewVersion();
    $status_code = 200;
  }
  else {
    // Write the transaction immediately to its 'creation' state
    $transaction->state = $workflow->creation->state;
    $transaction->version = 0;
    $transaction->saveNewVersion();
    $status_code = 201;
  }
  return json_response($response, $transaction, $status_code);
});

$app->post('/transaction/relay', function (Request $request, Response $response) {
  global $orientation;
  check_permission($request, 'relayTransaction');
  $request->getBody()->rewind(); // ValidationMiddleware leaves this at the end.
  $data = json_decode($request->getBody()->read());
  $transaction = TransversalTransaction::createFromUpstream($data);
  $transaction->buildValidate();
  $transaction->saveNewVersion();
  $orientation->responseMode = TRUE;
  return json_response($response, $transaction, 201);
});

$uuid_regex = '[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}';

$app->patch('/transaction/{uuid:'.$uuid_regex.'}/{dest_state}', function (Request $request, Response $response, $args) {
  check_permission($request, 'stateChange');
  global $orientation;
  $uuid = $args['uuid'];
  $transaction = Transaction::loadByUuid($uuid);
  // Ensure that transversal transactions are being manipulated only from their
  // end points, not an intermediate ledger
  if (!$orientation->upstreamAccount and !empty($transaction->payer->url) and !empty($transaction->payee->url)) {
    throw new IntermediateLedgerViolation();
  }
  $transaction->changeState($args['dest_state']);
  return $response->withStatus(201);
});
// Retrieve one transaction
$app->get('/transaction/{uuid:'.$uuid_regex.'}/{format}', function (Request $request, Response $response, $args) {
  global $orientation;
  check_permission($request, 'getTransaction');
  if ($args['format'] == 'entry') {
    $result = StandaloneEntry::loadByUuid($args['uuid']);
  }
  else {// format = full
    $result = Transaction::loadByUuid($args['uuid']);
  }
  $orientation->responseMode = TRUE;
  return json_response($response, $result, 200);
});

$app->get('/transaction', function (Request $request, Response $response) {
  global $orientation;
  check_permission($request, 'filterTransactions');
  $params = $request->getQueryParams();
  $uuids = Transaction::filter($params);
  // return uuids or entry ids?
  return json_response($response, $uuids, 200);
});


return $app;

/**
 * Load an account from the accountStore.
 *
 * @staticvar array $fetched
 * @param string $id
 *   The account id or empty string to load a dummy account.
 * @return CreditCommons\Account
 * @throws DoesNotExistViolation
 *
 * @todo make sure this is used whenever possible because it is cached.
 * @todo This doesn't seem like a good place to throw a violation.
 */
function load_account(string $id) : Account {
  static $fetched = [];
  if (!isset($fetched[$id])) {
    if ($id == '<anon>') {
      $fetched[$id] = \CCNode\Accounts\User::AnonAccount();
    }
    elseif ($id and $acc = accountStore()->fetch($id)) {
      $fetched[$id] = $acc;
    }
    else {
      throw new \DoesNotExistViolation(type: account, id: $id);
    }
  }
  return $fetched[$id];
}

function check_permission(Request $request, string $operationId) {
  authenticate($request); // This sets $user
  global $user, $orientation;
  $orientation = new Orientation();

  $permitted = \CCNode\permitted_operations();
  if (!in_array($operationId, array_keys($permitted))) {
    throw new PermissionViolation(
      acc_id: $user->id?:'<anon>',
      method: $request->getMethod(),
      endpoint: $request->getRequestTarget()
    );
  }
}

/**
 * Access control for each API method.
 *
 * Anyone can see what endpoints they can user, any authenticated user can check
 * the workflows and the connectivity of adjacent nodes. But most operations are
 * only accessible to direct members and leafward member, making this node quite
 * private with respect to the rest of the tree.
 * @global type $user
 * @return string[]
 *   A list of the api method names the current user can access.
 */
function permitted_operations() : array {
  global $user;
  $data = CreditCommonsInterface::OPERATIONS;
  $has_BoT = (bool)API_calls();
  $permitted[] = 'permittedEndpoints';
  if ($user->id <> '<anon>') {
    $permitted[] = 'handshake';
    $permitted[] = 'workflows';
    if (!$user instanceof BoT) {
      // This is the default privacy setting; leafward nodes are private to trunkward nodes
      if ($has_BoT) {
        $permitted[] = 'trunkwardNodes';
      }
      $permitted[] = 'accountHistory';
      $permitted[] = 'accountLimits';
      $permitted[] = 'accountNameAutocomplete';
      $permitted[] = 'accountSummary';
      $permitted[] = 'newTransaction';
      $permitted[] = 'getTransaction';
      $permitted[] = 'filterTransactions';
      $permitted[] = 'stateChange';
    }
    if ($user instanceof Remote) {
      $permitted[] = 'relayTransaction';
    }
  }
  return array_intersect_key(CreditCommonsInterface::OPERATIONS, array_flip($permitted));
}

/**
 * Taking the user id and auth key from the header and comparing with the database. If the id is of a remote account, compare the extra
 * @global array $config
 * @global stdClass $user
 * @param Request $request
 * @return void
 * @throws DoesNotExistViolation|HashMismatchFailure|AuthViolation
 */
function authenticate(Request $request) : void {
  global $config, $user;
  $user = load_account('<anon>'); // Anon
  if ($request->hasHeader('cc-user') and $request->hasHeader('cc-auth')) {
    $acc_id = $request->getHeaderLine('cc-user');
    $auth = $request->getHeaderLine('cc-auth');
    // Users connect with an API key which can compared directly with the database.
    if ($acc_id) {
      $user = load_account($acc_id);
      if ($user instanceOf Remote) {
        // Remote nodes connect with a hash of the connected account, which needs to be compared.
        $query = "SELECT TRUE FROM hash_history WHERE acc = '$account->id' AND hash = '$auth' ORDER BY id DESC LIMIT 0, 1";
        $result = Db::query($query)->fetch_row();
        if ($hash && !$result or !$hash && $result) {
          throw new HashMismatchFailure(otherNode: $acc_id);
        }
      }
      elseif (!accountStore()->filter(['chars' => $acc_id, 'auth' => $auth])) {
        //local user with the wrong password
        throw new AuthViolation(acc_id: $acc_id);
      }
    }
    else {
      // Blank username supplied, fallback to anon
    }
  }
  else {
    // No attempt to authenticate, fallback to anon
  }
}

/**
 * Get the object with all the API calls, initialised with a remote account to call.
 *
 * @param Remote $account
 *   if not provided the balance of trade of account will be used
 * @return RestAPI|NULL
 */
function API_calls(AccountRemote $account = NULL) {
  global $config;
  if (!$account) {
    if ($bot = $config['bot']['acc_id']) {
      $account = load_account($bot);
    }
    else {
      return NULL;
    }
  }
  return new RestAPI($account->url, $config['node_name'], $account->getLastHash());
}

/**
 * Get the library of functions for accessing ledger accounts.
 */
function accountStore() : AccountStore {
  global $config;
  return new AccountStore($config['account_store_url']);
}

/**
 * Convert all errors into an stdClass, which includes a field showing
 * which node caused the error
 */
class Slim3ErrorHandler {
  /**
   * Probably all errors and warnings should include an emergency SMS to admin.
   * This callback is also used by the ValidationMiddleware.
   * @note The task is made complicated because the $exception->message property is
   * protected and is lost during json_encode
   */
  public function __invoke($request, $response, $exception) {
    global $config;
    $exception_class = explode('\\', get_class($exception));
    $exception_class = array_pop($exception_class);
    if (!$exception instanceOf CCError) {
      $code = 500;
      $trace = $exception->getTrace(); //experimental;
      $exception_class = 'CCFailure';
      $output = (object)[
        'message' => $exception->getMessage()?:$exception_class
      ];
      // Just show the last error.
      while ($exception = $exception->getPrevious()) {
        $output = (object)[
          'message' => $exception->getMessage()?:$exception_class
        ];
        //if (get_class($exception) == 'League\OpenAPIValidation\PSR7\Exception\NoResponseCode') break;
      }
      $output->trace = $trace;
    }
    elseif (in_array($exception_class, ['CCViolation', 'CCFailure'])) {
      $output = (object)[
        'message' => $exception->getMessage()
      ];
    }
    else {
      $output = (object)($exception);
    }
    $output->node = $config['node_name'];
    $output->class = $exception_class;
    // this bypasses the middleware, so need to do this again.
	  $response = $response->withHeader('Node-path', absolute_path());
    return json_response($response, $output, $code??$exception->getCode());
   }
}

/**
 * Populate a json response.
 *
 * @param Response $response
 * @param stdClass|array $body
 * @param int $code
 * @return Response
 */
function json_response(Response $response, $body, int $code = 200) : Response {
  if (is_scalar($body)){
    throw new CCFailure('Illegal value passed to json_response()');
  }
  $response->getBody()->write(json_encode($body, JSON_UNESCAPED_UNICODE));
  return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
}

/**
 * Get the path of the trunkwards node and append the current node name
 * @global array $config
 * @return string
 *   The absolute path from trunkwards to leafwards
 *
 * @todo this MUST be cached somewhere before multilevel deployment
 */
function absolute_path() : string {
  global $config;
  // for now we'll just use the BoT account name, This will serve for a 2 level
  // tree, but in future we'll need to cache somwehere the response header from each BOT request.
  if (!empty($config['bot']['acc_id'])) {
    $path[] = $config['bot']['acc_id'];
  }
  $path[] = $config['node_name'];
  return implode('/', $path);
}
