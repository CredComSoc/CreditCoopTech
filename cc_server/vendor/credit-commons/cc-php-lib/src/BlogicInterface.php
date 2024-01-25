<?php

namespace CreditCommons;

interface BlogicInterface {

  /**
   * Given the transaction properties, return new entries which might constitute transaction fees.
   *
   * @param string $type
   * @param string $payee
   * @param string $payer
   * @param int $quant
   * @param \stdClass $metadata
   * @param string $description
   * @return 'stdClass[]
   *   Entries with upcast, non-identical users.
   */
  public function addRows(string $type, string $payee, string $payer, int $quant, \stdClass $metadata, string $description = '') : array;
}
