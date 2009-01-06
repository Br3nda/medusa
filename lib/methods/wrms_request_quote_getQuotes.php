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
    function run ($params) {
        $return = array();
        return $return;
    }
}
