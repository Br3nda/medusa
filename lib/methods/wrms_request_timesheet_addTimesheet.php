<?php
/**
 * wrms.request.timesheet.addTimesheet
 * Adds a timesheet to the specified WR
 */
class wrms_request_timesheet_addTimesheet {
    /**
     * Performs the insert of the timesheet
     *
     * @params $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   - $params->user: User ID making the request
     *   - $params->date: The date to record the timesheet for
     *   - $params->time: The time to start the record from
     *   - $params->quantity: The quantity of units to add
     *   - $params->units: The units to use (default: hours)
     *   - $params->rate: The rate at which to charge
     *   - $params->description: A description about the time
     * @return
     *   TRUE on success, FALSE on failure to add
     */
    function run($params) {
        $return = false;
        return $return;
    }
}
