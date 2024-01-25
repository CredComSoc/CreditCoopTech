<?php

namespace Examples;

/**
 * Class for a local (leaf) account.
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
    $this->admin = !empty($data->admin);
  }

}
