<?php

namespace CreditCommons;

use CreditCommons\CreateFromValidatedStdClassTrait;
use CreditCommons\Account;

/**
 * A transaction consists of several ledger entries sharing a common UUID, type and workflow state.
 * There is a primary entry, and many dependents.
 * Entries can be either transversal or local.
 * The transaction is either transversal or local, depending on the primary entry.
 * That means a local transaction cannot have transversal dependents.
 */
abstract class BaseTransaction implements TransactionInterface {
  use CreateFromValidatedStdClassTrait;

  /**
   * A list of workflow states the current user is permitted to transition to.
   * @var []
   */
  public array $transitions = [];

  public function __construct(
    public string $uuid,
    public string $created,
    public string $updated,
    public string $type,
    public string $state,
    public array $entries,
    public int $version,
    public int $txID// We only know this for saved transaction, not new or upstream transactions
  ) {}

  static function create(\stdClass $data) : static {
    $data->version = $data->version??-1;
    $data->txID = $data->txID??0;
    $data->created = $data->created??'';
    $data->updated = $data->updated??'';
    static::validateFields($data);
    return new static($data->uuid, $data->created, $data->updated, $data->type, $data->state, $data->entries, $data->version, $data->txID);
  }

  protected abstract static function createEntries(array $rows, Account $author = NULL) : array;

}
