<?php
/**
 * @file 
 * @ingroup Methods
 * wrms.request.getRequest
 * Returns the specified request
 */

 /**
  * @ingroup Methods
  * Work Requests
  */
class wrms_request_getRequest {
    /**
     * Performs the fetch of the work request
     *
     * @param $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   - $params->user: User ID making the request
     *   @return
     *     The request object on success
     *     FALSE if permission is denied
     *     NULL if no work request
     */
	function run($params) {
        $request_id = $params['GET']['wr'];
        $access = access::getInstance();
        if ($access->canUserSeeRequest($request_id)) {
            $result = db_query('SELECT * FROM request WHERE request_id = %d', $request_id);
            if (db_num_rows($result) == 1) {
                $response = new response('Success');
                $object = new WrmsWorkRequest();
                $object->populate(db_fetch_object($result));
                $response->set('wr', $object);
                return $response;
            } 
            else {
                return false;
            }
        } 
        else {
            return false;
        }
	} 
}
