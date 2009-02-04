<?php
/**
 * wrms.user.timesheet.addTimesheet
 * Adds a timesheet for the specified user ID
 */
class wrms_user_timesheet_addTimesheet extends wrms_base_method {
    /**
     * Performs the insert of the timesheet
     *
     * @params $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID
     *   - $params->datetime: The date and time to record the timesheet for in ISO format
     *   - $params->quantity: The quantity of units to add
     *   - $params->units: The units to use (default: hours)
     *   - $params->rate: Optional rate of charge - if not supplied we apply the WRMS logic. - Note: If units is 'amount' then rate is required
     *   - $params->description: A description about what the time was spent on
     * @return
     *   TRUE on success, FALSE on failure to add
     */
    function run($params) {

        // All the things we might need to enter a WR
        $wr = $params['GET']['wr']; 
        $datetime = $params['GET']['datetime'];
        $quantity = $params['GET']['quantity']; 
        $units = $params['GET']['units']; 
        $rate = $params['GET']['rate']; 
        $description = $params['GET']['description']; 

        // Who are we logged in as, and can we actually add timesheets?
        $user = currentuser::getInstance();
        $access = access::getInstance();

        if ($access->canUserAddTimesheets()) {

            // Get the ID of the user
            $id = $user->getUserID();
            if ($id == null) {
                return new error('You must be logged in to add a timesheet', '403');
            }
 
            // Is this a real WR?
            $result = db_query("SELECT request_id FROM request WHERE request_id = %d", $wr);
            if (db_fetch_object($result) == null) {
                return new error("You cannot put time against a non-existant work request", '405');
            }

            // Make sure the date and time are valid - convert to wrms-happy timestamp
            $timestamp = date('Y-m-d H:i:s', strtotime($datetime));

            if ($timestamp == '1970-01-01 12:00:00') {
                return new error('Unable to add timesheet: Invalid date', 400);
            }
            
            // Get the amount of time worked -- Can't be negative or zero
            if ($quantity <= 0)  {
                return new error("Unable to add timesheet: You can't work 0 hours or less on a WR", '405');
            }
       
            /*
             * So there's more than one way to log 'time' on a WR
             * The standard is hours, so this will be the default if you don't specify
             * Days is only here because of the few that use it - I personally would love to see it gone forever
             * Amount is for when you buy hardware or a fixed cost item as part of a WR - new server - travel expenses, etc.
             * All the others I've yet to find a reason to implement
             */ 
            switch ($units) {
                case 'hours':
                    // If we are in hours, then our job is very simple - we do nothing and the SQL figures itself out        
                break;
                case 'days':
                    // If we are in days, then our job is very simple - we do nothing and the SQL figures itself out        
                break;
                case 'amount':
                    if (empty($rate)) {
                        return new error('Unable to add timesheet: you must specify a rate when adding an amount to a WR', '400');
                    }
                    else if (!is_numeric($rate)) {
                        return new error('Unable to add timesheet: rate must be a numeric value', '400');
                    }
                    // So long as we've got this far the below rate calculation logic won't be applied
                break;
                case 'dollars':
                    return new error('dollars not implemented for this method - please use hours, days or amount', 406);
                break;
                case 'pounds':
                    return new error('pounds not implemented for this method - please use hours, days or amount', 406);
                break;
                case 'euros':
                    return new error('euros not implemented for this method - please use hours, days or amount', 406);
                break;
                case 'usd':
                    return new error('usd not implemented for this method - please use hours, days or amount', 406);
                break;
                case 'aud':
                    return new error('aud not implemented for this method - please use hours, days or amount', 406);
                break;
                default:
                    $units = 'hours';
                break;
                
            }

            /*
             * Okay, I'm not saying this logic is any good, but it's what WRMS 2.0 does (pick the first that applies)
             * 1. If the user has specified a rate in the call to this method, use that
             * 2. If the client has a rate set, use that rate
             * 3. If the user has a rate set, use that rate
             * 4. If the supplier has a rate set, use that rate
             * 5. Default to the config value (120 at time of coding, but configurable from lib/medusa/config/config.php)
             */

            // If we haven't got a rate, set $rate to null so the default rate logic will kick in
            if (empty($rate)) {
                $rate = null;
            }
            // If we have been given a stupid value for rate
            else if (!is_numeric($rate)) {
                return new error('Unable to add timesheet: rate must be numeric', '400');
            }

            // Check the rate for the client (requestor)
            if ($rate == null) {
                $result = db_query("SELECT work_rate FROM request 
                                        INNER JOIN usr ON (request.requester_id = usr.user_no) 
                                        INNER JOIN organisation_plus ON (usr.org_code = organisation_plus.org_code) 
                                        WHERE request.request_id=%d LIMIT 1", $wr);

                while ($row = db_fetch_object($result)) {
                    $rate = $row->work_rate;
                }
            }
           
            // If we didn't have any luck there, check the rate for the user 
            if ($rate == null) {
                $result = db_query("SELECT base_rate FROM usr WHERE user_no=%d LIMIT 1", $id);
                while ($row = db_fetch_object($result)) {
                   $rate = $row->base_rate;
                }
            }
            
            // Still no luck? Check the supplier rate
            if ($rate == null) {
                $result = db_query("SELECT work_rate FROM usr INNER JOIN organisation_plus ON (usr.org_code = organisation_plus.org_code) 
                                        WHERE usr.user_no=%d LIMIT 1", $id);

                while ($row = db_fetch_object($result)) {
                    $rate = $row->work_rate;
                }
            }

            // If all our options have failed us, set a default rate from config
            if ($rate == null) {
               $rate = DEFAULT_CHARGE_RATE; 
            }

            // Description - URL Encoded
            $description = urldecode($description);
            
            // I know "$quantity $units" looks bad, postgres puts this into an 'interval' database field, so _it_ figures out how to make it nice, not us
            if ($units != 'amount') {
                $duration = "'$quantity $units'";
            }
            else {
                $duration = "null";
            }
            $result = db_query("INSERT INTO request_timesheet (request_id, work_on, work_quantity, work_duration, work_by_id, work_description, work_rate, work_units) 
                                VALUES (%d, '%s', %d, %s, %d, '%s', %d, '%s')", $wr, $timestamp, $quantity, $duration, $id, $description, $rate, $units);

            if ($result == false) {
                return new error('Database query failed', '500');
            }
            else {
                return new response('Success');
            }
        }
        else {
            return new error('You are not authorised to add timesheets', 403);    
        }
    }
}
