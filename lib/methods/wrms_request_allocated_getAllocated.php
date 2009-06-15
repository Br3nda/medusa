<?php
/**
 * wrms.request.allocated.getAllocated
 * Returns the current allocated users
 * @ingroup Methods
 */
class wrms_request_allocated_getAllocated extends wrms_base_method {
    /**
     * Performs the fetch of allocated users
     *
     * @param $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   @return
     *     An array of users on success
     *     An error reponses
     */
    function run($params) {
        if ($params['GET']['wr'] == null) {
          error_logging('WARNING', "No work request number (wr) provided.");
          return new error('No work request number (wr) provided.');
        }
        $request_id = $params['GET']['wr'];
        $access = access::getInstance();
        if ($access->permitted('wr/view', $request_id)) {
            $result = db_query('SELECT allocated_to_id FROM request_allocated WHERE request_id = %d', $request_id);
            $users = array();
            $response = new response('Success');

            while ($row = db_fetch_object($result)) {
                $users[] = new user($row->allocated_to_id);
            }
            
            $response->set('allocated', $users);
            return $response;
        }
        else {
            return new error('Access denied', '403');
        }
    }
}
