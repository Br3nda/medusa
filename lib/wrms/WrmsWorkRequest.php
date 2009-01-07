<?php

/**
 * WorkRequest Object
 */
class WrmsWorkRequest extends WrmsBase
{
    private $id;
    private $populated;
    private $data; # Set of key values
    private $timesheets;
    private $notes;

    public function __construct($id = null) {
        $this->data = array();

        if ($id == null) {
            $this->populated = false;
        } else if (is_set($id) && is_int($id)) {
            $this->id = $id;
        }
    }

    private function populateNow($id = null) {
        if ($id == null)
            $id = $this->id;
        $result = db_query("SELECT * FROM request WHERE request_id='%d'", $id);
        if (count($result) == 1) 
            $this->populate($result[0]);
    }

    // Force Extending class to define this method
    public function populate($row) {
            # TODO, fill this out!
            foreach ($row as $key => $value)
                $this->$key = $value; # Horrible horrible hack!
            $this->populated = true;
    }

    private function __set($name,$value) {
        $this->data[$name] = $value;
    }

    private function __get($name) {
        if ($name == 'timesheets') {
            if ($this->timesheets == null) { # TODO There might be a slightly better way of doing this, but my brain hurts so much right now.
                $this->timesheets = new WrmsTimeSheets($this->id); # Create the time sheets object
            }
            return $this->timesheets;
        }
        else if ($name == 'notes') { # And so on. There might be a better way!
            if ($this->notes == null) {
                $this->notes = new WrmsRequestNotes($this->id);
            }
            return $this->notes;
        }
        if (!$this->populated)
            $this->populateNow();
        if (array_key_exists($name, $this->data))
            return $this->data[$name];
    }

    private function __isset($name) {
        return isset($this->data[$name]);
    }

    private function __unset($name) {
        $this->data[$name] = null;
    }
}

?>
