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
    function run ($params) {
        $return = array();
        return $return;
    }
}
