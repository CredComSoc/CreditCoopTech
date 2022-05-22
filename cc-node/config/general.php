<?php


if ($_POST) {
  // populate unchecked boxes
  //print_r($_POST);
  $_POST['priv'] += [
    'metadata' => 0,
    'acc_ids' => 0,
    'acc_summaries' => '0',
    'transactions' => 0
  ];

  if (!isset($_POST['zero_payments'])) {
    $_POST['zero_payments'] = 0;
  }

  unset($_POST['submit']);
  require './writeini.php';
  replaceIni($_POST, NODE_INI_FILE);

}
$config = parse_ini_file(NODE_INI_FILE);

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
    <?php if (!empty($errs)) print "<p><font color=red>".implode('<br />', $errs).'</font></p>';
    elseif ($_POST) print "<p><font color=green>Saved</font></p>"; ?>
    <form method="post">
      <h2>Transactions</h2>

      <p>Although the ledger may display formatted values, values are stored internally with a base unit which is always an integer. The exchange rate is the number of local base units which equals 1 base unit on the trunkward node.<br />
      <input name = "conversion_rate" type = "number" min = "0.001" max = "1000" step = "0.001" size = "2" value = "<?php print $config['conversion_rate']; ?>" <?php if (!$trunkwards_name) print 'disabled';?> > <?php if (!$trunkwards_name) print '(no trunkwards node configured)';?></p>

      <p>How to convert from the base integer to the displayed amount.<br />
      <input name = "display_format" value = "<?php print @$config['display_format']; ?>" disabled> (coming soon)</p>

      <!--
      <p title="This information could only be used by the end client or for formatting info to send to the client.">
        Name of unit <input name = "currency_name" value = "<?php print $config['currency_name']??''; ?>">
      </p>
      <p title="This information could only be used by the end client or for formatting info to send to the client.">
        Decimal places displayed <input name = "decimal_places" type = "number" min = "0" max = "3" size = "1" value = "<?php print $config['decimal_places']; ?>">
      </p>
-->
      <p>Some social currencies register transactions with zero units.<br />
        Allow zero payments <input name = "zero_payments" type = "checkbox" value = "1" <?php if ($config['zero_payments'])print 'checked'; ?>>
      </p>

      <h2>Privacy</h2>
      <p>Which aspects of the ledger are visible to the public? (to be implemented soon)</p>
        <label>Account Ids</label>
        <input name = "priv[acc_ids]" type = "checkbox" value = "1" disabled  <?php print $config['priv']['acc_ids'] ? 'checked ': ''; ?>>
        <br />
        <label>Transactions</label>
        <input name = "priv[transactions]" type = "checkbox" value = "1" disabled <?php print $config['priv']['transactions'] ? 'checked ': ''; ?>>
        <br />
        <label>Anonymised stats</label>
        <input name = "priv[acc_summaries]" type = "checkbox" value = "1" disabled <?php print $config['priv']['acc_summaries'] ? 'checked ': ''; ?>>
        <br />
        <label>Transaction metadata</label>
        <input name = "priv[metadata]" type = "checkbox" value = "1" disabled <?php print $config['priv']['metadata'] ? 'checked' : ''; ?>></span>
      </p>

      <h2>Other</h2>
      <p>How many seconds should the requests wait for a response before timing out? Since requests are relayed accross many nodes, this needs to be longer in bigger trees.<br />
        Timeout <input name = "timeout" type = "number" min = "0" max = "60" size = "1" value = "<?php print $node_config['timeout']; ?> disabled"> (not yet implemented)
      <br />
      </p>

      <p>How many seconds should validated transactions remain available for the author to confirm, before being cleaned up. Leave 0 to never clean up.<br />
        Validated window <input name = "validated_window" type = "number" min = "0" max = "3600" size = "1" value = "<?php print $node_config['validated_window']; ?> disabled"> (not yet implemented)
      </p>
      <p><input type ="submit" value ="save"></p>
    </form>

  </body>
</html><?php

