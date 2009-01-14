<?php

class error extends response {
    
    function __construct($message = null, $code = 400) {
    	$this->status_message = $message;
        $this->status_code = $code;
    }

  /**
    * The return code for the response
    */
    function code($code) {
      assert(!is_null($code));
      $this->status_code = $code;
    }  

  /** 
    * Wrapper for code and message so we can do it on one line
    */
    function set($message, $code) {
      assert(!is_null($message));
      assert(!is_null($code));
      $this->message($message);
      $this->code($code);
    } 

    /*
     * Inherits all other functions from class response
     */

}
