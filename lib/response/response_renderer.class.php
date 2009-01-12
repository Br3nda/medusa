<?php
/**
* @file
* @ingroup Response
*/


class response_renderer {
  protected $reponse;
  
  /**
   * Give it a response object
   */
    function __construct($to_render) {
        $this->response = $to_render;
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
        header('Content-type: application/x-javascript');
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
            return json_encode($output);
        }
        elseif ($this->response instanceof WrmsBase) {
            return json_encode($this->response->getData());
        } 

        elseif ($this->response instanceof response) {
            // Our response object contains a code, message and a data array, which should have eberything it wants rendered as public
            return json_encode($this->response);
        }
        else {
            // We're stuffed
        }
    }

    private function __render_xml() {
        header('Content-type: application/xml');
    
        $output = $this->__recurse_xml($this->response);
        return $output;
    }
    private function __recurse_xml($input) {

        if (is_object($input)) {
            $data = $input->getData();
            $tag = get_class($input);
            $output = "<$tag>\n";

            foreach ($data as $key=>$value) {
                $child = htmlentities($key);
                if (is_object($value) || is_array($value)) {
                    $output .= "<$child>".$this->__recurse_xml($value)."</$child>\n";
                } else {
                    $output .= "<$child>".htmlentities($value)."</$child>\n";
                }
            }
            $output .= "</$tag>\n";
            return $output;
        } elseif (is_array($input)) {
            $output = '';
            foreach ($input as $key=>$value) {
                if (is_object($value)) {
                    $output .= $this->__recurse_xml($value);
                }
            }
            return $output;
        } else {
            return $input;
        }
    }

    private function __render_dump() {
        return '<br />Response:<br /><pre>'.print_r($this->response, true).'</pre>';
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
