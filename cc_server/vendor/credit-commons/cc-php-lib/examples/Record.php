<?php

namespace Examples;

/**
 * Class for reading account data stored in accountstore.json
 */
abstract class Record implements \JsonSerializable {

  /**
   * The unique name of the account
   * @var string
   */
  public string $id;

  /**
   * @var int|null
   */
  public int $min;

  /**
   * @var int|null
   */
  public int $max;

  function __construct(string $id, int $min, int $max) {
    $this->id = $id;
    $this->min = $min;
    $this->max = $max;
  }

  /**
   * Remove the auth before sending account details anywhere.
   */
  function jsonSerialize() : mixed {
    $arr = (array)$this;
    unset($arr['key']);
    return $arr;
  }

  function asObj() {
    $data = new \stdClass;
    foreach ((array)$this as $key => $val) {
      $data->{$key} = $val;
    }
    return $data;
  }
}
