<?php

namespace AccountStore;

/**
 * Class for reading and writing policy data from a csv file
 */
class UserRecord extends Record {

  /**
   * A password or API key (user accounts only)
   * @var string
   */
  public $key;

  /**
   * Whether this user is an admin
   * @var bool
   */
  public $admin;

  function __construct(\stdClass $data) {
    parent::__construct($data->id, $data->min??NULL, $data->max??NULL);
    $this->key = $data->key;
    $this->admin = (bool)@$data->admin;
  }


  function set(\stdClass $new_data) {
    if (isset($new_data->admin)) {
      $this->admin = (bool)@$new_data->admin;
    }
    parent::set($new_data);
  }


  function view() {
    $result = clone($this);
    unset($result->key);
    return $result;
  }
}
