<?php
if (!is_dir('../vendor')) {
  die("Don't forget to run composer update...");
}
require_once '../vendor/autoload.php';
ini_set('display_errors', 1);
define ('ACCOUNT_STORE', '../AccountStore/store.json');

const NODE_INI_FILE = '../node.ini';
$config = parse_ini_file(NODE_INI_FILE);
$abs_path = explode('/', $config['abs_path']);
end($abs_path);
$trunkward_name = prev($abs_path);
$accounts = editable_accounts();
$errs = [];
// the following form is used once in set up
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
  <head>
    <title>Credit Commons config</title>
    <style>th{background-color: #eee;} li{display:inline-block; padding:0 1em;}</style>
  </head>
  <body>
    <?php if (!empty($errs)) {
      print "<p><font color=red>".implode('<br />', $errs).'</font></p>';
    }?>
    <?php if (!empty($config['db']['name'])): ?>
      <ul>
        <li><a href="/config/index.php">Setup</a></li>
        <li><a href="/config/index.php?accounts">Edit accounts</a></li>
        <li><a href="/config/index.php?general">Settings</a></li>
      <?php if (isset($accounts[$trunkward_name]) and (bool)$trunkward_name == (bool)$accounts[$trunkward_name]->url ): ?>
        <li><a href="https://gitlab.com/credit-commons-software-stack/cc-dev-client/-/blob/master/INSTALL.md">Install</a> developer client</li>
        <li>call <a href="<?php print $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];?>"><?php print end($abs_path);?></a> from your client</li>
      <?php endif; ?>
        </ul><hr />
    <?php endif; ?>
<?php
if (!empty($_SERVER['QUERY_STRING'])){
  if ($config['account_store_url']) {
    require $_SERVER['QUERY_STRING'].'.php'; // only appleis to accounts.php ATM
    exit;
  }
}?>
<?php
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
  $config = $_POST;

  if (!$errs) {
    require './writeini.php';
    replaceIni($config, NODE_INI_FILE);
    $connection = new mysqli('localhost', $config['db']['user'], $config['db']['pass']);
    $connection->query("DROP DATABASE ".$config['db']['name']);
    $connection->query("CREATE DATABASE ".$config['db']['name']);
    CCNode\Db::connect($config['db']['name'], $config['db']['user'], $config['db']['pass'], $config['db']['server']);
    foreach (explode(';', file_get_contents('install.sql')) as $q) {
      if ($query = trim($q)) {
        CCNode\Db::query($query);
      }
    }
    $config = $config;
    print "Do check that the db has been created and then congratulations; the node should now be installed.<br />";
  }
}?>

    <form method="post">
      <h2>Microservices</h2>
      <p title="The reference implementation uses these two microservices (with as yet undocumented apis)">
        Account store <input name = "account_store_url" value = "<?php print $config['account_store_url'] ?: 'http://accounts.'.$_SERVER['HTTP_HOST']; ?>" placeholder = "https://accounts.mydomain.com">
      <br />Business logic <input name = "blogic_service_url" value = "<?php print $config['blogic_service_url'] ?: 'http://blogic.'.$_SERVER['HTTP_HOST']; ?>" placeholder = "https://blogic.mydomain.com">  (optional)
      </p>

      <h2>Database settings</h2>
      <p>Db server <input name = "db[server]" value = "<?php print $config['db']['server']; ?>">
        <br />Db name <input name = "db[name]" value = "<?php print $config['db']['name']; ?>">
        <br />Db user <input name = "db[user]" value = "<?php print $config['db']['user']; ?>">
        <br /><span title="Password is not required for the moment">Db pass <input name = "db[pass]" value = "<?php print $config['db']['pass']; ?>"></span>
      </p>

      <h2>Absolute path</h2>
      <p>The address of this node in a credit commons tree. Node names starting with the trunk, separated by slashes, and ending with the name of the current node. If this path is more than one item long, you must <a href="/config/index.php?accounts">provide a url</a> for the trunkwards account.<br />
      <input name = "abs_path" placeholder="trunk/branch/thisnode" value = "<?php print $config['abs_path']; ?>">
      </p>

      <input type="submit" value="(Re)Install database">
    </form>
  </body>
</html><?php
/**
 * load or save the set of accounts directly to the file.
 * @param array $accounts
 * @return array
 */
function editable_accounts(array $accounts = []) : array {
  if ($accounts) {//save
    foreach ($accounts as $id => &$account) {
      $account->id = $id;
      if ($account->max === '')$account->max = NULL;
      if ($account->min === '')$account->min = NULL;
      $account->admin = (bool)@$account->admin;
    }
    file_put_contents(ACCOUNT_STORE, json_encode($accounts));
    return [];
  }
  else {
    return (array)json_decode(file_get_contents(ACCOUNT_STORE));
  }
}
?>