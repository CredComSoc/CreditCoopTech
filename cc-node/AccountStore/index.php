<?php
namespace AccountStore;
use AccountStore\AccountManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use Slim\App;

/**
 * AccountStore service. For reference only
 * Normally this service would be replaced by a wrapper around the main user database.
 * This implementation uses a simple json file to store the users.
 */

require_once '../vendor/autoload.php';
$app = new App();

$app->get('/filter/full', function (Request $request, Response $response, $args) {
  $accounts = account_store_filter($request->getQueryParams());
  $result = $accounts->view(); // array
  $response->getBody()->write(json_encode($result));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/filter', function (Request $request, Response $response, $args) {
  $accounts = account_store_filter($request->getQueryParams());
  $result = array_keys($accounts->accounts); // array
  $response->getBody()->write(json_encode($result));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/{acc_id}', function (Request $request, Response $response, $args) {
  $accounts = new AccountManager();
  if ($accounts->has($args['acc_id'])) {
    $account = $accounts[$args['acc_id']]->view();
    $response->getBody()->write(json_encode($account));
  }
  else{
    return $response->withStatus(404);
    // This might be more elegant, but the class isn't available it seems.
    throw new NotFoundException();
  }
  return $response->withHeader('Content-Type', 'application/json');
});

$app->run();


function account_store_filter($params) : AccountManager {
  $accounts = new AccountManager();
  if (!empty($params['chars'])) {
    $accounts->filterByName($params['chars']);
  }
  if (count($accounts) == 1 and !empty($params['auth'])) {
    //prevents getting a list of all users with a given auth string.
    $accounts->filterByAuth($params['auth']);
  }
  if (!empty($params['status'])) {
    $accounts->filterByStatus((bool)$params['status']);
  }
  if (!empty($params['local'])) {
    $accounts->filterByLocal((bool)$params['local']);
  }
  return $accounts;
}
