<?php
/**
 * @defgroup Requests
 */

/**
 * @ingroup Request
 * Parses the URI, works out what methods to call.
 */

class Request {
  private $_uri_;
  function __construct($uri) {
    $this->_uri_ = $uri;
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
