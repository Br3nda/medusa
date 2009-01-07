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
    $section1 = '';
    $section2 = '';
    $raw_params = '';
    $in_params = false;
    for($i = 0; $i < strlen($uri); $i++) {
      if (($uri[$i] == '.') && !$in_params) {
        $section1 .= $section2;
        $section2 = '';
      }
      if (($uri[$i] == '?') && !$in_params) {
        $in_params = true;
        $this->_method_ = substr($section1, 1);
        $this->_format_ = substr($section2, 1);
      }
      if (!$in_params) {
        $section2 .= $uri[$i];
      }
      if ($in_params) {
        $raw_params .= $uri[$i];
      }
    }
    $raw_params = substr($raw_params, 1);
    foreach(split('&', $raw_params) as $variable) {
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

