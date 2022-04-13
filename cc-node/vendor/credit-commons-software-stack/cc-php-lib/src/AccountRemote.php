<?php

namespace CreditCommons;

/**
 *
 */
abstract class AccountRemote extends Account {

  function __construct(
    public string $id,
    public bool $status,
    public int $min,
    public int $max,
    public string $url
   ) {
    parent::__construct($id, $status, $min, $max);
  }


  abstract function getLastHash() : string;


}
