<?php

namespace CreditCommons;
use CreditCommons\Leaf\FlatEntry;

/**
 * Just declared the properties of the transaction, according to what is
 * delivered back to the client from POST /transaction or GET /transactions
 */
class TransactionDisplay  {
  use CreateFromValidatedStdClassTrait;

  public function __construct(
    public string $uuid,
    public string $type,
    public string $state,
    /** @var Entry[] $entries */
    public array $entries,
    public string $written,
    public int $version
  ) { }

  static function create(\stdClass $data) : static {
    $data->version = $data->version??-1;
    $data->written = $data->written??'';
    $data->state = $data->state??TransactionInterface::STATE_INITIATED;
    static::validateFields($data);
    return new static(...(array)$data);
  }


  /**
   * {@inheritDoc}
   */
  public static function createFromJsonClass(\stdClass $data, array $transitions) : static {
    if (get_called_class() == get_class()) {
      throw new CCFailure('Cannot call base transaction class directly.');
    }
    foreach ($data->entries as &$entry) {
      $entry = new FlatEntry(...(array)$entry);
    }

    $t = static::create($data);
    $t->transitions = $transitions;
    return $t;
  }

  // A json object to pass back to the leaf
  function JsonDisplayable() : array {
    return [
      'uuid' => $this->uuid,
      'written' => $this->written,
      'state' => $this->state,
      'type' => $this->type,
      'version' => $this->version,
      'entries' => $this->entries
    ];
  }


  /**
   * Render the transaction action links as forms which can post. (Client side only)
   *
   * @param array $transitions
   * @return string
   */
  public function actionLinks(array $transitions) : string {
    if ($transitions) {
      $output[] = '<form method="post" class="inline" action="">';
      $output[] = '<input type="hidden" name="uuid" value="'.$this->uuid.'">';
      foreach ($this->getWorkflow()->actionLabels($this->state, $transitions) as $target_state => $label) {
        $output[] = '<button type="submit" name="stateChange" value="'.$target_state.'" class="link-button">'.$label.'</button>';
      }
      $output[] = '</form>';
    }
    else {
      $output[] = "<span title = \"Current user is not permittions to do anything to this '$this->type' transaction\">(no actions)</span>";
    }
    return implode($output);
  }

}
