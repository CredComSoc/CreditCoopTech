<?php
/**
 * Reference implementation of a credit commons node
 */

declare(strict_types=1);

require_once './vendor/autoload.php';
ini_set('html_errors', '0');
$config = parse_ini_file('./node.ini');
if (\CCNode\Db::connect($config['db']['name'], $config['db']['user'], $config['db']['pass'], $config['db']['server'])->connect_error) {
  header('Location: config/index.php');
}
if ($config['dev_mode']){
  $node_name = array_pop(explode('/', $config['abs_path']));
  //file_put_contents($node_name.'.debug', '');
  file_put_contents('last_exception.log', '');// server may not be able to recreate the file.
  file_put_contents('error.log', '');// server may not be able to recreate the file.
}

//  Simpletest needs to be able to call $app->run() itself.
require './slimapp.php';

//Middleware to do logging (not needed for testing)
$app->add(function (Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Message\ResponseInterface $response, callable $next) {
  $response = $next($request, $response);
  $method = $request->getMethod();
  $uri = $request->getUri();
  $path = $uri->getPath();
  if ($params = $uri->getQuery()) {
    $path .= '?'.$params;
  }
  $headers = array_map(function ($val){return $val[0];}, $request->getHeaders());
  $request_headers = implode("\n", $headers);
  $request_body = $request->getBody()->getContents();
  $request->getBody()->rewind();
  $response_code = $response->getStatusCode();
  $response_body = $response->getBody()->getContents();
  $response->getBody()->rewind();
  $query = "INSERT INTO log (http_method, path, request_headers, request_body, response_code, response_body) "
  . "VALUES ('$method', '$path', '$request_headers', '$request_body', $response_code,'$response_body');";
  \CCNode\Db::query($query);
  return $response;
});
$app->run();
