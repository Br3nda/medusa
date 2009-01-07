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
    function run ($params) {
        $return = array();
        return $return;
    }
}
