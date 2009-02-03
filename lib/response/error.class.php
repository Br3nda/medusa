<?php

class error extends response {
    
    function __construct($message = null, $code = 400) {
    	$this->status['message'] = $message;
        $this->status['code'] = $code;
    }

  /**
    * The return code for the response
    */
    function code($code) {
      assert(!is_null($code));
      $this->status['code'] = $code;
    }  

  /** 
    * Wrapper for code and message so we can do it on one line
    */
    function set_status($message, $code) {
      assert(!is_null($message));
      assert(!is_null($code));
      $this->message($message);
      $this->code($code);
    } 

    /*
     * Inherits all other functions from class response
     */

}

function errorHandler($errno, $errstr, $errfile, $errline){

    switch ($errno) {

        /*
         * If we hit an actual error
         */
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            error_logging('ERROR', $errstr.' in '.$errfile.' on line '.$errline);
            $response_renderer = response_renderer::getInstance();
            $error = new error($errstr);
            $error->set('error_value', $errno);
            $error->set('error_file', $errfile);
            $error->set('error_line', $errline);
            echo $response_renderer->render($error);
            exit;
        break;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            error_logging('WARNING', $errstr.' in '.$errfile.' on line '.$errline);
            $response_renderer = response_renderer::getInstance();
            $error = new error($errstr);
            $error->set('error_value', $errno);
            $error->set('error_file', $errfile);
            $error->set('error_line', $errline);
            echo $response_renderer->render($error);
            exit;
        break;
        default:
            return false;
        break;
    }   
    return true;


}
