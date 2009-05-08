<?php
/**
* @file
* @ingroup Response
*/


class response {
  public $status;
  public $data;
  

  function __construct($message = 'None') {
      $status = array();
      $data = array();
      $this->status['code'] = 200;
      $this->status['message'] = $message;
  }

  /**
    * The human readable message
    */
    function message($message) {
      assert(!is_null($message));
      $this->status['message'] = $message;
    }
  
  /**
    * So we can append to our response
    */
    function message_append($message) {
      assert(!is_null($message));
      $this->status['message'] .= ' ' . $message;
    }
    
    /*
     * Allows us to set whatever data we like, but we can't touch the status through this
     */
    function set($name, $value) {
        assert(!is_null($name));
        assert(!is_null($value));
        $this->data[$name] = $value;
    }

    /*
     * Allows us to get the status object
     */
    function getStatus() {
    	//TODO where does status come from?
       return $status; 
    }

    /*
     * Allows us to get the data object
     */
    function getData() {
        return $this->data;
    }
}
/**
 * @defgroup Response Medusa Response
 * encodes the result of the method call, into the requested format
 *  e.g.
 *  - html
 *  - json
 *  - yaml
 *  - tetris
 *
 */
