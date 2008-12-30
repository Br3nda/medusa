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
  }
  public function get_params() {
  }
  public function get_format() {
  }
  
}
