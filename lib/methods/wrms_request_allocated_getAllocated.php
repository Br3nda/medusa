<?php
/**
 * wrms.request.allocated.getAllocated
 * Returns the current allocated users
 * wrms.request.allocated.getAllocated 
 * @ingroup Methods
 */
class wrms_request_allocated_getAllocated {
    /**
     * Performs the fetch of allocated users
     *
     * @param $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   - $params->user: User ID making the request
     *   @return
     *     An array of users on success
     *     An empty array on failure
     */
    function run($params) {

        $request_id = $params['GET']['wr'];
        $access = access::getInstance();
        if ($access->canUserSeeRequest($request_id)) {
            $result = db_query('SELECT allocated_to_id FROM request_allocated WHERE request_id = %d', $request_id);
            $users = array();
            $response = new response('Success');

            while ($row = db_fetch_object($result)) {
                $users[] = new user($row->allocated_to_id);
            }
            
            $response->set_data('allocated', $users);
            return $response;
        }
        else {
            return new error('Access denied', '403');
        }
    }
}
