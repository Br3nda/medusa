<?php
/**
 * wrms.request.timesheet.getTimesheets
 * Returns timesheets associated with the specified WR
 */
class wrms_request_timesheet_getTimesheets extends wrms_base_method {
    /**
     * Performs the fetch of the timesheets by work request
     *
     * @params $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   - $params->user: User ID making the request
     *   - $params->start_date: Start date to search by
     *   - $params->end_date: End date to search by
     *   Start_date and End_date are inclusive, results will be returned for those days as well.
     *   If one date is ommited a result set its returned for the one day specified by the other date
     *   @return
     *     An array of timesheets or an empty array if no results
     */
    function run($params) {
        $access = access::getInstance();
        $request_id = $params['GET']['wr'];
        if ($access->canUserSeeStatus($request_id)) {
            $result = db_query('SELECT * FROM request_timesheet WHERE request_id = %d ORDER BY timesheet_id DESC', $request_id);
            $response = new response('Success');
            if (db_num_rows($result) > 0) {
                while ($row = db_fetch_object($result)) {
                    $obj = new WrmsTimeSheet();
                    $obj->populate($row);
                    $return[] = $obj;
                }   
            }   
            $response->set('timesheetentries', $return);
            return $response;
        }   
        else {
            return new error('Access denied', 403);
        }   
    }
}
