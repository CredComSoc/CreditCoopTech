<?php

namespace CCNode;

class ConfigFromIni implements ConfigInterface {

  function __construct(array $ini_file) {
    $this->dbCreds = $ini_file['db']; // Array
    $this->accountStore = $ini_file['account_store']??'\Examples\AccountStore';

    $this->blogicMod = $ini_file['blogic_mod']; // optional
    $this->zeroPayments = $ini_file['zero_payments']??false;
    $this->validatePending = $ini_file['validate_pending']??true;
    $this->devMode = $ini_file['dev_mode']??false;

    $this->workflowsFile = $ini_file['workflows_filepath'];

    // The rest are only used when there are remote accounts.
    $this->absPath = $ini_file['abs_path']??'mynode';
    $tree = explode('/', $this->absPath);
    $this->nodeName = end($tree);
    if (count($tree) > 1) {
      $this->conversionRate = $ini_file['conversion_rate'];
      $this->privacy = $ini_file['priv']; // Array
      $this->timeOut = $ini_file['timeout'];
      $this->validatedWindow = $ini_file['validated_window'];
      $this->trunkwardAcc = prev($tree);
    }
    else {
      $this->trunkwardAcc = '';
      $this->conversionRate = 1;
    }
    $this->displayFormat = $ini_file['display_format'];
  }
}
