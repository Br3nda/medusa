<?php
/**
* @ingroup User
 */
class access {
  private static $instance;
  private $user; # User object, already initilized, or failing that, a string
  private $org; # Org object
  private $taskcache; # Array of task ids, with returned text levels;

  private function __construct() {
    //Its hidden and those nothing, call updateInfo to set user and org
  }

  public function updateInfo(&$user, &$org = null) {
    
    $this->taskcache = array();
    
    # the next two code blocks are to make up for the fact that php does not support function overloading.
    # Users and orgs may be passed as names, id's, or existing objects
    if (is_object($user) && ($user instanceof user)) {
      $this->user = $user; # Existing user object is usable, wootage.
    }
    elseif (!is_object($user)) {
      $this->user = new user($user); # Cos that's not confusing at all!
    }
    else {
    	return false;
    }
    
    if ($org === null) {
      $this->org = new org($this->user->getOrgID());
    }
    else if (is_object($org) && ($org instanceof org)) {
      $this->org = $org;
    }
    elseif (!is_object($org)) { 
      // It might be an org id. How handy!
      $this->org = new org($org); # Cos that's not confusing at all!
    }
    else {
      return false; # What have you done here?
    }
  }

  public static function getInstance() {
    if (!isset(self::$instance)) {
      $c = __CLASS__;
      self::$instance = new $c();
    }
    return self::$instance;
  }

  public function __clone() {
    trigger_error('Clone is forbidden on this object', E_USER_ERROR);
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
      
      if ($this->user->getUserID() == $this->org->getAdminID()) {
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

/*
 * Edward start on this - ben is going to be redoing it so Ben's methods like here
 */

  /*
   * Action can be any string that's predefined
   * Object is what they are calling the action on
   * e.g. 'wr/create', WrmsWorkRequest object
   */
  public function permitted($action, $object) {
    switch ($action) {
    case 'wr/create':
        return access::permitted_wr_create($object);
        break;
    case 'wr/view':
        return access::permitted_wr_view($object);
        break;
    case 'wr/edit':
        return false;
        break;
    case 'wr/delete':
        return false;
        break;
    }
  }

  private function permitted_wr_create($wr) {
        return false;
  }

  private function permitted_wr_view($wr) {
      // We can deal with either a WR object or an ID
      if (!($wr instanceof WrmsWorkRequest)) {
          $wr = new WrmsWorkRequest(intval($wr));
          $wr->populateNow();
      }

      /*
       * Viewing rules!
       * 1. If you're a support user then you can just see everything because you're considered to be special
       * 2. If you're not support then you have to have 'View', 'Enter', 'Own' or 'Coordinate' role on the System the WR is for
       * 3. I don't know if there's a 3rd option
       */
      $user = currentuser::getInstance();

      if ($user->getUserID() == null) {
          return false; // If they aren't logged in then tell them to go away -- TODO figure out the anonymous access use case for a WR
      }
      
      // Are we a support role?
      $result = db_query("SELECT role_name FROM roles INNER JOIN role_member ON roles.role_no = role_member.role_no WHERE user_no = %d", $user->getUserID());
      while ($row = db_fetch_object($result)) {
          if ($row->role_name == "Support") {
              return true;
          }
      }

      // Do we have any access to the system the WR is for?
      $result = db_query("SELECT role FROM system_usr WHERE system_id = %d AND user_no = %d", $wr->system_id, $user->getUserID());

      // We only need one permission in this group to be allowed to view a WR - doesn't matter which
      while (db_fetch_object($result)) {
          return true;
      }
      return false;
  }

/*
 * Ben's methods end here
 */
  
  
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
  
  //Massive number of permission stubs following
  //May need to be simplified down eventually
  //Will need to review arguments
  public function canUserSeeRequest($wr) {
      return true;
  }

  public function canUserCreateRequest() {
      return true;
  }

  public function canUserEditRequest($wr) {
      return true;
  }

  public function canUserAlterStatus($wr) {
      return true;
  }

  public function canUserSeeStatus($wr) {
      return true;
  }

  public function canUserSeeFiles($wr) {
      return true;
  }

  public function canUserAddFile($wr) {
      return true;
  }

  public function canUserRemoveFile($wr) {
      return true;
  }

  public function canUserGetQuotes($wr) {
      return true;
  }

  public function canUserAddQuote($wr) {
      return true;
  }

  public function canUserApproveQuote($wr) {
      return true;
  }

  public function canUserInvoiceQuote($wr) {
      return true;
  }

  public function canUserGetWRTimesheets($wr) {
      return true;
  }

  public function canUserGetSelfTimesheets() {
      return true;
  }

  // A user can add a timesheet if they work for Catalyst
  // i.e. If they are in one of the Catalyst groups (org_type = 1)
  public function canUserAddTimesheets() {
        return true;
  }

  public function canUserRemoveTimesheets($wr) {
      return true;
  }

  public function canUserInvoiceTimesheets($wr) {
      return true;
  }

  public function canUserAddSubscriber($wr) {
      return true;
  }

  public function canUserGetSubscribers($wr) {
      return true;
  }

  public function canUserRemoveSubscribers($wr) {
      return true;
  }

  public function canUserAllocatePeople($wr) {
      return true;
  }

  public function canUserGetAllocatedPeople($wr) {
      return true;
  }

  public function canUserRemoveAllocatedPeople($wr) {
      return true;
  }

  public function canUserAddQAAction($wr) {
      return true;
  }

  public function canUserGetQAActions($wr) {
      return true;
  }

  public function canUserAddRelations($wr) {
      return true;
  }

  public function canUserGetRelations($wr) {
      return true;
  }

  public function canUserRemoveRelations($wr) {
      return true;
  }

  public function canUserGetNotes($wr) {
      return true;
  }

  public function canUserAddNote($wr) {
      return true;
  }
}

/**
 * @defgroup User wrms.user
 * you need to be a user to do anything
 *
 */
