<?php

ini_set('display_errors', 1);
const NODE_INI_FILE = '../node.ini';
require_once '../vendor/autoload.php';
$node_config = parse_ini_file(NODE_INI_FILE);

if ($_POST) {
  unset($_POST['submit']);
  $errs = [];

  if (!isset($_POST['zero_payments'])) {
    $_POST['zero_payments'] = 0;
  }

  if (!$errs) {
    require './writeini.php';
    replaceIni($_POST, NODE_INI_FILE);
  }

}
$node_config = parse_ini_file(NODE_INI_FILE);

if (!is_writable(NODE_INI_FILE)) {
  $errs[] = NODE_INI_FILE . " is not writable";
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
  <head>
    <title>Credit Commons config</title>
  </head>
  <body>
    <h1>Credit Commons node settings</h1>
    <p>Hover for help. To edit settings, see the ini files;
      <br />Or go to <a href = "index.php?accounts">account settings</a>.
    <?php if (!empty($errs)) print "<p><font color=red>".implode('<br />', $errs).'</font>'; ?>
    <form method="post">
      <h2>Transactions</h2>
<!--
      <p title="This information could only be used by the end client or for formatting info to send to the client.">
        Name of unit <input name = "currency_name" value = "<?php print $node_config['currency_name']??''; ?>">
      </p>
      <p title="This information could only be used by the end client or for formatting info to send to the client.">
        Decimal places displayed <input name = "decimal_places" type = "number" min = "0" max = "3" size = "1" value = "<?php print $node_config['decimal_places']; ?>">
      </p>
-->
      <p title="Some social currencies like to register zero value transactions">
        Allow zero payments <input name = "zero_payments" type = "checkbox" value = "<?php print $node_config['zero_payments']; ?>">
      </p>

      <h2>Performance</h2>
      <p>Timeout in seconds<input name = "timeout" type = "number" min = "0" max = "60" size = "1" value = "<?php print $node_config['timeout']; ?>">
      <br />(Needs to be longer for nodes far away from the trunk)
      </p>

      <p><input type ="submit" value ="save"></p>
    </form>

  </body>
</html><?php

