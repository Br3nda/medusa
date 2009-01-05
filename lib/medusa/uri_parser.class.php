<?php
/**
 * @defgroup Requests
 */

/**
 * @ingroup Request
 * Parses the URI, works out what methods to call.
 */

class Uri_Parser {
  private $_uri_;
  function __construct($uri) {
    $this->_uri_ = $uri;
    $bits = split('/', $uri);
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

/** 
 * @ingroup Methods
 * 
 */
class wrms_request_getRequest {
	function run($params) {
		$request_id = $params['request_id'];
		$result = db_query('SELECT * FROM request WHERE request_id = %d', $request_id);
		$object = db_fetch_object($result);
		return $object; 
	} 
}