<?php
namespace CreditCommons;

use CreditCommons\RestAPI;
use CreditCommons\Workflow;
use CreditCommons\Exceptions\CCFailure;
use CreditCommons\Exceptions\DoesNotExistViolation;

/**
 * Class for clients and base class for nodes.
 * @todo consider making this abstract and using it as a base class for clients and nodes.
 */
class Workflows {

  /**
   * The trunkwards node requester.
   * @var RestAPI
   */
  protected $restAPI;

  function __construct(RestAPI $trunkwards_node) {
    // N.B. nodes might not have a trunkwards node.
    $this->restAPI = $trunkwards_node;
  }


  function loadAll() {
    return $this->getTrunkwardsWorkflows();
  }


  /**
   * Get a particular workflow.
   * If this is not overridden it only knows about workflows on the trunkwards node.
   * @param type $needed_id
   * @return Workflow
   * @throws DoesNotExistViolation
   */
  function get($needed_id) : Workflow {
    foreach ($this->getTrunkwardsWorkflows() as $wfs) {
      foreach ($wfs as $workflow) {
        if ($workflow->id == $needed_id) {
          return $workflow;
        }
      }
    }
    throw new DoesNotExistViolation(type: 'workflow', id: $needed_id);
  }

  /**
   * Get all the workflows from trunkwards nodes and index them by their position in the tree and hashes.
   * @return array
   * @throws CCFailure
   *
   * @todo cache the results.
   */
  function getTrunkwardsWorkflows() : array {
    $tree = [];
    // Child classes might not have restAPI set, and call
    if ($this->restAPI) {
      $tree = $this->restAPI->getWorkflows();
    }
    return $tree;
  }

}


