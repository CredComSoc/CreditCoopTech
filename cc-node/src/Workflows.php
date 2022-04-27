<?php
namespace CCNode;

use CreditCommons\RestAPI;
use CreditCommons\Workflow;
use CreditCommons\Exceptions\DoesNotExistViolation;

  /**
   * A helper class for dealing with Workflows
   */
class Workflows extends \CreditCommons\Workflows {

  function __construct(RestAPI $trunkwards_node = NULL) {
    // The parent class requires the argument because it is used by clients
    // which must call the trunkwards for workflows.
    if ($trunkwards_node) {
      parent::__construct($trunkwards_node);
    }
  }

  /**
   * @return array
   *   Translated workflows, keyed by the trunkwards node name they originated from
   *
   * @todo This should be cached if this system has any significant usage.
   * @todo This is incomplete.
   */
  function loadAll() {
    $trunkwards_workflows = $api = API_calls() ? static::getTrunkwardsWorkflows(): [];
    return self::arrange($this->loadLocal(), $trunkwards_workflows);
  }

  /**
   *
   * @return Workflow[]
   *   Keyed by hash
   */
  private function loadLocal() : array {
    $wfs = [];
    if (file_exists('workflows.json')) {
      $content = file_get_contents('workflows.json');
      if ($data = json_decode($content)) {
        foreach ($data as $wf) {
          $workflow = new Workflow($wf);
          $wfs[$workflow->getHash()] = $workflow;
        }
      }
      else {
        new \CreditCommons\Exceptions\CCFailure('Bad json workflows file');
      }
    }
    return $wfs;
  }

  /**
   * Collect Trunkward workflows and merge them with local workflows.
   * @param Workflow[] $local_workflows
   * @paramn RestAPI $trunkward_requester
   * @return array
   *   Translated workflows, keyed by the trunkwards node name they originated from
   *
   * @todo This should be cached if this system has any significant usage.
   */
  static function arrange(array $local_workflows, $trunkwards_tree) : array {
    // get the local workflows
    if ($local_workflows) {
      // Now compare the hashes, and where similar, replace the trunkwards one with the local translation.
      foreach ($trunkwards_tree as $node_path => $wfs) {
        foreach ($wfs as $hash => $wf) {
          if (isset($local[$hash])) {
            $trunkwards_tree[$node_path][$hash] = $local[$hash];
            unset($local[$hash]);
          }
        }
      }
      if ($trunkwards_tree) {
        // Get the trunkward workflows and merge them in.
        // No idea how this was supposed to work, but absoluteNodePath is no longer a function.
        $trunkwards_nodes = RestAPI::absoluteNodePath($trunkward_requester);
        $abs_path = '/'.implode('/', array_reverse($trunkwards_nodes));
      }
      else{
        global $config;
        $abs_path = $config['node_name'];
      }
      $trunkwards_tree[$abs_path] = $local_workflows;
    }
    $all = [];
    // remove the hash keys
    foreach ($trunkwards_tree as $node_path => &$wfs) {
      foreach ($wfs as $hash => $wf) {
        $all[$node_path][] = $wf;// $node_path indicates the scope of each workflow
      }
    }
    return $all;
  }

  /**
   *
   * @param type $needed_id
   * @param array $all_workflows
   * @return Workflow
   * @throws DoesNotExistViolation
   */
  function get($needed_id) : Workflow {
    foreach ($this->loadAll() as $wfs) {
      foreach ($wfs as $workflow) {
        if ($workflow->id == $needed_id) {
          return $workflow;
        }
      }
    }
    throw new DoesNotExistViolation(type: 'workflow', id: $needed_id);
  }



}


