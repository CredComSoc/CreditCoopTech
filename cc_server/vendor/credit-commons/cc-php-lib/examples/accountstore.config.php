<?php
/**
 * Config form for Example AccountStore
 * Included by cc-server/config/index.php
 */

if (!is_writable(DEFAULT_ACCOUNT_STORE_DATA_FILE)) {
  $errs[] = DEFAULT_ACCOUNT_STORE_DATA_FILE . " is not writable";
}
$trunkward_name = $config->trunkwardAcc;
if ($_POST) {
  unset($_POST['submit']);
  if (!$errs) {
    require './writeini.php';
    // Save the new local account
    if ($_POST['user']['new']['id'] and $_POST['user']['new']['key']) {
      add_account('user', $_POST['user']['new']);
    }
    unset($_POST['user']['new']);
    // Save the new remote account
    if (@$_POST['node']['new']['id'] and $_POST['node']['new']['url']) {
      add_account('node', $_POST['node']['new']);
    }
    unset($_POST['node']['new']);
    // Resave existing local accounts
    if (isset($_POST['user'])) {
      foreach ($_POST['user'] as $id => $fields) {
        mod_account('user', $id, $fields);
      }
    }
    // Resave existing remote accounts
    if (isset($_POST['node'])) {
      foreach ($_POST['node'] as $id => $fields) {
        mod_account('node', $id, $fields);
      }
    }
    // Save or resave the Trunkward account
    if (count($abs_path) > 1) {
      $accs = editable_accounts();
      if (empty($_POST['trunkward']['url']) and empty($accs[$trunkward_name]->url)) {
        die('trunkward account must have a url, or remove it from the path in the general settings.');
      }
      if (!isset($accs[$trunkward_name])) {
        add_account('node', $_POST['trunkward'] + ['id' => $trunkward_name]);
      }
      elseif (isset($accs[$trunkward_name])) {
        mod_account('node', $trunkward_name, $_POST['trunkward']);
      }
    }
  }
}

