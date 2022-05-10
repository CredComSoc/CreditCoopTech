<?php

if (!is_dir('../vendor')) {
  die("Don't forget to run composer update...");
}
require_once '../vendor/autoload.php';
ini_set('display_errors', 1);

const NODE_INI_FILE = '../node.ini';
const ACC_STORAGE_INI_FILE  = '../AccountStore/accountstore.ini';
$node_conf = parse_ini_file(NODE_INI_FILE);
$errs = [];
if (!empty($_SERVER['QUERY_STRING'])){
  if ($node_conf['account_store_url']) {
    require $_SERVER['QUERY_STRING'].'.php'; // only appleis to accounts.php ATM
    exit;
  }
}
if ($_POST) {
  if (!filter_var($_POST['account_store_url'], FILTER_VALIDATE_DOMAIN)) {
    $errs[] = "invalid Account store url";
  }
  if(!empty($_POST['account_store_url']) and !filter_var($_POST['account_store_url'], FILTER_VALIDATE_DOMAIN)) {
    $errs[] = "invalid Account store url";
  }
  if (empty($_POST['db']['name'])) {
    $errs[] = "Database name required";
  }
  if (empty($_POST['db']['user'])) {
    $errs[] = "Database user required";
  }
  if (empty($_POST['acc']['default_max'])) {
    $_POST['acc']['default_max'] = 0;
  }
  if (empty($_POST['acc']['default_min'])) {
    $_POST['acc']['default_min'] = 0;
  }

  $values = $_POST;

  if (!$errs) {
    require './writeini.php';
    $acc = $values['acc'];
    unset($values['acc']);
    replaceIni($values, NODE_INI_FILE);
    replaceIni($acc, ACC_STORAGE_INI_FILE);
    $connection = new mysqli('localhost', $values['db']['user'], $values['db']['pass']);
    $connection->query("DROP DATABASE ".$values['db']['name']);
    $connection->query("CREATE DATABASE ".$values['db']['name']);
    CCNode\Db::connect($values['db']['name'], $values['db']['user'], $values['db']['pass']);
    foreach (explode(';', file_get_contents('install.sql')) as $q) {
      if ($query = trim($q)) {
        CCNode\Db::query($query);
      }
    }
    $node_conf = $values;
    print "Do check that the db has been created and then congratulations; the node should now be installed.<br />";
  }
}
$values = $node_conf + parse_ini_file(ACC_STORAGE_INI_FILE);

// the following form is used once in set up
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
  <head>
    <title>Credit Commons config</title>
    <style>th{background-color: #eee;}</style>
  </head>
  <body>
    <?php if (!empty($errs)) {
      print "<p><font color=red>".implode('<br />', $errs).'</font></p>';
    }?>
    <?php if (!empty($values['db']['name'])) {
      print "<p>Now you can <ul>"
        ."<li><a href=\"index.php?accounts\">Edit the default accounts</a></li>"
        ."<li>Send requests from your own client</li>"
        ."<li>or <a href=\"https://gitlab.com/credit-commons-software-stack/cc-dev-client/-/blob/master/INSTALL.md\">install</a> the developer's client.</li>"
        . "</ul></p>";
    }?>


    <form method="post">
      <h2>Microservices</h2>
      <p title="The reference implementation uses these two microservices (with as yet undocumented apis)">
        Account store <input name = "account_store_url" value = "<?php print $values['account_store_url'] ?: 'http://accounts.'.$_SERVER['HTTP_HOST']; ?>" placeholder = "https://accounts.mydomain.com">
      <br />Business logic <input name = "blogic_service_url" value = "<?php print $values['blogic_service_url'] ?: 'http://blogic.'.$_SERVER['HTTP_HOST']; ?>" placeholder = "https://blogic.mydomain.com">  (optional)
      </p>

      <h2>Database settings</h2>
      <p>Db server <input name = "db[server]" value = "<?php print $values['db']['server']; ?>">
        <br />Db name <input name = "db[name]" value = "<?php print $values['db']['name']; ?>">
        <br />Db user <input name = "db[user]" value = "<?php print $values['db']['user']; ?>">
        <br /><span title="Password is not required for the moment">Db pass <input name = "db[pass]" value = "<?php print $values['db']['pass']; ?>"></span>
      </p>


      <h2>Default values for new accounts</h2>
      <p>Max limit: <input name="acc[default_max]" type="number" min="1" max="1000000" size="3" value="<?php print $values['default_max']; ?>" />
      <br />Min limit: <input name="acc[default_min]" type="number" max="0" min="-1000000" size="3" value="<?php print $values['default_min']; ?>" />
      <p>Accounts are created as:<br />
         <input type="radio" name= "acc[default_status]" value = "1"<?php if (!empty($values['default_status'])) print ' checked'; ?> />Enabled<br />
         <input type="radio" name= "acc[default_status]" value = "0"<?php if (empty($values['default_status'])) print ' checked'; ?> />Disabled
      </p>
      <input type="submit" value="(Re)Install database">
    </form>
  </body>
</html>
