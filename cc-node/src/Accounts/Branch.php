<?php

namespace CCNode\Accounts;
use CreditCommons\Account;

/**
 * Class representing an account linked to  leafwards node
 */
abstract class Branch extends Remote {

  function __construct(
    string $id,
    bool $status,
    int $min,
    int $max,
    string $url,
    string $given_path = ''
  ) {
    parent::__construct($id, $status, $min, $max, $url);
    if ($given_path) {
      $parts = explode('/', $given_path);
      $pos = array_search($this->id, $parts);
      if ($branchwards = array_slice($parts, $pos + 1)) {
        $tail = implode('/', $branchwards);
        $this->relative .= "/$tail";
      }
    }
  }

  static function create(\stdClass $data) : Account {
    if (empty($data->given_path)) {
      $data->given_path = $data->id;
    }
    static::validateFields($data);
    return new static($data->id, $data->status, $data->min, $data->max, $data->url??NULL, $data->given_path);
  }

}
