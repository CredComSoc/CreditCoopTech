<?php

namespace Examples;

/**
 * Class for reading account data from accountstore.json file.
 * Performance is not an issue as groups should never exceed more than a few hundred members.
 */
class AccountManager implements \Iterator, \ArrayAccess, \Countable {

  /**
   * @var Record[]
   */
  public $accounts = [];

  /**
   * Needed for array interface.
   * @var int
   */
  private $pos = 0;

  function __construct(string $json_store_filepath) {
    if (!file_exists($json_store_filepath)) {
      throw new \CreditCommons\Exceptions\CCFailure("$json_store_filepath not found from ".getcwd());
    }
    $accs = json_decode(file_get_contents($json_store_filepath));
    if (!$accs) {
      throw new \CreditCommons\Exceptions\CCFailure("Badly formatted json at $json_store_filepath");
    }
    $accs = (array)json_decode(file_get_contents($json_store_filepath));
    foreach ($accs as $id => $data) {
      $class  = !empty($data->url) ? '\Examples\RemoteRecord' : '\Examples\UserRecord';
      $this->accounts[$id] = new $class($data);
    }
    ksort($this->accounts);
  }

  function addAccount(Record $record) {
    $this->accounts[$record->id] = $record;
    $this->save();
  }

  /**
   * @param string $str_fragment
   */
  function filterByName(string $str_fragment) {
    $this->accounts = array_filter(
      $this->accounts,
      function ($a) use ($str_fragment) {return stripos(haystack: $a->id, needle: $str_fragment) !== FALSE;}
    );
  }

  /**
   * @param bool $local
   *   TRUE for only local accounts, FALSE for only remote accounts
   */
  function filterByLocal(bool $local) {
    $class = $local ? 'Examples\UserRecord' : 'Examples\RemoteRecord';
    $this->accounts = array_filter(
      $this->accounts,
      function ($a) use ($class) {return $a instanceof $class;}
    );
  }

  /**
   * @param bool $admin
   *   TRUE for only local accounts, FALSE for only remote accounts
   */
  function filterByAdmin(bool $admin) {
    $this->accounts = array_filter(
      $this->accounts,
      function ($a) use ($admin) {return $a->admin == $admin;}
    );
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

  function key() : mixed {
    return $this->pos;
  }

  function valid() : bool {
    return isset($this->accounts[$this->pos]);
  }

  function current() : mixed {
    return $this->accounts[$this->pos];
  }

  function rewind() : void {
    $this->pos = 0;
  }

  function next() : void {
    $this->pos++;
  }

  public function offsetExists($offset) : bool {
    return array_key_exists($offset, $this->accounts);
  }

  public function offsetGet($offset) : mixed {
    return $this->accounts[$offset];
  }
  public function offsetSet($offset, $value) : void {
    $this->accounts[$offset] = $value;
  }
  public function offsetUnset($offset) : void {
    unset($this->accounts[$offset]);
  }
  public function count() : int {
    return count($this->accounts);
  }

  // alias of offsetExists.
  public function has($id) {
    return array_key_exists($id, $this->accounts);
  }
}

