<?php

namespace BlogicService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Business logic service
 * @note the fee will need formatting to go into the description string.
 */
global $config;
$config = parse_ini_file('blogic.ini');

$app = new App();
$app->post('/{type}', function (Request $request, Response $response, $args) {
  global $config;
  $type = $args['type']; // Not used here, but could be handy
  $content = $request->getBody();
  $entry  = json_decode($content);
  $additional = [];
  if (!empty($config['payee_fee'])) {
    $additional[] = payee_fee($entry, $config['payer_fee']);
  }
  if (!empty($config['payer_fee'])) {
    $additional[] = payer_fee($entry, $config['payer_fee']);
  }
  $additional = array_filter(
    $additional,
    function($e) {return $e->payer <> $e->payee;}
  );

  $response->getBody()->write(json_encode(array_values($additional)));
  return $response->withHeader('Content-Type', 'application/json');;
});

return $app;

/**
 * Charge the payee.
 */
function payee_fee(\stdClass $entry, $fee) : \stdClass {
  global $config;
  // Might want to author with the authenticaed account rather than $fees account
  $fee = ceil(calc($entry->quant, $fee));
  return (object)[
    'payer' => $entry->payee,
    'payee' => $config['fees_account'],
    'author' => $config['fees_account'],
    'quant' => $fee,
    'description' => $_SERVER['SERVER_NAME']. " payee fee of $fee to ".$config['fees_account']
  ];
}

/**
 * Charge the payer.
 */
function payer_fee(\stdClass $entry, $fee) : \stdClass {
  global $config;
  $fee = ceil(calc($entry->quant, $fee));
  // Might want to author with the authenticated account rather than $fees account
  return (object)[
    'payer' => $entry->payer,
    'payee' => $config['fees_account'],
    'author' => $config['fees_account'],
    'quant' => $fee,
    'description' => $_SERVER['SERVER_NAME']." payer fee of $fee to ".$config['fees_account']
  ];
}

/**
 *
 * @param int $quant
 * @param float $fee
 * @return int
 */
function calc(int $quant, string $fee) : float {
  // the setting is a fix num of units or a percent.
  preg_match('/([0-9.])(%?)/', $fee, $matches);
  $num = $matches[1];
  $percent = $matches[2];
  if ($percent) {
    $val = $quant * $num/100;
  }
  else {
    $val =  $num;
  }
  return $val;
}
