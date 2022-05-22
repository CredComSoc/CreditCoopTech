<?php
namespace CCNode;

use CreditCommons\NodeRequester;
use CreditCommons\Workflow;
use CreditCommons\Exceptions\DoesNotExistViolation;

  /**
   * A helper class for dealing with Workflows
   */
class Workflows extends \CreditCommons\Workflows {

  function __construct(NodeRequester $trunkward_node = NULL) {
    // The parent class requires the argument because it is used by clients
    // which must call the trunkward for workflows.
    if ($trunkward_node) {
      parent::__construct($trunkward_node);
    }
  }

  /**
   * @return array
   *   Translated workflows, keyed by the trunkward node name they originated from
   *
   * @todo This should be cached if this system has any significant usage.
   * @todo This is incomplete.
   */
  function loadAll() {
    $trunkward_workflows = $api = API_calls() ? static::getTrunkwardWorkflows(): [];
    return self::arrange($this->loadLocal(), $trunkward_workflows);
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
   * @param NodeRequester $trunkward_requester
   * @return array
   *   Translated workflows, keyed by the trunkward node name they originated from
   *
   * @todo This should be cached if this system has any significant usage.
   */
  static function arrange(array $local_workflows, $trunkward_tree) : array {
    // get the local workflows
    if ($local_workflows) {
      // Now compare the hashes, and where similar, replace the trunkward one with the local translation.
      foreach ($trunkward_tree as $node_path => $wfs) {
        foreach ($wfs as $hash => $wf) {
          if (isset($local[$hash])) {
            $trunkward_tree[$node_path][$hash] = $local[$hash];
            unset($local[$hash]);
          }
        }
      }
      if ($trunkward_tree) {
        // Get the trunkward workflows and merge them in.
        // No idea how this was supposed to work, but absoluteNodePath is no longer a function.
        $trunkward_nodes = NodeRequester::absoluteNodePath($trunkward_requester);
        $abs_path = '/'.implode('/', array_reverse($trunkward_nodes));
      }
      else{
        global $config;
        $abs_path = getConfig('node_name');
      }
      $trunkward_tree[$abs_path] = $local_workflows;
    }
    $all = [];
    // remove the hash keys
    foreach ($trunkward_tree as $node_path => &$wfs) {
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


