<?php
/**
* @file
* @ingroup Response
*/


class response {
  protected $reponse;
  private $thing;
  
  /**
    * The return code for the response
    */
    function code($code) {
      assert(!is_null($code));
      $this->response['code'] = $code;
    }
  /**
    * The human readable message
    */
    function message($message) {
      assert(!is_null($message));
      $this->response['message'] = $message;
    }
  
  /**
    * Wrapper for code and message so we can do it on one line
    */
    function set($code, $message) {
      $this->code($code);
      $this->message($message);
    }
  
  /**
    * So we can append to our response
    */
    function message_append($message) {
      assert(!is_null($message));
      $this->response['message'] .= ' ' . $message;
    }
    
    function set_var($name, $value) {
        assert(!is_null($name));
        assert(!is_null($value));
        $this->response[$name] = $value;
    }
  
  /**
    * Render the response, in whichever format we want
    */
    function render($format = 'html') {
    	$method = '__render_' . $format;
    	if(is_callable($this->$method)) {
    		return $this->$method;
    	}
    	else return $this->__render_html();
    }
  /**
    * Private functions - we don't want others calling these directly
    * Yay for php5!
    */
    private function __render_html() {
        $html = "<br />Response:<br />";
        if (is_object($this->response)) {
	        foreach($this->response as $k => $v) {
	            $html .= htmlentities("'$k' : '$v'").'<br />';
	        }
        }
        else {
        	return '<p>No response</p>';
        }
        return $html;
    }
    private function __render_json() {
        return json_encode($this->response);
    }
    private function __render_xml() {
    	return 'hello';
    }
    
    function response($thing) {
    	$this->response = $thing;
    }
}

/**
 * @defgroup Response Medusa Response
 * encodes the result of the method call, into the requested format
 *  e.g.
 *  - html
 *  - json
 *  - csv
 *  - yaml
 *  - tetris
 *
 */
