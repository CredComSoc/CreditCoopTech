<?php require './login.php';
// Make a filterable table with all transactions
$all_accounts = array_keys($node->getAccountSummary());
sort($all_accounts);
$_GET += ['limit' => 25, 'offset'=> 0, 'dir' => 'desc', 'sort' => 'written', 'author' => NULL, 'payee' => NULL, 'payer' => NULL, 'state' => NULL, 'type' => NULL];
$params = array_filter($_GET);
$params['limit']++; // request one m$paramsore than we want to display
[$count, $transactions, $transitions] = $node->filterTransactions($params);
?>
<h3>Transactions</h3>
<form method="GET">
  <p>
    Author: <select name="author">
      <option value=""> - any - </option>
      <?php foreach ($all_accounts as $id) : ?>
      <option value="<?php print $id; ?>" <?php if ($_GET['author'] == $id) print 'selected';?>><?php print $id; ?></option>
      <?php endforeach; ?>
    </select>
    Payee: <select name="payee">
      <option value=""> - any - </option>
      <?php foreach ($all_accounts as $id) : ?>
      <option value="<?php print $id; ?>" <?php if ($_GET['payee'] == $id) print 'selected';?>><?php print $id; ?></option>
      <?php endforeach; ?>
    </select>
    Payer: <select name="payer">
      <option value=""> - any - </option>
      <?php foreach ($all_accounts as $id) : ?>
      <option value="<?php print $id; ?>" <?php if ($_GET['payer'] == $id) print 'selected';?>><?php print $id; ?></option>
      <?php endforeach; ?>
    </select>
    State: <select name="state">
      <option value=""> - any - </option>
      <option value="pending" <?php if ($_GET['state'] == 'pending') print 'selected';?>>Pending</option>
      <option value="completed" <?php if ($_GET['state'] == 'completed') print 'selected';?>>Completed</option>
    </select>
    Type: <select name="type">
      <option value=""> - any - </option>
      <?php foreach (workflow_names() as $id => $title) : ?>
      <option value="<?php print $id; ?>" <?php if ($_GET['type'] == $id) print 'selected';?>><?php print $title; ?></option>
      <?php endforeach; ?>
    </select>
  </p>
  <p>Order
    <select name="sort">
      <option value="quant" <?php if ($_GET['sort'] == 'quant') print 'selected';?>>Quantity</option>
      <option value="state" <?php if ($_GET['sort'] == 'state') print 'selected';?>>State</option>
      <option value="written" <?php if ($_GET['sort'] == 'written') print 'selected';?>>Date</option>
    </select>
  <input type="radio" id = "dir1" name="dir" value="asc" <?php if ($_GET['dir'] == 'asc') print 'checked';?>><label for="dir1">Asc</label>
  <input type="radio" id = "dir2" name="dir" value="desc" <?php if ($_GET['dir'] == 'desc') print 'checked';?>><label for="dir2">Desc</label>
  Limit: <select name="limit">
    <option value="10" <?php if ($_GET['limit'] == 10) print 'selected';?>>10</option>
    <option value="25" <?php if ($_GET['limit'] == 25) print 'selected';?>>25</option>
    <option value="100" <?php if ($_GET['limit'] == 100) print 'selected';?>>100</option>
  </select>
  <p><!-- returns YYYY-MM-DD -->
    From <input type="date" name="since" />
    To <input type="date" name="until" />
  </p>
  </p>
  <input type="submit" />
</form>
<table>
  <thead>
    <th>Last updated</th>
    <th>Payee</th>
    <th>Payer</th>
    <th>Amount</th>
    <th>Description</th>
    <th>Author</th>
    <th>State</th>
    <th>Type</th>
    <th>Version</th>
    <th>UUID</th>
    <th>Actions (todo)</th>
  </thead>
  <tbody>
    <?php if (empty($transactions)) print '<tr>No transactions</tr>';?>
    <?php /** @var CCNode\Transaction\Transaction $t */ ;
    for ($i=0; $i<$_GET['limit']; $i++) : $t = $transactions[$i];?><tr>
        <td><?php print $t->written; ?></td>
        <td><a href="accounts.php?account=<?php print $t->entries[0]->payee->id; ?>"><?php print $t->entries[0]->metadata->{$t->entries[0]->payee->id}??$t->entries[0]->payee->id; ?></a></td>
        <td><a href="accounts.php?account=<?php print $t->entries[0]->payer->id; ?>"><?php print $t->entries[0]->metadata->{$t->entries[0]->payer->id}??$t->entries[0]->payer->id; ?></a></td>
        <td align="right"><?php print $t->entries[0]->quant; ?></td>
        <td><?php print $t->entries[0]->description; ?></td>
        <td><?php print $t->entries[0]->author; ?></td>
        <td><?php print $t->state; ?></td>
        <td><?php print $t->type; ?></td>
        <td><?php print $t->version; ?></td>
        <td><?php print $t->uuid; ?></td>
        <td><?php print $t->actionLinks($transitions[$t->uuid]); ?></td>
      <?php endfor; ?>
      </tr>
    </body>
  </table>
<div class="pager" style="margin: 0 auto">
  <?php
  if ($_GET['offset']) {
    $params = $_GET;
    $params['offset'] = $_GET['offset'] - $_GET['limit'];
    $query = http_build_query($params);
    print '<a href="?'.$query.'">&lt;&lt;prev</a>&nbsp';
  }
  if (count($transactions)) {
    $params = $_GET;
    $params['offset'] = $_GET['offset'] + $_GET['limit'];
    $query = http_build_query($params);
    print '&nbsp<a href="?'.$query.'">next &gt;&gt;</a>';
  }
  ?>
</div>
</html><?php

function workflow_names() :array {
  $results = [];
  foreach ((array)json_decode(file_get_contents('../workflows.json')) as $info) {
    $results[$info->id] = $info->label;
  }
  return $results;
}
