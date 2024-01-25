<?php

namespace CreditCommons\Leaf;

use CreditCommons\TransactionInterface;

/**
 * Transaction for use on the client side including transitions property.
 */
interface LeafTransactionInterface {

  /**
   * Upcast a transaction coming back from the node.
   *
   * @param stdClass $data
   *   Validated to contain payer, payee, description & quant
   * @return \Transaction
   * @note this is NOT part of jsonSerializable interface
   */
  static function createFromJsonClass(\stdClass $data, array $transitions) : static;


  /**
   * Render the transaction action links as forms which can post. (Client side only)
   *
   * @param string $uuid
   * @param array $labels
   *   action labels, keyed by target state.
   * @return string
   */
  public function actionLinks(array $transitions) : string;
}

