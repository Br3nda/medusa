<?php
/**
 * wrms.request.quote.getQuotes
 * Returns all quotes attached to the specified work request
 */
class wrms_request_quote_getQuotes {
    /**
     * Performs the fetch of the quotes
     *
     * @param $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   - $params->user: User ID making the request
     *   @return
     *     An array of quotes or an empty array
     */
    function run($params) {
        $request_id = $params['GET']['wr'];
        $access = access::getInstance();
        if ($access->canUserSeeRequest($request_id)) {
            $result = db_query('SELECT * FROM request_quote WHERE request_id = %d ORDER BY quoted_on', $request_id);
            $response = new response('Success');
            $actions = array();

            while ($row = db_fetch_object($result)) {
                $action = new WrmsQuote();
                $action->populateNow($row);
                $actions[] = $action;
            }
    
            $response->set('actions', $actions);
            return $response;
        }
        else {
            return new error('Access denied', '403');
        }
    }
}
