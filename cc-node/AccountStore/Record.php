<?php

namespace AccountStore;

/**
 * Class for reading and writing policy data from a csv file
 */
abstract class Record {

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
   * Set the record to the new values.
   * Don't forget to do accountManager->save() afterwards.
   * @param \stdClass $new_data
   */
  function set(\stdClass $new_data) {
    $this->min = (int)$new_data->min;
    $this->max = (int)$new_data->max;
  }

  abstract function view();

}
