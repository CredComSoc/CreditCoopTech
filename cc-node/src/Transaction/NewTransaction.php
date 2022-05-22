<?php

namespace CCNode\Transaction;

use CreditCommons\BaseNewTransaction;

/**
 * Receive data from the user and prepare it to send to the node via the transaction/new endpoint
 */
class NewTransaction extends BaseNewTransaction{

  /**
   * The twig which first converts the leaf request to a transaction validates and adds the uuid
   * generate the uuid
   * @param \stdClass $data
   */
  static function prepareClientInput(\stdClass &$data) {
    static::validateFields($data);
    $uuid = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0x0fff) | 0x4000,
      mt_rand(0, 0x3fff) | 0x8000,
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff)
    );

    $first_entry = (object)[
      'payee' => $data->payee,
      'payer' => $data->payer,
      'quant' => $data->quant,
      'description' => $data->description,
      'metadata' => $data->metadata ?? (new \stdClass())
    ];
    return (object)[
      'uuid' => $uuid,
      'type' => $data->type,
      'entries' => [$first_entry]
    ];
  }

}

