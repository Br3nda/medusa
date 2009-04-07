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
        $from = $params['GET']['start_date'];
        $to = $params['GET']['end_date'];
        $request_id = $params['GET']['wr'];
        if ($access->permitted('wr/timesheets/view', $request_id)) {
            $sql = 'SELECT * FROM request_timesheet WHERE request_id = %d ';

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

            $sql .= 'ORDER BY timesheet_id DESC';
            $result = db_query($sql, $request_id);
            $response = new response('Success');
            $return = array();
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
