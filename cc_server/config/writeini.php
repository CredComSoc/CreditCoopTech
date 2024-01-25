<?php

/**
 * Rewrite the ini file using find and replace of keys.
 *
 * @param array $some_values
 * @param string $filename
 * @return \CCNode\ConfigInterface
 */
function replaceIni(array $some_values, string $filename): \CCNode\ConfigInterface {
  $contents = file_get_contents($filename);
  foreach($some_values as $key => $val) {
    if(is_array($val)) {
      foreach($val as $skey => $sval) {
        $find = "/$key\[".$skey."\] ?=.*/";
        $replace = "$key".'['. $skey .'] = '.(is_numeric($sval) ? $sval : '"'.$sval.'"');
        $contents = preg_replace($find, $replace, $contents);
      }
    }
    else {
      $contents = preg_replace(
        "/$key ?=.*/",
        "$key = ".(is_numeric($val) ? $val : '"'.$val.'"'),
        $contents
      );
    }
  }
  if (file_put_contents(NODE_INI_FILE, $contents)) {
    return new \CCNode\ConfigFromIni(parse_ini_file(NODE_INI_FILE));
  }
  else {
    echo 'Problem writing config file: '.$filename;
  }
}

