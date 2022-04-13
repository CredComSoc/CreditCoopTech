<?php
namespace CCNode;

use CreditCommons\BaseEntry;
use CreditCommons\Account;

/**
 * Determine the account types for entries.
 *
 */
class Entry extends BaseEntry implements \JsonSerializable {

  /**
   * TRUE if this entry was authored locally or downstream.
   * @var bool
   */
  private bool $additional;

  /**
   * Convert the account names to Account objects, and instantiate the right sub-class.
   *
   * @param stdClass $data
   *   Could be from client or a flattened Entry
   * @return \CreditCommonsEntry
   */
  static function create(\stdClass $data) : BaseEntry {
    // Convert the payer and payee into Account objects;
    foreach (['payee', 'payer'] as $role) {
      if ($data->$role instanceOf AccountRemote) {
        $row->metadata[$data->$role->id] = $data->$role->givenPath;
      }
    }
    $class = static::determineEntryClass($data->payee, $data->payer);
    return parent::create($data);
  }

  /**
   *
   * @param Account $acc1
   * @param Account $acc2
   * @return string
   */
  static function determineEntryClass(Account $acc1, Account $acc2) : string {
    $class_name = 'CCNode\Entry';
    // Now, depending on the classes of the payer and payee
    if ($acc1 instanceOf AccountBranch and $acc2 instanceOf AccountBranch) {
      // both accounts are leafwards, the current node is at the apex of the route.
      $class_name = 'CCNode\TransversalEntry';
    }
    elseif ($acc1 instanceOf AccountBoT or $acc2 instanceOf AccountBoT) {
      // One of the accounts is trunkwards
      $class_name = 'CCNode\TrunkwardsEntry';
    }
    elseif ($acc1 instanceOf AccountBranch or $acc2 instanceOf AccountBranch) {
      // One account is local, one account is further leafwards.
      $class_name = 'CCNode\TransversalEntry';
    }
    return $class_name;
  }

  function additional() {
    $this->additional = TRUE;
    return $this;
  }

  function isAdditional() : bool {
    return $this->additional;
  }

  /**
   * Entries shared locally
   * if its for a local_request between services
   * - Account names collapsed to local name
   * or if its going back to the client
   * - Account names collapsed to a relative name
   * - Quant rounded to go back to the client
   * @return array
   */
  public function jsonSerialize() : array {
    global $orientation;
    $flat = [
      'payee' => $this->payee->id,
      'payer' => $this->payer->id,
      'author' => $this->author,
      'quant' => $this->quant,
      'description' => $this->description,
      'metadata' => $this->metadata
    ];
    // @todo move this to the point before serialization is triggered.
    if (!$orientation->localRequest) {// going back to client.
      $flat['payee'] = $this->metadata[$flat['payee']] ?? $flat['payee'];
      $flat['payer'] = $this->metadata[$flat['payer']] ?? $flat['payer'];
    }
    return $flat;
  }

}
