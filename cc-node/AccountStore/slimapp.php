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
$app = new App();

$app->get('/creds/{name}/{auth}', function (Request $request, Response $response, $args) {
  $accounts = new AccountManager();
  $name = $args['name'];
  $auth = $args['auth'];
  if ($accounts[$name]->key == $auth) {
    return $response->withStatus(200);
  }
  return $response->withStatus(400);
});

$app->get('/filter/full', function (Request $request, Response $response, $args) {
  $accounts = account_store_filter(...$request->getQueryParams());
  $response->getBody()->write(json_encode($accounts->view(), JSON_UNESCAPED_UNICODE));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/filter', function (Request $request, Response $response, $args) {
  $accounts = account_store_filter(...$request->getQueryParams());
  $response->getBody()->write(json_encode(array_keys($accounts->accounts), JSON_UNESCAPED_UNICODE));
  return $response->withHeader('Content-Type', 'application/json');
});

//see https://discourse.slimframework.com/t/http-head-request/473
$app->map(['HEAD'], '/{acc_id}', function (Request $request, Response $response, $args) {
  $accounts = new AccountManager();
  if (!$accounts->has($args['acc_id'])) {
    return $response = $response->withStatus(404);
    // This might be more elegant, but the class isn't available it seems.
    throw new NotFoundException();
  }
  return $response;
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

return $app;


function account_store_filter(...$params) : AccountManager {
  $accounts = new AccountManager();
  if (!empty($params['fragment'])) {
    $accounts->filterByName($params['fragment']);
  }
  if (isset($params['local'])) {
    $local = $params['local'] == 'true' ? TRUE : FALSE;
    $accounts->filterByLocal($local);
  }
  return $accounts;
}
