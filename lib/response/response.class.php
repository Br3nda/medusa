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
        error_logging('DEBUG', 'Render method: '.$method);
        if(is_callable(array($this, $method))) {
    		return $this->$method();
    	}
        else {
            error_logging('DEBUG', 'Going to default rendering');
            return $this->__render_html();
        }
    }
  /**
    * Private functions - we don't want others calling these directly
    * Yay for php5!
    */
    private function __render_html() {
        error_logging('DEBUG', 'Rendering with __render_html');
        $html = "<br />Response:<br />";
        if (is_object($this->response) || is_array($this->response)) {
            $html = $this->__recurse_html($this->response); 
        } elseif (!empty($this->response)) {
            $html = $this->response;
        }
        else {
        	return '<p>No response</p>';
        }
        return $html;
    }
    private function __render_json() {
        //Check if the response is an array
        if (is_array($this->response)) {
            $output = array();
            foreach ($this->response as $key=>$value) {
                if (is_object($value) && ($value instanceof WrmsBase)) {
                    //Can use this method as WrmsBase defines it
                    $output[] = $value->getData();
                } else {
                    //Shove anything else into array
                    $output[] = $value;
                }
            }
            return json_encode($this->response);
        } elseif ($this->response instanceof WrmsBase) {
            return json_encode($this->response->getData());
        } else {
            return json_encode($this->response);
        }
    }
    private function __render_xml() {
    	return $this->__recurse_xml($this->response);
    }
    private function __render_dump() {
        return '<br />Response:<br /><pre>'.print_r($this->response, true).'</pre>';
    }
    
    function __construct($response = null) {
    	$this->response = $response;
    }

    private function __recurse_html($input) {
        if ($input instanceof WrmsBase) {
            return $this->__recurse_html($input->getData());
        } elseif (is_array($input) || is_object($input)) {
            $output = '';
            foreach ($input as $key=>$value) {
                if (is_array($value) || is_object($value)) {
                    $output .= $this->__recurse_html($value);
                } else {
                    $output .= htmlentities($key) . ': ' . htmlentities($value) . "<br />\n";
                }
            }
            return $output;
        } else {
            return $input;
        }
    }

    private function __recurse_xml($input, $depth = 0, $parent = 'request') {
        if ($input instanceof WrmsBase) {
            //Data is not publicly available so call again with the data
            return $this->__recurse_xml($input->getData(), $depth, get_class($input));
        } elseif (is_object($input) || is_array($input)) { 
            $output = '';
            $next_depth = $depth + 1;
            foreach ($input as $key=>$value) {
                $tag = htmlentities($key);
                if (is_numeric($tag)) {
                    $tag = $parent;
                }
                $tabs = '';
                for ($i = 0; $i < $depth; $i++) {
                   //There has to be a more elegant way to do this
                   $tabs .= chr(9);
                }
                if (is_array($value) || is_object($value)) {
                    $output .= "$tabs<$tag>\n".$this->__recurse_xml($value, $next_depth, $tag)."</$tag>\n";
                } else {
                    $output .= "$tabs<$tag>".htmlentities($value)."</$tag>\n";
                }
            }
            return $output;
        } else {
            return null;
        }
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
