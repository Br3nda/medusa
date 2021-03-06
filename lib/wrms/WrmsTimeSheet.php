<?php

/**
 * WorkTimeSheet Object to attach to Work requests
 */
class WrmsTimeSheet extends WrmsBase {

  public function populateNow($id = null) {
    if ($id == null) {
      $id = $this->id;
    }
    $result = db_query("SELECT * FROM request_timesheet WHERE timesheet_id='%d'", $id);
    if (count($result) == 1) {
      $this->populate($result[0]);
    }
  }
  
  protected function __set($name, $value) {
    switch ($name) {
      case 'timesheet_id':
        $this->id = $value;
        break;
      default:
        parent::__set($name, $value);
        break;
    }
  }

  public function populateChildren() {}

  public function writeToDatabase() {
    return false;
  }

}
