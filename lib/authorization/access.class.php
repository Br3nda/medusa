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

/**
* @ingroup User
*/
class org {
  private $orgid;
  private $orgname;
  private $orgabbrev;
  private $adminid;
  private $db;

  /**
    * Pulls org from database
    */
  function __construct($orgid) {
 
    $result = db_query("SELECT * FROM org WHERE org.ordcode='%d'", $orgid);
    
    if (!$result) {
      return false; # Wrong! Try! Again!
    }
    $record = db_fetch_object($result);

    $this->orgname = $record->org_name;
    $this->orgabbrev = $record->abbreviation;
    $this->orgid = $record->org_code;
    $this->ownerid = $record->admin_user_no;

  }
  
  public function getOrgName() {
    return $this->getorgname;
  }
  
  public function getOrgAbbreviation() {
    return $this->orgabbrev;
  }
  
  public function getOrgID() {
    return $this->orgid;
  }
  
  public function getAdminID() {
    return $this->adminid;
  }
  
  function __destruct() {
  }
}

/**
* @ingroup User
 */
class access {
  private $db;
  private $user; # User object, already initilized, or failing that, a string
  private $org; # Org object
  private $taskcache; # Array of task ids, with returned text levels;
  
  function __construct($db, &$user, &$org = null) {
    
    $this->taskcache = array();
    $this->db = $db;
    
  # the next two code blocks are to make up for the fact that php does not support function overloading.
  # Users and orgs may be passed as names, id's, or existing objects
    if (is_object($user) && ($user instanceof user))
      $this->user = $user; # Existing user object is usable, wootage.
    else if (!is_object($user))
      $this->user = new user($db, $user); # Cos that's not confusing at all!
    else return false;
    
    if ($org === null) {
      $this->org = new org($db, $this->user->getOrgID());
    }
    else if (is_object($org) && ($org instanceof org)) {
      $this->org = $org;
    }
    else if (!is_object($org)) { # It might be an org id. How handy!
    $this->org = new org($db, $org); # Cos that's not confusing at all!
    }
    else {
      return false; # What have you done here?
    }
  }
  
  
    # This sets up the array and sets a bunch of things to false.
  private function initTaskArray($taskid) {
    assert('(!array_key_exists($taskid, $this->taskcache))');
    $this->taskcache[$taskid] = array();
    $this->taskcache[$taskid]['See'] = null;
    $this->taskcache[$taskid]['Delete'] = null;
  }
  
  /**
    * Deep Magic resides here to find out what we have access to
    */
    private function updateTaskAccess($taskid, $level) {
      assert('(array_key_exists($taskid, $this->taskcache))');
      
      if ($this->user->getUserID() == $this->org->getAdminID())
      {
      }
/*
# Gets the roles of the user
# $sql = 'SELECT FROM roles INNER JOIN role_member ON roles.role_no=role_member.role_no WHERE user_no='. $this->user->getUserID .'\'';
      
  $result = $this->db->query($sql)->fetchAll();
  if (count($result) != 0) {
#   $this->userfullname = $result[0]['fullname'];
  }
  else {
   # TODO fail mode;
  }
*/
    }
  
  
  private function getAccessLevel($taskid, $level) {
    // TODO add checking that level is sane.
    if (!array_key_exists($taskid, $this->taskcache)) {
      $this->initTaskArray($taskid);
    }
    if ($this->taskcache[$level] == null) {
      $this->updateTaskAccess($taskid, $level);
    }
    return $this->taskcache[$taskid][$level];
  }
  
  
  public function canUserSeeTask($taskid) {
    return $this->getAccessLevel($taskid, 'See');
  }
  
    // Commented by ben to stop php errors
    /*
  if (is_int($task) && ($task > 0)) {
  
  
    if ($this->taskcache[$taskid]['See'] == "FOO" || $this->taskcache[$taskid] == "BAR")
      return true;
  }
  else return false;
}*/
  
  public function canUserWrite($task) {
    return (is_int($task) && ($task > 0));
  }
  
  
}

/**
 * @defgroup User wrms.user
 * you need to be a user to do anything
 *
 */
