<?php

function replaceIni(array $array, string $filename): int {
  $inifile = file_get_contents($filename);
  foreach($array as $key => $val) {
    if(is_array($val)) {
      foreach($val as $skey => $sval) {
        $find = "/$key\[".$skey."\] ?=.*/";
        $replace = "$key".'['. $skey .'] = '.(is_numeric($sval) ? $sval : '"'.$sval.'"');
        $inifile = preg_replace($find, $replace, $inifile);
      }
    }
    else {
      $inifile = preg_replace(
        "/$key ?=.*/",
        "$key = ".(is_numeric($val) ? $val : '"'.$val.'"'),
        $inifile
      );
    }
  }
  if ($inifile) {
    return file_put_contents($filename, $inifile);
  }
  throw new \Exception('Problem with preg_replace settings on '.$filename);
}

