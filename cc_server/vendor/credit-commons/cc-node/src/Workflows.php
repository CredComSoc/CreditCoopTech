<?php
namespace CCNode;

use CreditCommons\NodeRequester;
use CreditCommons\Workflow;
use CreditCommons\Exceptions\DoesNotExistViolation;
use CCNode\API_calls;

/**
 * A helper class for dealing with Workflows.
 *
 * @deprecated for now, but later will need to cache trunkward workflows.
 */
class Workflows extends \CreditCommons\Workflows {

  protected $localWorkflows = [];

  function __construct(string $file_path, NodeRequester $trunkward_node = NULL) {
    // The parent class requires the argument because it is used by clients
    // which must call the trunkward for workflows.
    if ($trunkward_node) {
      parent::__construct($trunkward_node);
    }
    if (!file_exists($filePath)) {
      throw new \CreditCommons\Exceptions\CCFailure('Missing $file_path file at '.getcwd());
    }
    $wfs = json_decode(file_get_contents('workflows.json'));
    if (empty($wfs)) {
      throw new \CreditCommons\Exceptions\CCFailure('Bad json workflows file');
    }
    foreach ($wfs as $wf) {
      $workflow = new Workflow($wf);
      $this->localWorkflows[$workflow->getHash()] = $workflow;
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
    return self::merge($this->localWorkflows, $trunkward_workflows);
  }

  /**
   * Collect trunkward workflows and merge them with local workflows.
   * @param Workflow[] $local_workflows
   * @param array $trunkward_tree
   * @return array
   *   Translated workflows, keyed by the trunkward node name they originated from
   *
   * @todo This should be cached if this system has any significant usage.
   */
  static function merge(array $local_workflows, array $trunkward_tree) : array {
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
        global $cc_config;
        $abs_path = $cc_config->nodeName;
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
