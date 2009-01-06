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
  function __construct($uri) {
    $this->_uri_ = $uri;
    $bits = split('\.|/|\?', $uri);
    $this->_method_ = $bits[1];
    $this->_format_ = $bits[2];
    
    $raw_params = $bits[3];
    foreach(split(';', $raw_params) as $variable) {
    	echo $variable;
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

