<?php

namespace CreditCommons;
use CCNode\Accounts\Trunkward;
use CreditCommons\AccountRemoteInterface;

trait TransversalTransactionTrait {

  /**
   * Filter the entries for those that pertain to a certain node.
   * Make a clone of the transaction with only the entries shared with an
   * adjacent ledger.
   *
   * @param AccountRemoteInterface $account
   */
  protected function filterFor(AccountRemoteInterface $account) : array {
    // Filter entries for the appropriate adjacent ledger
    // If this works we can delete all the TransversalEntry Classes.
    $remote_name = $account->id;
    foreach ($this->entries as $e) {
      if ($e->payee->id == $remote_name or $e->payer->id == $remote_name) {
        $entries[] = $e;
      }
    }
    return $entries;
  }

  /**
   * Generate the hash for a Remote account.
   *
   * @param AccountRemoteInterface $account
   * @return string
   */
  function makeHash(AccountRemoteInterface $account) : string {
    $string = $this->makeHashString($account);
    return md5($string);
  }

  /**
   * @param AccountRemoteInterface $account
   * @return string
   *
   * @todo Establish the entry->description charset in case there's a risk of
   * different servers hashing it differently
   * @todo why doesn't this hash include the transaction type?
   */
  function makeHashString(AccountRemoteInterface $account) : string {
    $trunkward = $account instanceOf Trunkward;
    $rows = [];
    foreach ($this->filterFor($account) as $entry) {
      $quant = $trunkward ? $entry->trunkwardQuant : $entry->quant;
      // Storing the trunkward quant is a way of avoiding rounding errors.
      $rows[] = $quant.'|'.$entry->description;
      // todo it could also store the trunkward accountnames.
    }
    return join('|', [
      $account->getLastHash(),
      $this->uuid,
      $this->state,
      join('|', $rows),
      $this->version,
    ]);
  }

}
