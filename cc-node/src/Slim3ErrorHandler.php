<?php
namespace CCNode;

use CreditCommons\Exceptions\CCError;

/**
 * Convert all errors into an stdClass, which includes a field showing
 * which node caused the error
 */
class Slim3ErrorHandler {

  /**
   * Probably all errors and warnings should include an emergency SMS to admin.
   * This callback is also used by the ValidationMiddleware.
   * @note The task is made complicated because the $exception->message property is
   * protected and is lost during json_encode
   */
  public function __invoke($request, $response, $exception) {
    global $config;
    file_put_contents('last_exception.log', print_r($exception, 1)); //temp
    $exception_class = explode('\\', get_class($exception));
    $exception_class = array_pop($exception_class);
    if (!$exception instanceOf CCError) {
      $code = 500;
      $trace = $exception->getTrace(); //experimental;
      $exception_class = 'CCFailure';
      $output = (object)[
        'message' => $exception->getMessage()?:$exception_class
      ];
      // Just show the last error.
      while ($exception = $exception->getPrevious()) {
        $output = (object)[
          'message' => $exception->getMessage()?:$exception_class
        ];
        //if (get_class($exception) == 'League\OpenAPIValidation\PSR7\Exception\NoResponseCode') break;
      }
      $output->trace = $trace;
    }
    elseif (in_array($exception_class, ['CCViolation', 'CCFailure'])) {
      $output = (object)[
        'message' => $exception->getMessage(),
        'node' => ''
      ];
    }
    else {// All other CreditCommons error classes.
      $output = (object)($exception);
    }
    $output->node = $exception->node??$config['node_name'];
    $output->class = $exception_class;
    return json_response($response, $output, $code??$exception->getCode());
   }
}
