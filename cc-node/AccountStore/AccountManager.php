<?php

namespace AccountStore;

/**
 * Class for reading and writing account data from a csv file.
 * Performance is not an issue as groups should never exceed more than a few hundred members.
 */
class AccountManager implements \Iterator, \ArrayAccess, \Countable {

  const FILESTORE = 'store.json';

  /**
   * @var Record[]
   */
  public $accounts = [];

  /**
   * Needed for array interface.
   * @var int
   */
  private $pos = 0;

  function __construct() {
    $accs = (array)json_decode(file_get_contents(self::FILESTORE));
    foreach ($accs as $id => $data) {
      $class  = !empty($data->url) ? '\AccountStore\RemoteRecord' : '\AccountStore\UserRecord';
      $this->accounts[$id] = new $class($data);
    }
  }


  function save() {
    file_put_contents(self::FILESTORE, json_encode($this->accounts, JSON_PRETTY_PRINT));
  }

  function addAccount(Record $record) {
    $this->accounts[$record->id] = $record;
    $this->save();
  }

  /**
   * @param string $string
   * This looks weird and next time I would check the login credentials differently.
   */
  function filterByAuth(string $string) {
    $this->accounts = array_filter($this->accounts, function ($a) use ($string) {
      $auth = $a->key??$a->url;
      return $auth === $string;
    });
  }

  /**
   * @param string $string
   */
  function filterByName(string $string) {
    $this->accounts = array_filter($this->accounts, function ($a) use ($string) {
      return stripos($a->id, $string) !== FALSE;
    });
  }

  /**
   * @param bool $status
   *   True for active, FALSE for Blocked
   */
  function filterByStatus(bool $status) {
    $this->accounts = array_filter($this->accounts, function ($a) use ($status) {
      return $status == $a->status;
    });
  }

  /**
   * @param bool $local
   *   TRUE for local accounts, FALSE for remote accounts
   */
  function filterByLocal(bool $local) {
    $class = $local ? 'AccountStore\UserRecord' : 'AccountStore\RemoteRecord';
    $this->accounts = array_filter(
      $this->accounts,
      function ($a) use ($class) {return $a instanceof $class;}
    );
  }

  /**
   * View all the accounts in the list.
   *
   * @param string $view_mode
   *   can be full, or name
   * @return stdClass[]
   */
  function view() : array {
    $results = array_map(
      function ($a) {return $a->view();},
      $this->accounts
    );
    return array_values($results);
  }


  /**
   * @param string $id
   * @return bool
   */
  function availableName($id) {
    return !isset($this[$id]) ;
  }

  function validName($id) {
    return preg_match('/^[a-z0-9@.]{1,32}$/', $id) and strlen($id) < 32;
  }

  static function validateFields(array $fields) : array {
    $errs = Record::validateFields($fields);
    return $errs;
  }

  function key() {
    return $this->pos;
  }

  function valid() {
     return isset($this->accounts[$this->pos]);
  }

  function current() {
    return $this->accounts[$this->pos];
  }

  function rewind() {
    $this->pos = 0;
  }

  function next() {
    ++$this->pos;
  }

  public function offsetExists($offset) : bool {
    return array_key_exists($offset, $this->accounts);
  }

  public function offsetGet($offset) {
    return $this->accounts[$offset];
  }
  public function offsetSet($offset, $value) : void {
    $this->accounts[$offset] = $value;
  }
  public function offsetUnset($offset) : void {
    trigger_error('Cannot delete accounts', E_USER_WARNING);
  }
  public function count() : int {
    return count($this->accounts);
  }

  // alias of offsetExists.
  public function has($id) {
    return array_key_exists($id, $this->accounts);
  }
}

