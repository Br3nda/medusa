<?php

/**
 * WorkRequest Object
 */
class WrmsWorkRequest extends WrmsBase {
  private $timesheets;
  private $notes;

  public function populateNow($id = null) {
    if ($id == null) {
      $id = $this->id;
    }
    $result = db_query("SELECT * FROM request WHERE request_id='%d'", $id);
    if (count($result) == 1) {
      $this->populate($result[0]);
    }
  }
  
  protected function __set($name, $value) {
    switch ($name) {
      case 'request_id':
        $this->id = $value;
        break;
      default:
        parent::__set($name,$value);
        break;
    }
  }

  private function __get($name) {
    switch ($name) {
      case 'timesheets':
        if ($this->timesheets == null) {
            $this->timesheets = new WrmsTimeSheets($this->id);
        }
        return $this->timesheets;
        break;
            
      case 'notes':
        if ($this->notes == null) {
          $this->notes = new WrmsRequestNotes($this->id);
        }
        return $this->notes;
        break;
    }
       parent::__get($name);
  }
}
