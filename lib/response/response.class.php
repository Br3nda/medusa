<?php
/**
* @file
* @ingroup Response
*/


class response {
  protected $code = 200;
  protected $message;
  

  function __construct($message = null) {
      $this->message($message);
  }

  /**
    * The human readable message
    */
    function message($message) {
      assert(!is_null($message));
      $this->message = $message;
    }
  
  /**
    * So we can append to our response
    */
    function message_append($message) {
      assert(!is_null($message));
      $this->message .= ' ' . $message;
    }
    
    /*
     * Allows us to set whatever datas we like
     */
    function set_data($name, $value) {
        assert(!is_null($name));
        assert(!is_null($value));
        $this->response[$name] = $value;
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
