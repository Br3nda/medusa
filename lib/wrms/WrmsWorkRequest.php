<?php

/**
 * WorkRequest Object
 */
class WrmsWorkRequest extends WrmsBase {

  public function __construct($id = null) {
    $this->timesheets = array();
    $this->notes = array();    
    $this->id = $id;
  }

  public function populateNow($id = null) {
    if ($id == null) {
      $id = $this->id;
    }
    if ($id == null) {
        return new error("Unable to populate a null work request", 500);
    }
    $result = db_query("SELECT * FROM request WHERE request_id='%d'", $id);
    if (count($result) == 1) {
      $this->populate(db_fetch_object($result));
    }
    $this->populateChildren();
  }

  public function populateChildren() {
      $result = db_query("SELECT * FROM request_timesheet WHERE request_id='%d'", $this->id);
      while ($row = db_fetch_assoc($result)) {
        $newsheet = new WrmsTimeSheet();
        $newsheet->populate($row);
        $this->timesheets[] = $newsheet;
      }

      # This possibly isn't the smallest implementation, but it will do for the moment.
      $result = db_query("SELECT * FROM request_note WHERE request_id='%d'", $this->id);
      while ($row = db_fetch_assoc($result)) {
        $newnote = new WrmsRequestNote();
        $newnote->populate($row);
        $this->notes[] = $newnote;
      }
  }

  public function create($urgency, $importance, $type, $person, $brief, $details, $sys) {
                // We are creating a WR

            // request_id is automatic
            // request_on is automatic
            // active is automatic - do we ever want it to be false initially?
            // last_status I don't want to do right now
            //wap_status - is it ever used?
            //sla_response_hours - don't care yet
    
            if (is_numeric($urgency)) {
                $this->urgency = intval($urgency);
            }
            else {
                return new error("Urgency must be numeric");
            }
            $this->importance = intval($importance);
            $this->request_type = intval($type);
            $this->requester_id = intval($person);
            $this->brief = $brief; //TODO secure this
            $this->detailed = $details; //TODO secure this

            $user = currentuser::getInstance();
            $entered_by = $user->getUserId();
            $this->entered_by = $entered_by;

            if (is_numeric($sys)) {
                $this->system_id = intval($sys);
            }
            else {
                return new error("System ID must be numeric");
            }
            // Make sure the person exists
                
            //eta -- old, can't see a use for it

            //last_activity -- auto updates

            //sla_response_time and sla_response_type -- don't care yet

            //requested_by_date -- TODO

            //agreed_due_date -- TODO

            //request_by -- Can be null?


            //parent_request -- TODO don't care yet

            //invoice_to -- TODO
            return $this->writeToDatabase();

  }


  /*
   * This is the function that makes sure this object is in a sane state before writing to the database
   */
    private function validate() {

        // Our config for WRMS presets
        include('config/config.wr.php');
        /*
         * How urgent is this WR?
         */
        if (is_numeric($this->urgency)) {
            /*
             * Make sure it's a valid WRMS preset
             */
            $urgency_match = false;
            foreach ($WRMS_WR_URGENCY_CODES as $k => $v) {
                if ($this->urgency == $v) {
                    $urgency_match = true;
                }   
            }   
            if (!$urgency_match) {
                return new error('Urgency must be a valid WRMS value', 400);
            }  
        }   
        else {
            return new error('Urgency must be a numeric value', 400);
        }   

        // Importance
        if (is_numeric($this->importance)) {
            $importance_match = false;
            foreach ($WRMS_WR_IMPORTANCE_CODES as $k => $v) {
                if ($importance == $v) {
                    $importance_match = true;
                }   
            }
            if (!$importance_match) {
                return new error('Importance must be a valid WRMS value', 400);
            }
        }
        else {
            return new error('Importance must be a numeric value', 400);
        }

        //severity_code -- redundant, hasn't been used for 30,000 WRs and was only used on 110 of the ones before that

        // Request Type
        if (is_numeric($this->request_type)) {
            $type_match = false;
            foreach ($WRMS_WR_TYPE_CODES as $k => $v) {
                if ($type == $v) {
                    $type_match = true;
                }
            }
            if (!$type_match) {
                return new error('Type must be a valid WRMS value', 400);
            }
        }
        else {
            return new error('Type must be a numeric value', 400);
        }

        // requester_id -- This is the "Request For" field
        // I know we aren't doing any real validation on this - the access class will make sure this person exists and can be viewed by the WR adder
        if (!is_numeric($this->requester_id)) {
            return new error('Person must be a numeric value', 400);
        }

        // Brief
        if (empty($this->brief)) {
            return new error("You must supply a brief for the Work Request", 400);
        }
        
        // Detailed
        if (empty($this->detailed)) {
            return new error("You must supply the details for the Work Request", 400);
        }
        
        // Entered_By -- The ID of the person who created the WR
        // Get ID from session
        if (!is_numeric($this->entered_by)) {
           return new error("You must be logged in to add a WR", 403);
        }

        // System id
        if (!is_numeric($this->system_id)) {
            return new error("System must be a numeric value", 400);
        }
    }

  public function writeToDatabase() {
   
    $valid = $this->validate();

    if (!$valid instanceof error) {

    var_dump($this);
    exit();
    $this->id = 58327;
    // New record
    if ($this->id == null) {
        $result = db_query("INSERT INTO request (urgency, importance, request_type, requester_id, brief, detailed, system_id) VALUES (%d, %d, %d, %d, '%s', '%s', %d)",
                        $this->urgency, $this->importance, $this->request_type, $this->requester_id, $this->brief, $this->detailed, $this->system_id); 

    }
    // Already in DB
    else {
        $result = db_query("UPDATE request SET urgency=%d, importance=%d, request_type=%d, requester_id=%d, brief='%s', detailed='%s', system_id=%d WHERE request_id = %d",
                        $this->urgency, $this->importance, $this->request_type, $this->requester_id, $this->brief, $this->detailed, $this->system_id, $this->id);
        if ($result) {
            return true;
        }
        else {
            //Our error handler should catch the query error before
            return false;
        }
    }
    return false;

    }
    else {
        return $valid;
    }
  }
  
  protected function __set($name, $value) {
    error_logging('DEBUG', "Calling WrmsWorkRequest.__set with $name");
    switch ($name) {
      case 'request_id':
        $this->id = $value;
        break;
      default:
        parent::__set($name, $value);
        break;
    }
  }

  protected function __get($name) {
    error_logging('DEBUG', "Calling WrmsWorkRequest.__get with $name");
    switch ($name) {
      case 'timesheets':
        if ($this->timesheets == null) {
            $this->populateChildren();
        }
        return $this->timesheets;
        break;
            
      case 'notes':
        if ($this->notes == null) {
            $this->populateChildren();
        }
        return $this->notes;
        break;
    }
       parent::__get($name);
  }
}

