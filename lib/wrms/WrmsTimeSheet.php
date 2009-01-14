<?php

/**
 * WorkRequest Object
 */
class WrmsTimeSheet extends WrmsBase {

  public function populateNow($id = null) {
    if ($id == null) {
      $id = $this->id;
    }
    $result = db_query("SELECT * FROM request_id WHERE timesheet_id='%d'", $id);
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
        parent::__set($name,$value);
        break;
    }
  }

  protected function populateChildren() {}
}
