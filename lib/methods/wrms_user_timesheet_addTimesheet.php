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

        $wr = $params['GET']['wr']; 
        $date = $params['GET']['date']; 
        $time = $params['GET']['time']; 
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


        // Validate that this is a real WR
        $result = db_query("SELECT request_id FROM request WHERE request_id = %d",$wr);
        if (db_fetch_object($result) == null) {
            return new error("You cannot put time against a non-existant work request", '405');
        }

        $user = currentuser::getInstance();

        

        // Make sure the date and time are valid - comvert to wrms-happy timestamp
        // Get the amount of time worked -- CAN'T BE NEGATIVE
        // Get the rate - how the heck will we know this? From the logged in user?
        // Description - encoded somehow?
        
        
        return new error('Not implemented');
    }
}
