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
    $entries = $this
      ->setBody($transaction->entries[0])
      ->setMethod('post')
      ->request(200, $transaction->type);

    // Upcast the payer and payees

    $additional = [];
    foreach ($entries as $entry) {
      $entry->payee = accountStore()->fetch($entry->payee);
      $entry->payer = accountStore()->fetch($entry->payer);
      $additional[] = Entry::create($entry)->additional();
    }
    return $additional;
  }

}
