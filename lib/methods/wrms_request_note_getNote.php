<?php
/**
 * wrms.request.note.getNote
 * Gets a list of all notes attached to this work request.
 */
class wrms_request_note_getNote {
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
    function run ($params) {
        $return = array();
        return $return;
    }
}
