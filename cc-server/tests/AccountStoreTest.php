<?php

namespace AccountStore\Tests;

use PHPUnit\Framework\TestCase;
use League\OpenAPIValidation\PSR15\ValidationMiddlewareBuilder;
use League\OpenAPIValidation\PSR15\SlimAdapter;
use Slim\Psr7\Response;

/**
 * Test that the accountstore is providing consistent results, but without checking
 * the original account data because we don't knew where or how it is stored.
 */
class AccountStoreTest extends TestCase {

  const SLIM_PATH = 'slimapp.php';
  const API_FILE_PATH = 'accountstore.openapi.yml';

  private array $allAccs;
  private array $admins;
  private array $normies;
  private array $local;
  private array $remote;
  private \Slim\App $app;


  function loadMems() {
    static $mems;
    if (!$mems) {
      // Get the full list users and sort between admins and normies.
      $this->allAccs = $this->sendRequest("filter/full", 200);
      // expect at least 3 names, at least one admin
      $this->assertGreaterThan(2, count($this->allAccs));
      $this->admins = array_filter(
        $this->allAccs,
        function ($o){return $o->admin == 1;}
      );
      $this->normies = array_filter(
        $this->allAccs,
        function ($o){return $o->admin == 0;}
      );
      $this->local = array_filter(
        $this->allAccs,
        function ($o){return empty($o->url);}
      );
      $this->remote = array_filter(
        $this->allAccs,
        function ($o){return !empty($o->url);}
      );
    }
  }

  function testGetAccount() {
    $this->loadMems();
    $name = key($this->allAccs);
    $this->sendRequest(reset($this->normies)->id, 200);
  }

  function testBalances() {
    $this->loadMems();
    foreach ($this->allAccs as $acc) {
      $this->assertObjectHasAttribute('max', $acc);
      $this->assertObjectHasAttribute('min', $acc);
      if (!empty($acc->max)) {
        $this->assertIsInt($acc->max);
        $this->assertGreaterThan(0, $acc->max);
      }
      if (!empty($acc->min)) {
        $this->assertIsInt($acc->min);
        $this->assertLessThanOrEqual(0, $acc->min);
      }
    }
  }

  function testLogin() {
    $this->loadMems();
    $name = reset($this->normies);
    $this->sendRequest("creds/$name->id/z!<", 400);
    // We can't know what the passwords actually are.
  }

  function testFilters() {
    $this->loadMems();
    $this->filterTest("local=true", $this->local);
    $this->filterTest("local=false", $this->remote);
    $this->filterTest("admin=true", $this->admins);
    $this->filterTest("admin=false", $this->normies);
  }

  function testAutocomplete() {
    $this->loadMems();
    $all_accounts = array_merge($this->allAccs);

    $this->filterTest('local=true', $this->local);
    $char = substr(reset($this->normies)->id, 0, 1);
    $expected = array_filter(
      $this->allAccs,
      function ($acc) use ($char) {return stripos($acc->id, $char) !== FALSE;}
    );
    $this->filterTest("fragment=$char", $expected);
  }


  /**
   *
   * @param string $queryString
   * @param stdClass[] $expected
   */
  function filterTest(string $queryString, array $expected) {
    $names = $this->sendRequest("filter?$queryString", 200);
    sort($names);
    $expected_names = array_map(function($acc){return $acc->id;}, $expected);
    sort($expected_names);
    $this->assertEquals($expected_names, $names);

    $names_full = $this->sendRequest("filter/full?$queryString", 200);
    sort($names_full);
    sort($expected);
    $this->assertEquals($expected, $names_full);
  }



  /**
   * @staticvar \Slim\App $app
   * @return \Slim\App
   */
  protected function getApp(): \Slim\App {
    static $app;
    if (!$app) {
      $app = require_once static::SLIM_PATH;
      if (static::API_FILE_PATH) {
        $spec = file_get_contents(static::API_FILE_PATH);
        $psr15Middleware = (new ValidationMiddlewareBuilder)
          ->fromYaml($spec)
          ->getValidationMiddleware();
        $middleware = new SlimAdapter($psr15Middleware);
        $app->add($middleware);
      }
    }
    return $app;
  }

  /**
   *
   * @param string $path
   * @param int $expected_response
   * @return \stdClass|NULL|array
   */
  protected function sendRequest(string $path, int $expected_response) : \stdClass|NULL|array {
    if ($query = strstr($path, '?')) {
      $path = strstr($path, '?', TRUE);
      parse_str(substr($query, 1), $params);
    }

    $request = (new \Nyholm\Psr7\Factory\Psr17Factory())->createServerRequest('GET', '/'.$path);
    if (isset($params)) {
      $request = $request->withQueryParams($params);
    }
    $response = $this->getApp()->process($request, new Response());
    $contents = json_decode(strval($response->getBody()));
    $status_code = $response->getStatusCode();
    if ($status_code <> $expected_response) {
      // Blurt out to terminal to ensure all info is captured.
      echo "\n $acc_id got unexpected code ".$status_code." on $path: ".print_r($contents, 1);
    }
    $this->assertEquals($expected_response, $status_code);

    return $contents;
  }


}
