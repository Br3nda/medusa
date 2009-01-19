<?php
/**
 * wrms.request.status.getStatusHistory
 * Fetches a list of all status changes made to the specified request
 */
class wrms_request_status_getStatusHistory {
    /**
     * Performs the fetch list action
     *
     * @param $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   - $params->user: User ID making the request
     * @return 
     *   An array of status changes ordered from most recent to oldest
     *   An empty array if permission is denied
     */
    function run($params) {
        $return = array();
        $access = access::getInstance();
        $request_id = $params['GET']['wr'];
        if ($access->canUserSeeStatus($request_id)) {
            $result = db_query('SELECT * FROM request_status WHERE request_id = %d ORDER BY status_on DESC', $request_id);
            $response = new response('Success');
            if (db_num_rows($result) > 0) {
                while ($row = db_fetch_object($result)) {
                    $obj = new WrmsStatus();
                    $obj->populate($row);
                    $return[] = $obj;
                }
            }
            $response->set('history', $return);
            return $response;
        }
        else {
            return new error('Access denied', 403);
        }
    }
}
