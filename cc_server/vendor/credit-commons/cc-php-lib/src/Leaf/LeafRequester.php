<?php

namespace CreditCommons\Leaf;

use CreditCommons\NodeRequester;
use CreditCommons\NewTransaction;
use CreditCommons\Exceptions\CCFailure;
use GuzzleHttp\RequestOptions;

/**
 * Class for a non-ledger client to call to a credit commons accounting node.
 * This wraps the NodeRequester in order to handle the authentication and errors
 * appropriate for the client. Extend this class to catch the errors.
 */
abstract class LeafRequester extends NodeRequester implements LeafRequesterInterface {

  /**
   * {@inheritDoc}
   */
  function __construct(string $downstream_node_url, string $user, string $key) {
    $this->baseUrl = $downstream_node_url;
    // todo put this in the client base class.
    $this->options[RequestOptions::HEADERS]['CC-user'] = $user;
    $this->options[RequestOptions::HEADERS]['CC-auth'] = $key;
  }

  /**
   *
   * @param NewTransaction $new_transaction
   * @return array
   *   the Transaction and permitted transitions as an \stdClass
   */
  public function submitNewTransaction(NewTransaction $new_transaction) : array {
    $request = $this
      ->setMethod('post')
      ->setBody($new_transaction);
    $result = $request->request('transaction');
    if (!$result) {
      throw new CCFailure('No transaction response from downstream');
    }
    return [$result->data, (array)$result->meta->transitions];
  }

}
