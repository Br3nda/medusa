<?php

/**
 * WrmsOrganisation object
 * 
 */
class WrmsOrganisation extends WrmsBase {

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
  } 
    
  protected function __set($name, $value) {
    switch ($name) {
      case 'org_code':
        $this->id = $value;
        break;
      case 'org_name':
        $this->name = $value;
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
