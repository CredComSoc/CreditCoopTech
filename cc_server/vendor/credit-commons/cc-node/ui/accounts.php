<?php require './login.php';
// Make a filterable table with all transactions
$all_accounts = $node->getAccountSummary();
ksort($all_accounts);
?>
<form method="GET">
  Show details: <select name="account">
    <option value=""></option>
    <?php foreach (array_keys($all_accounts) as $id) : ?>
    <option value="<?php print $id; ?>"><?php print $id; ?></option>
    <?php endforeach; ?>
  </select>
  <input type="submit" />
</form>
<?php
if (isset($_GET['account'])) {
  $id = $_GET['account'];
  print formatStats($id, $all_accounts[$id], $node->getAccountLimits($id)[0]);
}
else {
  // Print all accounts and balances.
  print allAccounts($all_accounts);
  ?><!--<a href="addaccount.php">Add Account...</a>--><?php
}

// generate a display summarising one account's trade.
function formatStats(string $name, stdClass $data, stdClass $limits) : string {
  $output = '<table><thead><tr><th>'.$name.'</th><th>Actual</th><th>Pending</th></tr></thead>';
  $output .='<tbody><tr><th>Balance <font size="-2">('.$limits->min.' to +'. $limits->max.')</font></th><td>'.$data->completed->balance.'</td><td>'.$data->pending->balance.'</td></tr>';
  $output .='<tr><th>Trades</th><td>'.$data->completed->trades.'</td><td>'.$data->pending->trades.'</td></tr>';
  $output .='<tr><th>Entries</th><td>'.$data->completed->entries.'</td><td>'.$data->pending->entries.'</td></tr>';
  $output .='<tr><th>Volume</th><td>'.$data->completed->volume.'</td><td>'.$data->pending->volume.'</td></tr>';
  $output .='<tr><th>Gross Income</th><td>'.$data->completed->gross_in.'</td><td>'.$data->pending->gross_in.'</td></tr>';
  $output .='<tr><th>Gross Expenditure</th><td>'.$data->completed->gross_out.'</td><td>'.$data->pending->gross_out.'</td></tr>';
  $output .='<tr><th>Partners</th><td>'.$data->completed->partners.'</td><td>'.$data->pending->partners.'</td></tr>';
  return $output.'</tbody></table>';
}

// generate a display summarising all accounts
function allAccounts($data) :string {
  if (count($data) < 20)$cols = 1;
  elseif (count($data) < 40)$cols = 2;
  else $cols = 3;
  $chunk_size = floor(count($data)/$cols);
  $chunks = array_chunk($data, $chunk_size, TRUE);
  $output = [];
  foreach ($chunks as $chunk) {
    $output[] = '<table style="display:inline-block">';
    $output[] = '  <thead>';
    $output[] = '    <tr>';
    $output[] = '      <th>Name</th>';
    $output[] = '      <th>Balance</th>';
    $output[] = '      <th>Volume</th>';
    //$output[] = '      <th>Balance<br />(pending)</th>';
    //$output[] = '      <th>Volume<br />(pending)</th>';
    $output[] = '    </tr>';
    $output[] = '  </thead>';
    $output[] = '  <tbody>';
    foreach ($chunk as $id => $summary) {
      $output[] = "    <tr>";
      $output[] = "      <th>$id</th>";
      $output[] = "      <td>".$summary->completed->balance."</td>";
      $output[] = "      <td>".$summary->completed->volume."</td>";
      //$output[] = "      <td>".$summary->pending->balance."</td>";
      //$output[] = "      <td>".$summary->pending->volume."</td>";
      $output[] = "    </tr>";
    }
    $output[] = '  </tbody>';
    $output[] = '</table>';
  }
  return implode("\n", $output);
}
