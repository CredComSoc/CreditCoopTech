<?php

namespace CCNode;
use CreditCommons\Requester;
use GuzzleHttp\RequestOptions;

/**
 * Calls to the business logic service.
 */
class BlogicRequester extends Requester {


  /**
   * Add a new rule.
   *
   * @param Transaction $transaction
   * @return array
   */
  function appendto(Transaction $transaction) : array {
    $additional = $this
      ->setBody($transaction->entries[0])
      ->setMethod('post')
      ->request(200, 'append/'.$transaction->type);
    return $additional;
  }

  /**
   * Get a list of the Blogic rules.
   *
   * @param bool $full
   */
  function getRules(bool $full = TRUE) : array {
    $this->options[RequestOptions::QUERY] = ['full' => (int)$full];
    list (, $rules) = $this->request(200, 'rules');
    return (array)$rules;
  }
}
