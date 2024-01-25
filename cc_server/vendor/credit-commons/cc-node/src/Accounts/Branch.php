<?php

namespace CCNode\Accounts;

/**
 * Class representing an account linked to leafwards node
 */
abstract class Branch extends Remote {

  function trunkwardPath() : string {
    global $cc_config;
    $path = "$cc_config->nodeName/$this->id";
    if ($this->relPath) {
      $path .= '/'.$this->relPath;
    }
    return $path;
  }

  function leafwardPath() : string {
    return $this->id. '/'.$this->relPath;
  }

}
