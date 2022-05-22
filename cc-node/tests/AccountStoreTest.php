<?php

namespace CCNode\Tests;

/**
 * Test class for the AccountStore service.
 */
class AccountStoreTest extends TestBase {

  const SLIM_PATH = 'AccountStore/slimapp.php';
  const API_FILE_PATH = 'AccountStore/accountstore.openapi.yml';

  function __construct() {
    global $config;
    parent::__construct();
    require_once __DIR__.'/../slimapp.php';
    $node_name = \CCNode\getConfig('node_name');
    chdir(__DIR__.'/../AccountStore');
    $this->loadAccounts('');
  }

  function testLogin() {
    $name = reset($this->normalAccIds);
    $pass = $this->passwords[$name];
    $this->sendRequest("creds/$name/z!<", 400);
    $this->sendRequest("creds/$name/$pass", 200);
  }

  function testFilterName() {
    $all_accounts = array_merge($this->normalAccIds, $this->branchAccIds, $this->adminAccIds, [$this->trunkwardId]);

    $this->filterTest('', array_filter($all_accounts));// because trunkwardId might be empty
    $this->filterTest('local=true', array_merge($this->normalAccIds, $this->adminAccIds));
    $char = substr(reset($this->normalAccIds), 0, 1);
    $expected = array_filter(
      array_keys($this->rawAccounts),
      function ($acc) use ($char) {return stripos($acc, $char) !== FALSE;}
    );
    $this->filterTest("fragment=$char", $expected);
  }

  function testGetAccount() {
    $name = key($this->rawAccounts);
    $this->sendRequest("$name", 200);
  }


  function filterTest($queryString, $expected) {
    $names = $this->sendRequest("filter?$queryString", 200);
    $objs = $this->sendRequest("filter/full?$queryString", 200);
    $names_full = array_map(function ($a){return $a->id; }, $objs);

    sort ($names);
    sort ($names_full);
    sort ($expected);
    $this->assertEquals($expected, $names);
    $this->assertEquals($expected, $names_full);
  }

}
