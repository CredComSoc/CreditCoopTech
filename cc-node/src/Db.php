<?php

namespace CCNode;
use CreditCommons\Exceptions\CCFailure;

class Db {

  /**
   * @var mysqli
   */
  protected static $connection;

  /**
   * Connect to the database
   *
   * @return bool false on failure / mysqli MySQLi object instance on success.
   */
  public static function connect($db = '', $user = NULL, $pass = NULL, $server = NULL) : \mysqli {
    if(!isset(static::$connection) or $db) {
      $db = $db ?: getConfig('db.name');
      $db_user = $user?:getConfig('db.user');
      $db_pass = isset($pass)?$pass:getConfig('db.pass');
      $db_server = $server?:getConfig('db.server');
      static::$connection = new \mysqli($db_server, $db_user, $db_pass, $db);
    }
    return static::$connection;
  }

  /**
   * Query the database
   *
   * @param string $query The query string
   * @return mixed The result of the mysqli::query() function
   */
  public static function query(string $query) : \mysqli_result|bool {
    $connection = static::connect();
    $result = $connection->query($query);
    if ($error = static::error()) {
      throw new CCFailure('Database error:' . $error .": ".$query);
    }
    if (strtoupper(substr($query, 0, 6)) == 'INSERT') {
      return $connection->insert_id;
    }
    return $result;
  }

  /**
   * Fetch the last error from the database
   *
   * @return string Database error message
   */
  public static function error() {
    $connection = static::connect();
    return $connection->error;
  }

  public function lastId() : int {
    $connection = $this->connect();
    $result = $connection->insert_id;
    return $result;
  }

  /**
   * Remove from the temp table.
   * @param type $uuid
   */
  public static function delete($uuid) {
    static::query("DELETE FROM temp where uuid = '$uuid'");
  }

  public static function printAll() {
    global $config;
    $html = static::printTable($config['node_name'], 'users').static::printTable($config['node_name'], 'temp').static::printTable($config['node_name'], 'ledger');
    return $html ? : "the database is empty";
  }

  public static function printTable($instance_name, $table) {
    while ($row = static::query('SELECT * from $table')->fetch_assoc()) {
      $rows[] = $row;
    }
    if ($rows) {
      $theader = '<thead><tr>';
      foreach (array_keys(reset($rows)) as $column_heading) {
        $theader .= '<th>'.$column_heading.'</th>';
      }
      $theader.="</tr></thead>\n";
      $tbody = '<tbody>';
      foreach ($rows as $id => $row) {
        $tbody .= '<tr>';
        foreach ($row as $field) {
          $tbody .= '<td>'.$field.'</td>';
        }
        $tbody .= '</tr>';
      }
      $tbody .= '</tbody>';
      return "<table><caption>$instance_name</caption>".$theader.$tbody.'</table>';
    }
  }

}
