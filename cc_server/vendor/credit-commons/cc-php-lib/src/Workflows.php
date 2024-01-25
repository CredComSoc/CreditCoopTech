<?php
namespace CreditCommons;

use CreditCommons\NodeRequester;
use CreditCommons\Workflow;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\DoesNotExistViolation;

/**
 * Class for clients and base class for nodes.
 * @todo consider making this abstract and using it as a base class for clients and nodes.
 */
abstract class Workflows {

  /**
   * The trunkward node requester.
   * @var NodeRequester
   */
  protected $nodeRequester;

  function __construct(NodeRequester $trunkward_node) {
    // N.B. nodes might not have a trunkward node.
    $this->nodeRequester = $trunkward_node;
  }


  function loadAll() {
    return $this->getTrunkwardWorkflows();
  }


  /**
   * Get a particular workflow.
   * If this is not overridden it only knows about workflows on the trunkward node.
   * @param type $needed_id
   * @return Workflow
   * @throws DoesNotExistViolation
   */
  abstract function get($needed_id) : Workflow;

  /**
   * Get all the workflows from trunkward nodes and index them by their position in the tree and hashes.
   * @return array
   * @throws CCFailure
   *
   * @todo cache the results.
   */
  function getTrunkwardWorkflows() : array {
    $tree = [];
    // Child classes might not have NodeRequester set, and call
    if ($this->nodeRequester) {
      $tree = $this->nodeRequester->getWorkflows();
    }
    return $tree;
  }


}


