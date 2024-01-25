<?php
namespace CCNode\Transaction;

use CCNode\Transaction\Entry;
use CCNode\Transaction\Transaction;
use CCNode\Accounts\Remote;
use CreditCommons\TransversalTransactionTrait;
use function \CCNode\API_calls;

/**
 * Handle the sending of transactions between ledgers and hashing.
 * @todo make a new interface for this.
 */
class TransversalTransaction extends Transaction {

  use TransversalTransactionTrait;

  public function __construct(
    public string $uuid,
    public string $type,
    public string $state,
    /** @var Entry[] $entries */
    public array $entries,
    public string $written,
    public int $version
  ) {
    global $cc_user, $cc_config;
    $entries[0]->isPrimary = TRUE;
  }

  /**
   * {@inheritDoc}
   */
  function buildValidate() : array {
    global $cc_user, $orientation;
    $new_local_rows = parent::buildvalidate();
    if ($orientation->downstreamAccount) {
      $new_remote_rows = $orientation->downstreamAccount->relayTransaction($this);

      static::upcastEntries($new_remote_rows, TRUE);
      $this->entries = array_merge($this->entries, $new_remote_rows);
    }
    $orientation->responseMode();
    // Entries have been added to the transaction, but now return the local and
    // remote additional entries which concern the upstream node.
    // @todo this should go somewhere a bit closer to the response generation.
    if ($cc_user instanceof Remote) {
      $new_local_rows = array_filter(
        $this->filterFor($cc_user),
        function($e) {return !$e->isPrimary;}
      );
    }
    return array_values($new_local_rows);
  }

  /**
   * {@inheritDoc}
   */
  public function saveNewVersion() : int {
    $id = parent::saveNewVersion();

    if ($this->version > 0) {
      $this->writeHashes($id);
    }
    return $id;
  }

  /**
   * {@inheritDoc}
   */
  function delete() {
    if ($this->state <> static::STATE_VALIDATED) {
      throw new CCFailure('Cannot delete transversal transactions.');
    }
    parent::delete();
  }

  /**
   * Send the whole transaction downstream for building.
   * Or send the entries back upstream
   *
   * To send transactions to another node
   * - filter the entries
   * - remove workflow
   *
   * @return stdClass
   */
  public function jsonSerialize() : mixed {
    global $orientation;
    $orig_entries = $this->entries;
    if ($adjacentNode = $orientation->targetNode()) {
      $this->entries = $this->filterFor($adjacentNode);
    }
    $array = parent::jsonSerialize();
    $this->entries = $orig_entries;
    unset($array['status'], $array['workflow'], $array['created'], $array['version'], $array['state']);
    return $array;
  }

  /**
   * Load this transaction's workflow from the local json storage, then restrict
   * the workflow as for a remote transaction.
   */
  public function getWorkflow() : \CreditCommons\Workflow {
    global $orientation;
    $workflow = parent::getWorkflow();
    if ($orientation->upstreamAccount instanceOf Remote) {
      // Remote transactions can ONLY be created by their authors, and acted on
      // only by participants, not admins. However Workflow::getTransitions
      // assumes admin can do any valid transition.
      // Prevent admin doing any transitions that payer or payee cant do
      foreach ($workflow->states as &$state) {
        foreach ($state as $target_state => $info) {
          if (empty($info->actors)) {
            $info->actors = ['payer', 'payee'];
          }
        }
      }
    }
    return $workflow;
  }

  /**
   * {@inheritDoc}
   */
  function changeState(string $target_state) : bool {
    global $orientation;
    if ($orientation->downstreamAccount) {
      API_calls($orientation->downstreamAccount)->transactionChangeState($this->uuid, $target_state);
    }
    $saved = parent::changeState($target_state);
    $this->responseMode = TRUE;
    return $saved;
  }

  /**
   * {@inheritDoc}
   */
  public function transitions() : array {
    global $cc_user;
    // Admin permission over a transaction can only be granted when payer or payee is local
    if ($this->entries[0]->payer instanceof Remote and $this->entries[0]->payee instanceof Remote) {
      $admin = FALSE;
    }
    else {
      $admin = (bool)$cc_user->admin;
    }
    return $this->getWorkflow()->getTransitions($cc_user->id, $this, $admin);
  }
}
