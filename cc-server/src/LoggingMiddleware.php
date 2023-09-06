<?php
namespace CCServer;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CCNode\Db;


class LoggingMiddleware {

  public function __invoke(Request $request, Response $response, callable $next) : Response {
    global $cc_config;
    if ($cc_config->devMode){
      // server may not be able to recreate the file.
      file_put_contents('last_exception.log', '');
      file_put_contents('error.log', '');
    }

    $method = $request->getMethod();
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($params = $uri->getQuery()) {
      $path .= '?'.$params;
    }
    $headers = array_map(function ($val){return $val[0];}, $request->getHeaders());
    $request_headers = http_build_query($headers, NULL, "\n");
    $request_body = strval($request->getBody()->getContents());

    $query = "INSERT INTO log (method, path, request_headers, request_body) "
    . "VALUES ('$method', '$path', '$request_headers', '$request_body');";
    $last_id = Db::query($query);
    $response = $next($request, $response);

    $response_code = $response->getStatusCode();
    $body = $response->getBody();
    $body->rewind();
    $response_body = mysqli_real_escape_string(Db::connect(), $body->getContents());
    $body->rewind();
    $query = "UPDATE log "
      . "SET response_code = '$response_code', response_body = \"$response_body\" "
      . "WHERE id = $last_id";
    Db::query($query);

    return $response;


  }


}
