<?php

namespace CreditCommons;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use CreditCommons\Exceptions\CCFailure;

/**
 * Thin wrapper around Guzzle
 *
 * @note this is mainly used by RestAPI, but also by AccountStore.
 */
abstract class Requester {

  public $method = 'get';
  public $baseUrl;
  private $fields = [];


  function __construct(string $base_url) {
    if (substr($base_url, 0, 4) != 'http') {
      throw new CCFailure('Invalid url for downstream node: '.$base_url);
    }
    $this->baseUrl = $base_url;
  }


  /**
   * {@inheritDoc}
   */
  protected function request(int $required_code, string $endpoint = '') {
    $this->options[RequestOptions::HEADERS]['Accept'] = 'application/json';
    $client = new Client(['base_uri' => $this->baseUrl, 'timeout' => 1]);
//echo strtoupper($this->method) . " $this->baseUrl/$endpoint";print_r($this->options);
    $response = $client->{$this->method}($endpoint, $this->options);
    // reset the object in case it is used again.
    // In case of an error being thrown above, assume the object doesn't need resetting.
    $this->fields = [];
    $this->options = [];
    $this->method = 'get';

    $raw_result = $response->getBody()->getContents();
    if ($raw_result and $response->getHeaderline('Content-Type') != 'application/json') {
      // @todo are ALL responses Json or are some null or some text?
      throw new \Exception("Non-json response: '$raw_result'");
    }
    if ($response->getStatusCode() == $required_code) {
      // All requests are for json objects.
      return json_decode($raw_result);
    }
    throw new \Exception($response->getStatusCode() . " Unexpected result from $this->baseUrl/$endpoint");
  }


  /**
   * @param string $method
   * @return RestAPI
   */
  protected function setMethod($method) {
    $this->method = $method;
    return $this;
  }

  /**
   * @param mixed $body
   * @return RestAPI
   */
  public function setBody($body) {
    if ($content = json_encode($body)) {
      $this->options[RequestOptions::HEADERS]['Content-Type'] = 'application/json';
      $this->options[RequestOptions::HEADERS]['Content-Length']= strlen($content);
      $this->options[RequestOptions::BODY] = $content;
    }
    else {
      throw new \Exception('Request body was empty or could not be encoded into json.');
    }
    return $this;
  }

}
