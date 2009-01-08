<?php

/**
 * WorkRequest Object
 */
class WrmsWorkRequest extends WrmsBase {
  private $id;
  private $populated;
  private $data; // Set of key values
  private $timesheets;
  private $notes;

  public function __construct($id = null) {
    $this->data = array();

    if ($id == null) {
      $this->populated = false;
    }
    else if (is_set($id) && is_int($id)) {
      $this->id = $id;
    }
  }

  private function populateNow($id = null) {
    if ($id == null) {
      $id = $this->id;
    }
    $result = db_query("SELECT * FROM request WHERE request_id='%d'", $id);
    if (count($result) == 1) {
      $this->populate($result[0]);
    }
  }
  
  public function getData() {
    //Hack way to get data out
    return $this->data;
  }

    /**
  * Force Extending class to define this metho
    */
  public function populate($row) {
      // TODO, fill this out!
      foreach ($row as $key => $value) {
        $this->$key = $value; # Horrible horrible hack!
      }
      $this->populated = true;
  }

  private function __set($name, $value) {
    $this->data[$name] = $value;
  }

  private function __get($name) {
    switch ($name) {
      case 'timesheets':
        $this->timesheets = new WrmsTimeSheets($this->id);
        return $this->timesheets;
        break;
            
      case 'notes':
        if ($this->notes == null) {
          $this->notes = new WrmsRequestNotes($this->id);
        }
        return $this->notes;
        break;
    }
        
    if (!$this->populated) {
      $this->populateNow();
    }
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }
  }

  private function __isset($name) {
    return isset($this->data[$name]);
  }

  private function __unset($name) {
    $this->data[$name] = null;
  }
}
