<?php
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

    $result = db_query("SELECT * FROM organisation org WHERE org.org_code='%d'", $orgid);

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
?>
