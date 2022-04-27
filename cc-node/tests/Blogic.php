<?php

use CCNode\AccountStore;
use CCNode\AddressResolver;
use CCNode\Accounts\Branch;
use CCNode\Accounts\User;
use CCNode\Accounts\BoT;
use CreditCommons\Exceptions\DoesNotExistViolation;


chdir(__DIR__.'/../BlogicService');

/**
 * Test class for the Blogic service.
 */
class AddressResolverTest extends TestBase {

  const SLIM_PATH = 'BlogicService/slimapp.php';
  const API_FILE_PATH = 'BlogicService/blogic.openapi.yml';

  public static function setUpBeforeClass(): void {

  }

  function test_1() {
     $options = $this->sendRequest('', 200, '<anon>', 'options');
  }


}
