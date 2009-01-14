<?php
/**
* @file
* @ingroup Response
*/


class response {
  public $code = 200;
  public $message;
  public $data;
  

  function __construct($message = null) {
      $this->message($message);
      $data = array();
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
        $this->data[$name] = $value;
    }

    /*
     * Allows us to get the data object
     */
    function getData() {
        $this->data['code'] = $this->code;
        $this->data['message'] = $this->message;
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
