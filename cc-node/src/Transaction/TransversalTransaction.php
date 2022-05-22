<?php
namespace CCNode\Transaction;
use CCNode\Transaction\Entry;
use CCNode\Transaction\Transaction;
use CCNode\Accounts\Remote;
use function \CCNode\getConfig;
use function \CCNode\API_calls;

/**
 * Handle the sending of transactions between ledgers and hashing.
 * @todo make a new interface for this.
 */
class TransversalTransaction extends Transaction {

  public $downstreamAccount;
  public $upstreamAccount;
  // Only load the trunkward account if needed
  public $trunkwardAccount = NULL;

  public function __construct(
    public string $uuid,
    public string $written,
    public string $type,
    public string $state,
    array $entries,
    public int $version
  ) {
    global $user;
    $this->upstreamAccount = $user instanceof Remote ? $user : NULL;
    $payer = $entries[0]->payer;
    $payee = $entries[0]->payee;
    // Find the downstream account
    $trunkward_name = getConfig('trunkward_acc_id');
    // if there's an upstream account, then the other one, if remote is downstream
    if ($this->upstreamAccount) {
      if ($this->upstreamAccount->id == $payee->id and $payer instanceOf Remote) {
        $this->downstreamAccount = $payer; // going towards a payer branch
      }
      elseif ($this->upstreamAccount->id == $payer->id and $payee instanceOf Remote) {
        $this->downstreamAccount = $payee;// going towards a payee branch
      }
    }// with no upstream account, then any remote account is downstream
    else {
      if ($payee instanceOf Remote) {
        $this->downstreamAccount = $payee;
      }
      elseif ($payer instanceOf Remote) {
        $this->downstreamAccount = $payer;
      }
    }
    /// Set the trunkward account only if it is used.
    if ($this->upstreamAccount and $this->upstreamAccount->id == $trunkward_name) {
      $this->trunkwardAccount = $this->upstreamAccount;
    }
    elseif ($this->downstreamAccount and $this->downstreamAccount->id == $trunkward_name) {
      $this->trunkwardAccount = $this->downstreamAccount;
    }

    $this->upcastEntries($entries);
  }

  /**
   * {@inheritDoc}
   */
  function buildValidate() : void {
    parent::buildvalidate();
    if ($this->downstreamAccount) {
      $rows = $this->downstreamAccount->buildValidateRelayTransaction($this);
      Entry::upcastAccounts($rows);
      $this->upcastEntries($rows, TRUE);
    }
    $this->responseMode = TRUE;
  }


  /**
   * {@inheritDoc}
   */
  public function saveNewVersion() : int {
    global $user;
    $id = parent::saveNewVersion();
    if ($this->version > 0) {
      $this->writeHashes($id);
    }
    return $id;
  }


  /**
   * Filter the entries for those that pertain to a certain node.
   * Make a clone of the transaction with only the entries shared with an
   * adjacent ledger.
   *
   * @param Remote $account
   */
  public function filterFor(Remote $account) : array {
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
   * Produce a hash of all the entries and transaction data in an easily repeatable way.
   * @param Remote $account
   * @param Entry[] $entries
   * @return string
   */
  protected function getHash(Remote $account, array $entries) : string {
    foreach ($entries as $entry) {
      if ($this->trunkwardAccount and $account->id == $this->trunkwardAccount->id) {
        $quant = $entry->trunkward_quant;
      }
      else {
        $quant = $entry->quant;
      }
      $rows[] = $quant.'|'.$entry->description;
    }
    $string = join('|', [
      $account->getLastHash(),
      $this->uuid,
      join('|', $rows),
      $this->version,
    ]);
    return md5($string);
  }

  /**
   * Send the whole transaction downstream for building.
   * Or send the entries back upstream
   *
   * To send transactions to another node
   * - filter the entries
   * - remove workflow
   * - remove actions
   *
   * @return stdClass
   */
  public function jsonSerialize() : array {
    global $user;
    $array = parent::jsonSerialize();
    if ($adjacentNode = $this->responseMode ? $this->upstreamAccount : $this->downstreamAccount) {
      $array['entries'] = $this->filterFor($adjacentNode);
    }
    if ($array['version'] < 1) {
      unset($array['state']);
    }

    // relaying downstream
    if ($this->downstreamAccount && !$this->responseMode) {
      // Forward the whole transaction minus a few properties.
      unset($array['status'], $array['workflow'], $array['payeeHash'], $array['payerHash'], $array['transitions']);
    }
    return $array;
  }

  /**
   * Load this transaction's workflow from the local json storage.
   * @todo Sort out
   */
  protected function getWorkflow() : \CreditCommons\Workflow {
    $workflow = parent::getWorkflow();
    if ($this->upstreamAccount instanceOf Remote) {
      // Would have nice to cache this
      $workflow->creation->by = ['author'];// This is tautological, exactly what we need actually.
      foreach ($workflow->states as &$state) {
        foreach ($state as $target_state => $info) {
          if (empty($info->signatories)) {
            $info->signatories = ['payer', 'payee'];
          }
        }
      }
    }
    return $workflow;
  }

  /**
   * {@inheritDoc}
   */
  function changeState(string $target_state) : int {
    if ($this->downstreamAccount) {
      API_calls($this->downstreamAccount)->transactionChangeState($this->uuid, $target_state);
    }
    $status_code = parent::changeState($target_state);
    $this->responseMode = TRUE;
    return $status_code;
  }


  /**
   * Return TRUE if the response is directed towards the trunk.
   *
   * @return bool
   *
   * @todo put in an interface
   */
  public function trunkwardResponse() : bool {
    if ($this->trunkwardAccount) {
      if ($this->trunkwardAccount == $this->upstreamAccount and $this->responseMode == TRUE) {
        return TRUE;
      }
      elseif ($this->trunkwardAccount == $this->downstreamAccount and $this->responseMode == FALSE) {
        return TRUE;
      }
    }
    return FALSE;
  }
}
