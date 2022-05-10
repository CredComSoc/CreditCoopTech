<?php

namespace AccountStore;

/**
 * Class for reading and writing policy data from a csv file
 */
class RemoteRecord extends Record {

  /**
   * The url of the remote node (remote accounts only)
   * @var string
   */
  public $url;

  function __construct(\stdClass $data) {
    parent::__construct($data->id, $data->status, $data->min??NULL, $data->max??NULL);
    $this->url = $url;
  }


  function view() {
    return $this;
  }
}
