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
    $num = preg_match ('/^\/(\w)\?(.+)$/',$uri,$results);
    if ($num == 1) {
        $this->_uri_=$results[0];
    }
    $this->_params_ = $_GET; # Frankly, that's all we're really doing
    $this->_format_ = $_GET['format'];
    if (! $this->_format_)
        $this->_format_ = 'xml';
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
