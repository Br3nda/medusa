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

  public function writeToDatabase() {
    return false;
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

