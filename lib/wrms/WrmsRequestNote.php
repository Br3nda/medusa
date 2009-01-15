<?php

/**
 * WrmsRequestNote Object to attach to Work requests
 */
class WrmsRequestNote extends WrmsBase {

    public function populateNow($row = null) {

        if (is_null($row)) {
            return false;
        }
        // Convert an object we got with db_fetch_object to an array
        else if (is_object($row)) {
            $row = get_object_vars($row);
        }

        if (is_array($row)) {
            foreach ($row as $k => $v) {
                $this->$k = $v;
            }
        }
        else {
            return false;
        }

        
/*    if ($id == null) {
      $id = $this->id;
    }
    $result = db_query("SELECT * FROM request_note WHERE request_id='%d'", $id);
    while ($row = db_fetch_obj($result));
    if (count($result) == 1) {
      $this->populate($result[0]);
    }*/
  }
  
  protected function __set($name, $value) {
    switch ($name) {
      case 'note_by_id':
        $this->id = $value;
        break;
      default:
        parent::__set($name, $value);
        break;
    }
  }

  public function populateChildren() {}
}
