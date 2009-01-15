<?php
/**
 * wrms.request.note.getNote
 * Gets a list of all notes attached to this work request.
 */
class wrms_request_note_getNotes {
    /**
     * Performs the fetch of attached notes
     *
     * @param $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   - $params->user: User ID making the request
     * @return
     *   An array of notes on success
     *   An empty array on failure
     */
    function run($params) {

        $request_id = $params['GET']['wr'];
        $access = access::getInstance();
        if ($access->canUserSeeRequest($request_id)) {
            $result = db_query('SELECT * FROM request_note WHERE request_id = %d', $request_id);
            $response = new response('Success');
            $notes = array();

            while ($row = db_fetch_assoc($result)) {
                $notes[] = $row;
            }
    
            $response->set_data('notes', $notes);
            return $response;
        }
        else {
            return new error('Access denied', '403');
        }


        /*
        $return = array();
        return $return;*/
    }
}
