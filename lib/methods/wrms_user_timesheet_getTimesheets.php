<?php
/**
 * wrms.user.timesheet.getTimesheets
 * Get the timesheets of a specified user
 */
class wrms_user_timesheet_getTimesheets {
    /**
     * Performs the fetch of the timesheets by user
     * 
     * @params $params
     *   Associative array of parameters
     *   - $params->person: User ID to get timesheets from
     *   - $params->user: User ID making the request
     *   - $params->start_date: Start date to search by
     *   - $params->end_date: End date to search by
     *   Start_date and End_date are inclusive, results will be returned for those days as well.
     *   If one date is ommited a result set its returned for the one day specified by the other date
     *   @return
     *     An array of timesheets or an empty array if no results
     */
    function run($params) {
        $user_id = $params['GET']['person'];
        $from = $params['GET']['start_date'];
        $to = $params['GET']['end_date'];
        $access = access::getInstance();
        if ($access->canUserSeeRequest($request_id)) {

          $result = db_query('SELECT * FROM request_timesheet WHERE work_by_id = %d ORDER BY work_on ASC', $user_id);
                $timesheets = array();
                while ($row = db_fetch_object($result)) {
                    $timesheet = new WrmsTimeSheet();
                    $timesheet->populate($row);
                    $timesheets[] = $timesheet;
                }
                $response = new response('Success');
                $response->set('timesheets', $timesheets);
            return $response;
        }
        else {
            return new error('Access denied', 403);
        }
    }
}
