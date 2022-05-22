<?php
namespace CCNode;
use CreditCommons\Exceptions\CCError;
use CreditCommons\Exceptions\CCFailure;

/**
 * Convert all errors into an stdClass, which includes a field showing
 * which node caused the error
 */
class Slim3ErrorHandler {

  /**
   * Probably all errors and warnings should include an emergency SMS to admin.
   * This callback is also used by the ValidationMiddleware.
   *
   * @note The task is made complicated because the $exception->message property is
   * protected and is lost during json_encode
   */
  public function __invoke($request, $response, $exception) {
    global $user;
    if (getConfig('dev_mode')) {
      file_put_contents('last_exception.log', print_r($exception, 1)); //temp
    }
    $exception_class = explode('\\', get_class($exception));
    $exception_class = array_pop($exception_class);
    if (!$exception instanceOf CCError) {
      $code = 500;
      $exception_class = 'CCFailure';
      $output = new CCFailure($exception->getMessage()?:$exception_class);
      // Just show the last error.
//      while ($exception = $exception->getPrevious()) {
//        $output = (object)[
//          'message' => $exception->getMessage()?:$exception_class
//        ];
//        //if (get_class($exception) == 'League\OpenAPIValidation\PSR7\Exception\NoResponseCode') break;
//      }
    }
    else {// All CreditCommons error classes.
      $output = (object)($exception);
    }
    $output->node = $exception->node??getConfig('node_name');
    $output->method = $request->getMethod();
    $output->path = $request->geturi()->getPath();
    $output->class = $exception_class;
    $output->user = $user ? $user->id : '-anon-';
    if ($q = $request->geturi()->getQuery()){
      $output->path .= '?'.$q;
    }
    $output->break = $exception->getFile() .': '.$exception->getLine();
    $output->trace = $exception->getTraceAsString(); //experimental;;

    return json_response($response, $output, $code??$exception->getCode());
   }

}
