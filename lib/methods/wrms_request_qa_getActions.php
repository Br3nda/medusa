<?php
/**
 * wrms.request.qa.getActions
 * Gets a list of the QA Actions added to the specified request.
 */
class wrms_request_qa_getActions {
    /**
     * Performs the fetch of the current QA Actions
     *
     * @param $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   - $params->user: User ID making the request
     * @return
     *   An array of actions on success
     *   An empty array on failure
     */
    function run ($params) {
        $return = array();
        return $return;
    }
}
