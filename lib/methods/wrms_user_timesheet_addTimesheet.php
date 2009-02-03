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
     *   - $params->description: A description about the time
     * @return
     *   TRUE on success, FALSE on failure to add
     */
    function run($params) {

        $wr = $params['GET']['wr']; 
        $datetime = $params['GET']['datetime'];
        $quantity = $params['GET']['quantity']; 
        $units = $params['GET']['units']; 
        $rate = $params['GET']['rate']; 
        $description = $params['GET']['description']; 

        // Okay, so we want to add time to a WR
        
/*medusa=> \d request_timesheet
                                             Table "public.request_timesheet"
      Column      |            Type             |                                Modifiers                                 
------------------+-----------------------------+--------------------------------------------------------------------------
 timesheet_id     | integer                     | not null default nextval('request_timesheet_timesheet_id_seq'::regclass)
 request_id       | integer                     | 
 work_on          | timestamp without time zone | 
 ok_to_charge     | boolean                     | default false
 work_quantity    | double precision            | 
 work_duration    | interval                    | 
 work_by_id       | integer                     | 
 work_by          | text                        | 
 work_description | text                        | 
 work_rate        | double precision            | 
 work_charged     | timestamp without time zone | 
 charged_amount   | double precision            | 
 charged_by_id    | integer                     | 
 work_units       | text                        | 
 charged_details  | text                        | 
 entry_details    | text                        | 
 dav_etag         | text                        | 
 review_needed    | boolean                     | default false
Indexes:
    "request_timesheet_pkey" PRIMARY KEY, btree (timesheet_id)
    "request_timesheet_dupe_catcher_index" UNIQUE, btree (request_id, work_quantity, work_on, work_description, work_by_id)
    "request_timesheet_etag_skey" UNIQUE, btree (work_by_id, dav_etag)
    "request_timesheet_req" btree (request_id, timesheet_id) CLUSTER
    "request_timesheet_skey1" btree (work_on, work_by_id, request_id)
    "request_timesheet_skey2" btree (ok_to_charge, request_id)
Foreign-key constraints:
    "request_timesheet_charged_by_id_fkey" FOREIGN KEY (charged_by_id) REFERENCES usr(user_no) ON UPDATE CASCADE ON DELETE RESTRICT
    "request_timesheet_request_id_fkey" FOREIGN KEY (request_id) REFERENCES request(request_id) ON UPDATE CASCADE ON DELETE RESTRICT
    "request_timesheet_work_by_id_fkey" FOREIGN KEY (work_by_id) REFERENCES usr(user_no) ON UPDATE CASCADE ON DELETE RESTRICT
*/

        $user = currentuser::getInstance();
        $access = access::getInstance();
        if ($access->canUserAddTimesheets()) {
 
            // Validate that this is a real WR
            $result = db_query("SELECT request_id FROM request WHERE request_id = %d", $wr);
            if (db_fetch_object($result) == null) {
                return new error("You cannot put time against a non-existant work request", '405');
            }

            // Make sure the date and time are valid - convert to wrms-happy timestamp
            $timestamp = date('Y-m-d H:i:s', strtotime($datetime));

            // Get the amount of time worked -- CAN'T BE NEGATIVE
            if ($quantity <= 0)  {
                return new error("You can't work 0 hours or less on a WR", '405');
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
                    // TODO
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

            // Get the rate - how the heck will we know this? From the logged in user?
            $rate = $user->base_rate; 

            $id = $user->getUserID();
            if ($id == null) {
                return new error('You must be logged in to add a timesheet', '403');
            }
            // Description - TODO: encoded somehow?
            // I know "$quantity $units" looks bad, postgres puts this into an 'interval' database field, so _it_ figures out how to make it nice, not us
            $result = db_query("INSERT INTO request_timesheet (request_id, work_on, work_quantity, work_duration, work_by_id, work_description, work_rate, work_units) 
                                VALUES (%d, '%s', %d, '%s', %d, '%s', %d, '%s')", $wr, $timestamp, $quantity, "$quantity $units", $id, $description, $rate, $units);

            if ($result == false) {
                return new error('Database query failed', '500');
            }
            else {
                return new response('Success');
            }

                //return new error('Not implemented');
            }
            else {
                return new error('You are not authorised to add timesheets', 403);    
            }

       
        
    }
}
