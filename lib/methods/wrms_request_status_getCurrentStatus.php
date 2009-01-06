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
        $return = null;
        return $return;
    }
}
