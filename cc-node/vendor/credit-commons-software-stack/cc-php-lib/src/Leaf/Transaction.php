<?php

namespace CreditCommons\Leaf;

use CreditCommons\BaseTransaction;
use CreditCommons\Account;
use CreditCommons\Leaf\FlatEntry;

/**
 * Class for the client to handle responses with Transactions.
 */
abstract class Transaction extends BaseTransaction{

  /**
   * Upcast a transaction coming back from the node
   * @param stdClass $data
   *   Validated to contain payer, payee, description & quant
   * @return \Transaction
   * @note this is NOT part of jsonSerializable interface
   * @note at the moment this doesn't work when called statically in BaseTransaction
   */
  public static function createFromJsonClass(\stdClass $data) : static {
    if (get_called_class() == get_class()) {
      throw CCFailure::create('Cannot call base transaction class directly.');
    }
    $data->entries = static::createEntries($data->entries);
    $t = static::create($data);
    // @todo check that the given transitions are the same as the calculated ones.
    $t->transitions = $data->transitions;
    return $t;
  }

  /**
   *
   * @param stdClass[] $rows
   *   Which are Entry objects flattened by json for transport.
   * @return Entry[]
   *   The created entries
   */
  protected static function createEntries(array $rows, Account $author = NULL) : array {
    $entries = [];
    foreach ($rows as $row) {
      // same as an entry but with strings as account names.
      $entries[] = FlatEntry::create($row);
    }
    return $entries;
  }
}
