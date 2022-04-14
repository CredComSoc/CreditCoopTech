<?php

namespace CreditCommons;
use CreditCommons\Account;
use CreditCommons\Exceptions\CCViolation;
use CreditCommons\CreateFromValidatedStdClassTrait;

/**
 * This class pretty much mirrors what's in the database. But also translates
 * payer and payee addresses for upstream/downstream ledgers.
 */
class BaseEntry {
  use CreateFromValidatedStdClassTrait;

  function __construct(
    public Account $payee,
    public Account $payer,
    public int $quant,
    public string $author, // the author is always local and we don't need to cast it into account
    public array $metadata, // does not recognise field type: \stdClass
    public string $description = '',
  ) { }

  /**
   * Convert the account names to Account objects, and instantiate the right sub-class.
   *
   * @param object $data
   *   From upstream, downstream NewTransaction or Blogic service. The payer and
   *   payee are already converted to Accounts
   * @return \Entry
   *
   * @note This function is for use by the client only. Its doesn't know about
   * all the classes available for transversal entries. Nodes should override it.
   *
   */
  static function create(\stdClass $data) : BaseEntry {
    if (!isset($data->metadata)) {
      $data->metadata = [];
    }
    static::validateFields($data);

    if ($data->payer->id == $data->payee->id) {
      throw new CCViolation("Same account given for payee and payer: $payee->id");
      // @todo If this happens with the trunkwards account then it means the remote address does not exist.
    }
    return new static (
      $data->payee,
      $data->payer,
      $data->quant,
      $data->author,
      $data->metadata,
      $data->description
    );
  }


}
