<?php
namespace CCServer;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class DecorateResponse {

  public function __invoke(Request $request, Response $response, callable $next) : Response {
    $response = $next($request, $response);
    // Do these headers just apply to the OPTIONS requests?
    return $response
      ->withHeader('Access-Control-Allow-Origin', '*')
      ->withHeader('Access-Control-Allow-Methods', 'GET, PATCH')
      ->withHeader('Access-Control-Allow-Headers', 'content-type, cc-user, cc-auth')
      ->withHeader('Vary', 'Origin')
      ->withHeader('Content-Type', 'application/json');
  }

}
