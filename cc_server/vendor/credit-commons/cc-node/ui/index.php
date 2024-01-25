<?php
require 'login.php';
$accounts = $node->getAccountSummary();

$data = [];
foreach ($accounts as $acc_name => $info) {
  $data[] = "['$acc_name', {$info->completed->balance}]";
  sort($data);
}

?>
    <div id = "balance_map" style="width:70%"></div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">google.charts.load("current", {packages:["corechart"]});</script>
    <script type="text/javascript">
      function drawBalanceCharts() {
        var data = google.visualization.arrayToDataTable([
          ["Exchange", "Balance"],
          <?php print implode(",\n    ", $data); ?>
        ]);
        var options = {
          title: "All account balances",
          height: "<?php print count($accounts)*12 + 65; ?>",
          bar: {groupWidth: "95%"},
          fontSize: 10,
        };
        var chart = new google.visualization.BarChart(document.getElementById("balance_map"));
        chart.draw(data, options);
      }
      google.charts.setOnLoadCallback(drawBalanceCharts);
    </script>

  </body>
</html>
