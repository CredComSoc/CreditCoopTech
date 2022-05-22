<?php

namespace CCNode\Accounts;
use CCNode\Accounts\User;

/**
 * Class representing an account linked to  leafwards node
 */
abstract class Branch extends Remote {

  function __construct(
    string $id,
    int $min,
    int $max,
    string $url,
    string $given_path = ''
  ) {
    parent::__construct($id, $min, $max, $url);
    $parts = explode('/', $given_path);
    $pos = array_search($this->id, $parts);
    if ($leafwards = array_slice($parts, $pos + 1)) {
      $tail = implode('/', $leafwards);
      $this->relative .= "/$tail";
    }
  }

  static function create(\stdClass $data) : User {
    if (empty($data->given_path)) {
      $data->given_path = $data->id;
    }
    static::validateFields($data);
    return new static($data->id, $data->min, $data->max, $data->url??NULL, $data->given_path);
  }

}
