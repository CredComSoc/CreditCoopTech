<?php

namespace Examples;

/**
 * Class for a remote account.
 */
class RemoteRecord extends Record {

  /**
   * The url of the remote node (remote accounts only)
   * @var string
   */
  public $url;

  function __construct(\stdClass $data) {
    parent::__construct($data->id, $data->min??NULL, $data->max??NULL);
    $this->url = $data->url;
    $this->admin = FALSE;
    // This is need only for spoof users.
    if (isset($data->key)) {
      $this->key = $data->key;
    }
  }


}
