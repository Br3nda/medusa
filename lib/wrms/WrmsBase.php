<?php

/**
 * Base object for all wrms objects, such as work requests, time sheet entries, users, etc
 */
abstract class WrmsBase {
    public $id;
    protected $populated;

    /**
    * Force population of object using default method right now.
    */
    public function __construct($id = null) {
      $this->data = array();

      if ($id == null) {
        $this->populated = false;
      }
      else if (is_set($id) && is_int($id)) {
        $this->id = $id;
      }
    }

    /**
    * Force population of object using default method right now.
    */
    abstract public function populateNow($id = null);

    /**
    * When populate is called, it's imperative that we also populate any children
    * an object might have (eg; time sheets for work requests). 
    */
    abstract protected function populateChildren();

    /**
    * Method to populate object using external (or internal) source.
    */
    public function populate($row) {
      foreach ($row as $key => $value) {
        $this->$key = $value; # Horrible horrible hack!
      }
      $this->populateChildren();
      $this->populated = true;
    }

    private function __isset($name) {
        return isset($this->$name);
    }

    private function __unset($name) {
        unset($this->$name);
    }

    protected function __set($name, $value) {
        if (!isset($this->$name))
            $this->$name = $value;
    }

    /**
    * Overloading get status on objects
    * For simple objects, this will be enough.
    */
    private function __get($name) {
      if (!$this->populated) {
          $this->populateNow();
      }
      if (isset($this->$name)) {
          return $this->$name;
      }
      return 0;
    }
}
