<?php

namespace CreditCommons;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use CreditCommons\Exceptions\CCFailure;

/**
 * Thin wrapper around Guzzle
 *
 * @note this is mainly used by NodeRequester, but also by AccountStore.
 */
abstract class Requester {

  public $method = 'get';
  public $baseUrl;
  private $fields = [];
  protected $options = [];

  /**
   *
   * @param string $base_url
   * @throws CCFailure
   */
  function __construct(string $base_url) {
    if (substr($base_url, 0, 4) != 'http') {
      throw new CCFailure('Invalid url for downstream node: '.$base_url);
    }
    $this->baseUrl = $base_url;
    $this->setHeader('Accept','application/json');
  }

  /**
   * {@inheritDoc}
   */
  protected function request(string $endpoint = '/') : \stdClass|NULL {
    $params = ['base_uri' => $this->baseUrl, 'timeout' => 1];
    $client = new Client($params);
    $response = $client->{$this->method}($endpoint, $this->options);
    // Reset the object in case it is used again.
    // In case of an error being thrown above, assume the object doesn't need resetting.
    $this->fields = [];
    $this->options = [];
    $this->method = 'get';
    $raw_result = strval($response->getBody());
    $status = $response->getStatusCode();
    if ($raw_result and $response->getHeaderline('Content-Type') != 'application/json') {
      throw new \Exception("Non-json response: '$raw_result'");
    }
    if ($status == 200 or $status == 201) {
      // All requests are for json objects.
      return json_decode($raw_result);
    }
    throw new \Exception($response->getStatusCode() . " Unexpected result from $this->baseUrl/$endpoint");
  }


  /**
   * @param string $method
   * @return NodeRequester
   */
  protected function setMethod($method) {
    $this->method = $method;
    return $this;
  }

  /**
   * @param mixed $body
   * @return NodeRequester
   */
  public function setBody($body) {
    if ($content = json_encode($body)) {
      $this->setHeader('Content-Type', 'application/json');
      $this->setHeader('Content-Length', strlen($content));
      $this->options[RequestOptions::BODY] = $content;
    }
    else {
      throw new \Exception('Request body was empty or could not be encoded into json.');
    }
    return $this;
  }

  public function setHeader($name, $value) {
    $this->options[RequestOptions::HEADERS][$name] = $value;
  }

}

