<?php

namespace CreditCommons\Leaf;

use CreditCommons\RestAPI;
use GuzzleHttp\RequestOptions;

/**
 * Class for a non-ledger client to call to a credit commons accounting node.
 * This wraps the RestAPI in order to handle the authentication and errors
 * appropriate for the client.
 * Clients should instantiate this
 */
abstract class API extends RestAPI {

  /**
   * {@inheritdoc}
   */
  function __construct($downstream_node_url, $user, $pass) {
    $this->baseUrl = $downstream_node_url;
    // todo put this in the client base class.
    $this->options[RequestOptions::HEADERS]['CC-user'] = $user;
    $this->options[RequestOptions::HEADERS]['CC-auth'] = $pass;
  }

}
