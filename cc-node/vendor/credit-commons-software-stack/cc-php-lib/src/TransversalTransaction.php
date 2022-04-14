<?php
namespace CreditCommons;
use CreditCommons\Transaction;

/**
 * Handle the sending of transactions between ledgers and hashing.
 */
class TransversalTransaction extends Transaction {

  /**
   * Create a NEW transaction on this ledger to correspond with one relayed from upstream or downstream
   *
   * @param stdClass $data
   *   incoming entries. Could be from client or a flattened Entry. They should
   *   probably be validated either as they come in, or as converted to Entry
   *
   * @return \Transaction
   */
  public static function create(stdClass $data) : Transaction {
    global $orientation, $config, $user;
    // But wouldn't we be logged in as the upstream account... global $user
    $upstreamName = $orientation->upstreamAccount->id;

    $rows = (array)$data->entries;
    // All the entries given here are authored by the logged in account.
    $data->entries = Transaction::createEntries($rows, $user);

    // convert the exchange rates if needed.
    // feels like this belongs elsewhere
    if ($orientation->upstreamIsTrunkwards() and $config['bot']['rate'] <> 1) {
      $data->entries = array_map(
        function ($e) {return $e->fromTrunkNode();},
        $data->entries
      );
    }
    $data->state = TransactionInterface::STATE_INITIATED;
    return parent::create($data);
  }

  /**
   * {@inheritDoc}
   */
  public function saveNewVersion() {
    foreach (['payer', 'payee'] as $role) {
      $ledgerAccount = $this->{$role};
      if ($ledgerAccount instanceOf LedgerAccountRemote) {
        // Get the entries for the adjacent ledger, converted if trunkwards
        $entries = $this->filterFor($ledgerAccount, $ledgerAccount instanceOf LedgerAccountBot);
        $$role = $this->getHash($ledgerAccount, $entries);
      }
    }
    $scribe = $_SESSION['user'];

    // The datestamp is added automatically
    $q = "INSERT INTO transactions (uuid, `version`, type, state, scribe, payee_hash, payer_hash) "
    . "VALUES ('$this->uuid', $this->version, '$this->type', '$this->state', '$scribe', '$payee', '$payer')";
    $new_id = Db::query($q);
    $this->writeEntries($new_id);
  }

  /**
   * Make a clone of the transaction with only the entries shared with an
   * adjacent ledger.
   *
   * @param LedgerAccountRemote $account
   */
  function filterFor(LedgerAccountRemote $account, $trunkwards) : array {
    global $config;
    // Filter entries for the appropriate adjacent ledger
    // If this works we can delete all the TransversalEntry Classes.
    $remote_name = $account->id;
    foreach ($this->entries as $e) {
      if ($e->payee->id == $remote_name or $e->payer->id == $remote_name) {
        $entries[] = $e;
      }
    }
    if ($trunkwards and $config['bot']['rate'] <> 1) {
      $entries = array_map(
        function ($e) {return $e->toTrunkNode();},
        $entries
      );
    }

    return $entries;
  }

  /**
   * Produce a hash of all the entries and transaction data in an easily repeatable way.
   * @param LedgerAccountRemote $ledgerAccount
   * @param array $entries
   * @return string
   */
  private function getHash(LedgerAccountRemote $ledgerAccount, array $entries) : string {
    global $config;
    foreach ($entries as $entry) {
      $str = round($entry->quant);// Intval avoids rounding errors. Might not be bulletproof!
      $str .= $entry->description;
      $rows[] = $str;
    }
    $last_hash = $ledgerAccount->getLastHash();
    return static::makeHash(
      $last_hash,
      $this->uuid,
      $this->version,
      $rows
    );
  }

  /**
   * Compile the hash components and return the hash
   * @param string $last_hash
   * @param string $uuid
   * @param int $ver
   * @param array Entries
   *   An array of one string for each entry.
   * @return string
   *   The hash
   */
  private static function makeHash(string $last_hash, string $uuid, int $ver, $entries) : string {
    $entries_string = join('|', $entries);
    $string = join('|', [$last_hash, $uuid, $ver, $diff, $entries_string]);
    //cc_log($string, md5($string));
    return md5($string);
  }


  /**
   * To send transactions to another node
   * - filter the entries
   * - new rows only upstream
   * - remove workflow
   * - remove actions
   *
   * @global Orientation $orientation
   * @return stdClass
   */
  public function jsonSerialize() : array {
    global $orientation;
    $adjacentAccount = $orientation->adjacentAccount();
    if ($adjacentAccount == 'client') {
      return parent::jsonSerialize();
    }
    $array = (array)$this;
    $array['entries'] = $this->filterFor($adjacentAccount, $orientation->goingTrunkwards());

    if ($orientation->goingDownstream()) {
      // Generating a request, send the whole transaction minus a few properties.
      unset($array['status'], $array['workflow'], $array['payeeHash'], $array['payerHash']);
    }
    else {
      // Generating a response, send only additional entries.
      $array = array_filter($array['entries'], function($e) {return $e->isAdditional();} );
    }
    return $array;
  }


  /**
   * {@inheritDoc}
   */
  function buildValidate() : void {
    global $orientation;
    parent::buildvalidate();
    if ($requester = $orientation->getRequester()) {
      $requester->buildValidateTransaction($this);
    }
  }

  /**
   * {@inheritDoc}
   */
  function changeState(string $target_state) {
    global $orientation;
    if ($requester = $orientation->getRequester()) {
      $requester->transactionChangeState($this->uuid, $target_state);
    }
    parent::changeState($target_state);
  }


  protected static function createEntries(array $rows, Account $author = NULL) : array {
    $entries = [];
    foreach ($rows as $row) {
      if ($author){
        $row->author = $author->id;
      }
      $row->payer = accountStore()->ResolveAddressTolocal($data->payer, TRUE);
      $row->payee = accountStore()->ResolveAddressTolocal($data->payee, TRUE);
      $entries[] = Entry::create($row);
    }
    return $entries;
  }

}
