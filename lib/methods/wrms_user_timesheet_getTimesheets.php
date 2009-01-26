<?php
/**
 * wrms.user.timesheet.getTimesheets
 * Get the timesheets of a specified user
 */
class wrms_user_timesheet_getTimesheets extends wrms_base_method {
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
        $request_id = $params['GET']['wr'];
        $access = access::getInstance();
        if ($access->canUserSeeRequest($request_id)) {

            $sql = 'SELECT * FROM request_timesheet WHERE work_by_id = %d ';
            
            /*
             * There may be a better way to do this, but it seems like a sensible validation and or injection stopper - any invalid date will be 1970-01-01
             */
            if ($from) {
                $from = date('Y-m-d', strtotime($from));
                if ($from == "1970-01-01") {
                    return new error('Invalid date format in start date. Required format: yyyy-mm-dd');
                }
                else {
                    $sql .= "AND work_on >= '$from' ";
                }            
            }           
            if ($to) {
                $to = date('Y-m-d', strtotime($to));
                if ($to == "1970-01-01") {
                    return new error('Invalid date format in end date. Required format: yyyy-mm-dd');
                }
                else {
                    $sql .= "AND work_on <= '$to' ";
                }
            }

            $sql .= 'ORDER BY work_on ASC';

            $result = db_query($sql, $user_id);
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
