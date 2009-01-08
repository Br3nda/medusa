<?php
/**
 *
 * @ingroup User
 */
class user {
  private $username;
  private $userfullname;
  private $userid;
  private $db;
  private $org; # Link to org object
  
  function __construct($user) {

    if (intval($user) || is_int($user) && ($user > 0)) {
      $result = db_query("SELECT * FROM usr WHERE usr.user_no=%d", $user);
    }   
    elseif (is_string($user) && (preg_match('!^[a-zA-Z0-9]+$!', $user))) {
      $result = db_query("SELECT * FROM usr WHERE usr.username='%s'", $user);
    }   
    else {
      return false; 
    }   
    
    if (!$result) {
      return false; 
    }   
    $object = db_fetch_object($result);
    foreach($object as $key => $val) {
        $this->$key = $val; 
    }   
  }
  
  public function getUserID() {
    return $this->userid;
  }
  
  public function getFullName() {
    return $this->userfullname;
  }
  
  public function getUserName() {
    return $this->username;
  }
  
  public function getOrgID() {
    return $this->orgid;
  }
  
  function __destruct() {
  }
}
?>
