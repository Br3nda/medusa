<?php
/**
 * wrms.request.subscriber.getSubscribers
 * Gets a list of the current subscribed users
 */
class wrms_request_subscriber_getSubscribers {
    /**
     * Performs the fetch of the subscribed users
     *
     * @param $params
     *   Associative array of parameters
     *    - $params->wr: Work Request ID
     *    - $params->user: User ID making the request
     *  @return
     *    An array of users on success
     *    Empty array of failure
     */
    function run ($params) {
        $return = array();
        return $return;
    }
}
