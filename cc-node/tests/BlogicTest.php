<?php

namespace CCNode\Tests;
use CCNode\AccountStore;

// the testing script is calling this from the main application root.
chdir(__DIR__.'/../BlogicService');

/**
 * Test class for the Blogic service.
 */
class BlogicTest extends TestBase {

  const SLIM_PATH = 'BlogicService/slimapp.php';
  const API_FILE_PATH = 'BlogicService/blogic.openapi.yml';

  function test_only() {
    $main_entry = '{
      "payee": "admin",
      "payer": "bob",
      "author": "admin",
      "quant": 10,
      "description": "blah blah",
      "metadata": {}
    }';
    // The defatul blogic service will add two entries to every transaction.
    // Note that it reads blogic.ini to know the payer and payee fees to apply.
    // It does not verify the account names.
    $entries = $this->sendRequest('3rdparty', 200, '', 'post', $main_entry);
    $this->assertGreaterThan(0, count($entries));
  }


}
