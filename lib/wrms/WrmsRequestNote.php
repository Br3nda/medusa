<?php

/**
 * WrmsRequestNote Object to attach to Work requests
 */
class WrmsRequestNote extends WrmsBase {

  public function populateNow($id = null) {
    if ($id == null) {
      $id = $this->id;
    }
    $result = db_query("SELECT * FROM request_note WHERE request_id='%d'", $id);
    while ($row = db_fetch_obj($result));
    if (count($result) == 1) {
      $this->populate($result[0]);
    }
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
