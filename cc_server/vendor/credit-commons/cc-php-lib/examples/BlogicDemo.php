<?php

namespace Examples;

/**
 * Example class showing how to do business logic. Assumes an account named 'admin'.
 */
class BlogicDemo implements \CreditCommons\BlogicInterface {

  /**
   * The name of the account into which fees are paid.
   * @var CreditCommons\Account
   */
  protected $feeAcc;

  protected $nodeName;

  function __construct(string $acc_name = 'admin') {
    global $cc_config;
    $this->feeAcc = $acc_name;
    $this->nodeName = $cc_config->nodeName;
  }

  /**
   * Add a fee of 1 to both payer and payee
   */
  public function addRows(string $type, string $payee, string $payer, int $quant, \stdClass $metadata, string $description = '') : array {
    $additional_rows = [
      $this->chargePayee($payee, 1),
      $this->chargePayer($payer, 1)
    ];
    // remove any entries with too small amounts or payer & payee identical.
    return array_filter(
      $additional_rows,
      function($e) {return ($e->payee <> $e->payer) or $e->quant < 1;}
    );
  }

  /**
   * Charge the payee
   *
   * @param string $payee
   * @param int $quant
   * @return \stdClass
   */
  protected function chargePayee(string $payee, int $quant) : \stdClass {
    return (object)[
      'payer' => $payee,
      'payee' => $this->feeAcc,
      'author' => $this->feeAcc,
      'quant' => $quant,
      'description' => "Payee fee of $quant to $this->nodeName/$this->feeAcc",
      'metadata' => new \stdClass
    ];
  }

  /**
   * Charge the payer.
   *
   * @param string $payer
   * @param int $quant
   * @return \stdClass
   */
  protected function chargePayer(string $payer, int $quant) : \stdClass {
    return (object)[
      'payer' => $payer,
      'payee' => $this->feeAcc,
      'author' => $this->feeAcc,
      'quant' => $quant,
      'description' => "Payer fee of $quant to $this->nodeName/$this->feeAcc",
      'metadata' => new \stdClass
    ];
  }

}
