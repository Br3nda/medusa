<?php
/**
 * @file 
 * @ingroup Methods
 */

 /**
  * @ingroup Methods
  * Work Requests
  */
class wrms_request_getRequest {
	function run($params) {
		$request_id = $params['request_id'];
		$result = db_query('SELECT * FROM request WHERE request_id = %d', $request_id);
		$object = db_fetch_object($result);
		return $object; 
	} 
}
