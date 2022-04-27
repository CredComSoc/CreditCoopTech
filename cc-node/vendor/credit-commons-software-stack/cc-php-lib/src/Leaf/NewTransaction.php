<?php

namespace CreditCommons\Leaf;
use CreditCommons\BaseNewTransaction;

/**
 * Receive data from the user and prepare it to send to the node via the transaction/new endpoint
 */
class NewTransaction extends BaseNewTransaction {

  /**
   * The client side validates before sending this object.
   * @param \stdClass $data
   * @return static
   */
  static function create(\stdClass $data) {
    static::validateFields($data);
    return new static($data->payee, $data->payer, $data->quant, $data->description, $data->type);
  }


}

