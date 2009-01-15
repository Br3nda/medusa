<?php

/**
 * Base object for all wrms objects, such as work requests, time sheet entries, users, etc
 * For most simple objects, implementing the __set, populateNow, and populateChildren() methods
 * will be sufficient.
 * An example of a simple implementation is WrmsTimeSheet
 * An example of a more complicated implementation with child classes is WrmsWorkRequest
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
    else if (isset($id) && is_int($id)) {
      $this->id = $id;
    }
  }

    /**
  * Force population of object using default method right now.
    */
  abstract public function populateNow($id = null);

    /**
  * method to populate childen objects of this object, such as time sheets
  * in the case of a work request.
  * This is not called automatically, otherwise we'd get recursive objects.
  * If the implemented object is logically an endpoint, such as a request_note
  * or request_status, it is acceptable to make this method a blank implementation.
    */
  abstract public function populateChildren();

    /**
  * Method to populate object using external (or internal) source.
  * @param $row Array of key value pairs for a single row.
    */
  public function populate($row) {
    foreach ($row as $key => $value) {
      $this->$key = $value; # Horrible horrible hack!
    }
    $this->populated = true;
  }

  private function __isset($name) {
    return isset($this->$name);
  }

  private function __unset($name) {
    unset($this->$name);
  }

  protected function __set($name, $value) {
    if (!isset($this->$name)) {
      $this->$name = $value;
    }
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