$accs = editable_accounts();
config_connect($config);


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
  <head>
    <title>Credit Commons config</title>
    <style>th{background-color: #eee;}</style>
  </head>
  <body>
    <h1>Credit Commons account settings</h1>
    <p>This form edits the file <?php print DEFAULT_ACCOUNT_STORE_DATA_FILE; ?>. Hover over things for more info. </p>
    <?php if ($errs)
      print "<p><font color=red>".implode('<br />', $errs).'</font>';
    ?><form method="post">
      <h2>User accounts</h2>
      <table cellpadding="2">
        <thead>
          <tr>
            <th title = "Wallet id, must be unique on this node">Name</th>
            <th title = "Password-like string">API Key</th>
            <th title = "Minimum/Maximium balance REQUIRED">Min/Max</th>
            <th title = "Checked if this account has admin privileges">Admin</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $users = array_filter($accs, function($a){return !empty($a->key);});
        foreach ($users as $id => $acc) : ?>
          <tr>
            <th title = "Wallet id, must be unique on this node">
              <?php print $id;?><!--<input type="hidden" name="user[<?php print $id;?>][id]" value = "<?php print $id;?>">-->
            </th>
            <td title = "Password-like string">
              <input name="user[<?php print $id;?>][key]" value="<?php print $acc->key;?>" size = "6">
            </td>
            <?php print minmax_cell('td','user['.$id.']', $acc); ?>
            <td title = "Checked if this account has admin privileges">
              <input name="user[<?php print $id;?>][admin]" type="checkbox" value = "1" <?php print !empty($acc->admin)?'checked':'';?>>
            </td>
          </tr>
          <?php endforeach; ?>
          <tr>
            <td title = "Wallet id, must be unique on this node">
              <input name="user[new][id]" size = "8" placeholder = "new_acc_id">
            </td>
            <td title = "Password-like string">
              <input name="user[new][key]" size = "8">
            </td>
            <?php print minmax_cell('td', 'user[new]'); ?>
            <td title = "Checked if this account has admin privileges">
              <input name="user[new][admin]" type="checkbox" value = "1" >
            </td>
          </tr>
        </tbody>
      </table>

      <h2>Leafward nodes</h2>
      <p>Special accounts which are controlled by other, credit commons nodes.
      <table>
      <thead>
        <tr>
          <th title = "Wallet id, must be unique on this node">Node name</th>
          <th title = "Url of the node">Node url</th>
          <th title = "Spoof nodes only">Authentication</th>
          <th title = "Minimum/Maximum balance (override default @todo)">Min/Max</th>
          <th title = "Use this in the cc-auth header to connect as this account">Current hash</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $nodes = array_filter(
        $accs,
        function($a, $id) use ($trunkward_name){return !empty($a->url) and $id <> $trunkward_name;},
        ARRAY_FILTER_USE_BOTH
      );
      foreach ($nodes as $id => $acc) : ?>
      <tr>
        <th><?php print $id;?></th>
        <td title = "Url of the node">
          <input name="node[<?php print $id;?>][url]" placeholder="http://mynode.net" value="<?php print $acc->url;?>" size = "8">
        </td>
        <td title = "Spoof nodes only">
          <input name="node[<?php print $id;?>][key]" value="<?php print $acc->key;?>" size = "8">
        </td>
        <?php print minmax_cell('td', 'node['.$id.']', $acc); ?>
        <td title = "Use this in the cc-auth header to connect as this account"><?php print last_hash($id);?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <td title = "Wallet id, must be unique on this node">
            <input name="node[new][id]" size = "8" placeholder = "new_account_id" value="">
        </td>
        <td title = "Url of the remote node">
            <input name="node[new][url]" size = "8"  value="">
        </td>
        <td title = "Spoof nodes only">
          <input name="node[new][key]" value="<?php print $acc->key;?>" size = "8">
        </td>
        <?php print minmax_cell('td', 'node[new]'); ?>
        <td></td>
      </tr>
      </tbody>
      </table>
<?php if ($trunkward_name) : ?>
      <h2>Trunkward node</h2>
      <p>The trunkward node records the balance of trade with the rest of the world.
        <?php $trunkward_acc = $accs[$trunkward_name]??NULL;
        if ($trunkward_name and !$trunkward_acc) {
          print "<font color=red>You MUST enter a url to correspond with the absolute path: <strong>$config->absPath</strong> referenced in the main config.</font>";
        }?>
      </p>
      <table>
        <thead>
          <tr>
            <th title = "This is how this node identifies itself to the trunkward node.">Trunkward node name</th>
            <th title = "Url of the Trunkward node">Url</th>
            <th title = "Minimum/Maximum balance with respect to the rest of the world. (override default @todo)">Min/Max</th>
            <th title = "Use this in the cc-auth header to connect as this account">Current hash</th>
            </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <?php print $trunkward_name; ?>
            </td>
            <td>
              <input name="trunkward[url]" size = "8" placeholder="http://mynode.net" value="<?php print $trunkward_acc?$trunkward_acc->url:''; ?>" <?php if ($trunkward_acc and $trunkward_acc->url)print ' disabled';?>>
              <?php if ($trunkward_name and !$trunkward_acc) :?><font color=red>*</font><?php endif; ?>
            </td>
            <td>
              <?php print minmax_cell('span', 'trunkward', $trunkward_acc); ?>
            </td>
            <td title = "Use this in the cc-auth header to connect as this account"><?php print last_hash($trunkward_name);?></td>
          </tr>
        <tbody>
      </table>
<?php endif; ?>
      </br />
      <input type="submit" value="Save">
    </form>
  </body>
</html><?php


function add_account($type, $fields): void {

  $id = $fields['id'];
  unset($fields['id']);
  $fields = array_filter($fields, 'strlen');
  $fields += [
    'admin' => 0
  ];
  if ($type == 'node') {
    if (empty($fields['url'])) {
      die('bad data');
    }
    else {
      unset($fields['key']);
    }
  }
  elseif ($type == 'user') {
    if (empty($fields['key'])) {
      die('bad data');
    }
    else {
      unset($fields['url']);
    }
  }
  // edit the csv file directly.
  $accounts = editable_accounts();
  $accounts[$id] = (object)$fields;
  editable_accounts($accounts);
}

function mod_account($type, $id, $fields) : void {
  // Ensure there's a value in case of empty checkboxes
  $fields['admin'] = (int)!empty($fields['admin']);
  $accounts = editable_accounts();
  $accounts[$id] = (object)($fields + (array)$accounts[$id]);
  editable_accounts($accounts);
}

function minmax_cell($tag, $type, $acc = NULL) {
  ?><<?php print $tag; ?> title = "Min/max balance REQUIRED">
    <input name="<?php print $type; ?>[min]" type="number" min="-999999" max="0" size="4" placeholder = "<0" value="<?php print $acc?$acc->min:'-100';?>" />
    <input name="<?php print $type; ?>[max]" type="number" max="999999" min="0" size="4"  placeholder = ">0" value="<?php print $acc?$acc->max:'100';?>" />
  </<?php print $tag; ?>><?php
}

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
    file_put_contents(DEFAULT_ACCOUNT_STORE_DATA_FILE, json_encode($accounts));
    return [];
  }
  else {
    return (array)json_decode(file_get_contents(DEFAULT_ACCOUNT_STORE_DATA_FILE));
  }
}

function last_hash(string $acc_id) : string {
  $query = "SELECT hash "
    . "FROM hash_history "
    . "WHERE acc = '$acc_id' "
    . "ORDER BY id DESC LIMIT 0, 1";
  if ($row = CCNode\Db::query($query)->fetch_object()) {
    return (string)$row->hash;
  }
  else { //No hash because this account has never traded to.
    return '';
  }
}