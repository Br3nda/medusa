<?php
/**
 * @defgroup Uri_Parse Uri Parser
 * Parses requests to the RESTFUL api, and works out what code to run
 */

/**
 * @ingroup Uri_Parser 
 * Parses the URI, works out what methods to call.
 * 
 * example http request = http://api.wrms.com/wrms.request.getRequest.xml?request_id=123
 * where the method is  wrms.request.getRequest, the format is xml, and the params are request_id=>123
 */
class Uri_Parser {
  private $_uri_;

  /*
  * Takes a uri /wrms.request.getRequest.xml?a=1&b=2&userb=johnny|John Lewis&usera=sarahANDlewis and creates;
  * $this->method = wrms.request.getRequest
  * $this->format = xml
  * $this->params = a=>1, b=>2
  */
  function __construct($uri) {
    $this->_uri_ = $uri;

    $urihalves = split('\?',$uri,2); # Divide the uri into the class.format and parameters
    $methodstrings = split('\.',$urihalves[0]);
    $this->_format_ = array_pop($methodstrings); # drop off the last bit (the format), and...
    $this->_method_ = join('_',$methodstrings); # ... join them up with a different string
    $this->_method_ = str_replace(array('<', '>', '\\', '/',',','.'), "", $this->_method_);  # Clean up the method string

    foreach(split('&',  $urihalves[1]) as $variable) {
      $bits = split('=', $variable);
      $params[$bits[0]] = $bits[1];
    }
    $this->_params_ = $params;
  }
  
  public function get_method() {
    return $this->_method_;
  }
  public function get_params() {
    return $this->_params_;
  }
  public function get_format() {
    return $this->_format_;
  }
  
}

