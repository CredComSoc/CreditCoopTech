<?php

namespace CreditCommons\Leaf;

use CreditCommons\TransactionDisplay;
use CreditCommons\Leaf\FlatEntry;
use CreditCommons\Leaf\LeafTransactionInterface;

/**
 * Transaction for use on the client side including transitions property.
 */
abstract class LeafTransaction extends TransactionDisplay implements LeafTransactionInterface {

  public array $transitions = [];

  /**
   * @param stdClass[] $rows
   *   Which are Entry objects flattened by json for transport.
   * @return Entry[]
   *   The created entries
   */
  protected static function upcastEntries(array &$rows, bool $is_additional = FALSE) : bool {
    $entries = [];
    foreach ($rows as $row) {
      // same as an entry but with strings as account names.
      $entries[] = FlatEntry::create($row);
    }
    return $entries;
  }

}
