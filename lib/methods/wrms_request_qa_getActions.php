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
    function run($params) {
        $request_id = $params['GET']['wr'];
        $access = access::getInstance();
        if ($access->canUserSeeRequest($request_id)) {
            $result = db_query('SELECT * FROM request_qa_action WHERE request_id = %d ORDER BY action_on', $request_id);
            $response = new response('Success');
            $actions = array();

            while ($row = db_fetch_object($result)) {
                $action = new WrmsQAAction();
                $action->populateNow($row);
                $actions[] = $action;
            }
    
            $response->set_data('actions', $actions);
            return $response;
        }
        else {
            return new error('Access denied', '403');
        }
    }
}
