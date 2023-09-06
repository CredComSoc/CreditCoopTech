<?php
ini_set('display_errors', 1);

if (!is_dir('../vendor')) {
  die("vendor directory is missing relative to ".__FILE__." - did you forget to run composer update?");
}
require_once '../vendor/autoload.php';

const NODE_INI_FILE = '../node.ini';
const DEFAULT_ACCOUNT_STORE_DATA_FILE = '../accountstore.json';

if (!is_file(NODE_INI_FILE)) {
  copy('../vendor/credit-commons/cc-node/node.ini.example', NODE_INI_FILE);
  copy('../vendor/credit-commons/cc-node/workflows.json.example', 'workflows.json');
  echo "node.ini and workflows.json files created.";
}
$config = new \CCNode\ConfigFromIni(parse_ini_file(NODE_INI_FILE));

$abs_path = explode('/', $config->absPath);
end($abs_path);
$trunkward_name = prev($abs_path);
$errs = [];
// the following form is used once in set up
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
  <head>
    <title>Credit Commons config</title>
    <style>th{background-color: #eee;} li{display:inline-block; padding:0 1em;}</style>
    <link rel="shortcut icon" href="http://creditcommons.net/themes/credcom/img/favicon.ico" />
  </head>
  <body>
    <?php if (!empty($errs)) {
      print "<p><font color=red>".implode('<br />', $errs).'</font></p>';
    }?>
    <?php if (!empty($config->dbCreds['name'])): ?>
      <ul>
        <li><a href="/config/index.php">Setup</a></li>
        <li><a href="/config/index.php?general">Settings</a></li>
        <?php if (file_exists(DEFAULT_ACCOUNT_STORE_DATA_FILE)) : ?>
        <li><a href="/config/index.php?accounts">Edit accounts</a></li>
        <?php endif; ?>
        </ul><hr />
    <?php endif; ?>
<?php
if (!empty($_SERVER['QUERY_STRING'])){
  if ($config->accountStore == '\Examples\AccountStore') {
    require '../vendor/credit-commons/cc-php-lib/examples/accountstore.config.php'; // only applies to accounts.php ATM
    exit;
  }
}?>
<?php
if ($_POST) {
  $accStore = $_POST['account_store'];
  if (!filter_var($accStore, FILTER_VALIDATE_DOMAIN) and !class_exists($accStore)) {
    $errs[] = "Account store given is not a domain and does not exist as a class";
  }
  elseif ($accStore == '\CCNode\AccountStoreDefault' and !class_exists('\AccountStore\AccountManager')) {
    echo "Do 'composer require credit-commons/cc-demo-accountstore' to use the default AccountStore";
  }
  elseif ($accStore == '\CCNode\AccountStoreDefault' and !file_exists(DEFAULT_ACCOUNT_STORE_DATA_FILE)) {
    copy('../vendor/credit-commons/cc-php-lib/examples/accountstore.json.example', DEFAULT_ACCOUNT_STORE_DATA_FILE);
    echo DEFAULT_ACCOUNT_STORE_DATA_FILE." file created.";
  }
  if (empty($_POST['db']['name'])) {
    $errs[] = "Database name required";
  }
  if (empty($_POST['db']['user'])) {
    $errs[] = "Database user required";
  }

  if (!$errs) {
    require './writeini.php';
    $config = replaceIni($_POST, NODE_INI_FILE);
    $connection = new mysqli('localhost', $config->dbCreds['user'], $config->dbCreds['pass']);
    $connection->query("DROP DATABASE IF EXISTS".$config->dbCreds['name']);
    $connection->query("CREATE DATABASE ".$config->dbCreds['name']);
    config_connect($config);
    foreach (explode(';', file_get_contents('../vendor/credit-commons/cc-node/install.sql')) as $q) {
      if ($query = trim($q)) {
        $success = CCNode\Db::query($query);
      }
    }
    print $success ? "The db was (re)installed." : "There was a problem creating the database.";
  }
}?>

    <form method="post">
      <h2>Microservices</h2>
      <p title="The reference implementation uses these two microservices (with as yet undocumented apis)">
        Account store <input name = "account_store" value = "<?php print $config->accountStore ?: 'http://accounts.'.$_SERVER['HTTP_HOST']; ?>" placeholder = "https://accounts.mydomain.com"><font color="red">*</font> (PHP class which implements the AccountStoreInterface or url which implements AccountStore REST API.)
      <br />Business logic <input name = "blogic_mod" value = "<?php print $config->blogicMod ?: 'http://blogic.'.$_SERVER['HTTP_HOST']; ?>" placeholder = "https://blogic.mydomain.com"> (optional. PHP class which implements the BlogicInterface or url which implements Blogic REST API.)
      </p>

      <h2>Database settings</h2>
      <p>Db server <input name = "db[server]" value = "<?php print $config->dbCreds['server']; ?>">
        <br />Db name <input name = "db[name]" value = "<?php print $config->dbCreds['name']; ?>">
        <br />Db user <input name = "db[user]" value = "<?php print $config->dbCreds['user']; ?>">
        <br /><span title="Password is not required for the moment">Db pass <input name = "db[pass]" value = "<?php print $config->dbCreds['pass']; ?>"></span>
      </p>

      <h2>Absolute path</h2>
      <p>The address of this node in a credit commons tree. Node names starting with the trunk, separated by slashes, and ending with the name of the current node. If this path is more than one item long, you must <a href="/config/index.php?accounts">provide a url</a> for the trunkwards account.<br />
      <input name = "abs_path" placeholder="trunk/branch/thisnode" value = "<?php print $config->absPath; ?>">
      </p>

      <input type="submit" value="(Re)Install database">
    </form>
  </body>
</html>

<p><a href="https://gitlab.com/credit-commons/cc-dev-client/-/blob/master/INSTALL.md">About</a> the developer client</p>
<?php
function config_connect($config) {
  CCNode\Db::connect(
    $config->dbCreds['name'],
    $config->dbCreds['user'],
    $config->dbCreds['pass'],
    $config->dbCreds['server']
  );
}
