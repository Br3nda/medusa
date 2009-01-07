<?php
/**
 * wrms.request.status.getCurrentStatus
 * Returns the current status of the WR
 */
class wrms_request_status_getCurrentStatus {
    /**
     * Performs the fetch of the current status
     *
     * @param $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   - $params->user: User ID making the request
     * @return
     *   A character corresponding to the current status on success
     *   FALSE is permission denied
     *   NULL if no work request
     */
    function run ($params) {
        $request_id = $params['wr'];
        $access = access::getInstance();
        if ($access->canUserSeeStatus($request_id)) {
            $result = db_query('SELECT last_status AS current_status FROM request WHERE request_id = %d', $request_id);
            if (db_num_rows($result) > 0) {
                $info = db_fetch_object($result);
                return $info;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
