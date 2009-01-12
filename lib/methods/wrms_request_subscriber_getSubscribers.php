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
    function run($params) {
        $request_id = $params['GET']['wr'];
        $access = access::getInstance();
        if ($access->canUserSeeRequest($request_id)) {

          $result = db_query('SELECT user_no FROM request_interested WHERE request_id = %d', $request_id);
            if (db_num_rows($result) >= 1) {
            $users = array();
            while ($row = db_fetch_object($result)) {
                $users[] = new user($row->user_no);
            }
                $response = new response('Success');
                $response->set_data('users', $users);
                return $response;
            }
            else {
                return false;
            }
  
        }
        else {
            return new error('Access denied', '403');
        }
        $return = array();
        return $return;
    }
}
