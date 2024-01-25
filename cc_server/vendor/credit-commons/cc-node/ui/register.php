<?php require './login.php';

?>
    <h3>Register a new (local) transaction</h3>
    <form>

     Payee  <select name="payee"><?php print getLocalOptions(); ?></select><br />
     Payer <select name="payer"><?php print getLocalOptions(); ?></select><br />
     Quantity <input type="number"><br />
     Description <input name="description" /><br />
     Type: 3rdparty <br />
    </form>
  </body>
</html><?php
function getLocalOptions() {
  static $names = [];
  if (empty($names)) {
    $names = \CCNode\accountStore()->filter(local: TRUE, limit: 0, full: FALSE);
  }
  return $names;
}
