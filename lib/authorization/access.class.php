<?
  class user {
    private $username;
    private $userfullname;
    private $userid;
    private $db;

    function __construct($_db,$_user) { #Foobar hax, it's meant to be a global, but apparently we can't have that. Bitches.
	$this->db=$_db;
	if (is_string($_user) && (preg_match('/^[a-zA-Z0-9]+$/',$_user))) { 
		$sql = 'SELECT * FROM usr WHERE usr.username=\''.$_user.'\'';
	}
	else if (is_int($_user) && ($_user > 0)) {
		$sql = 'SELECT * FROM usr WHERE usr.user_no=\''.$_user.'\'';
	}
	else 
		$self->__destruct(); # Boomshakalaka

	if (isset($sql)) {
		if (!isset($this->db)) { echo "ETWTF?!? NoDBFTL!";}
		$result = $this->db->query($sql)->fetchAll();
		if (count($result) != 1) {
			$self->__destruct(); # Wrong! Try! Again!
		}
		else {
			$this->username = $result[0]['username'];
			$this->userfullname = $result[0]['fullname'];
			$this->userid = $result[0]['user_no'];
		}
	}
    }

    function canHasAccess($_request) {
	# You can has access! What Kind of Access would you like!?
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

    function __destruct() {
    }
  }


/*
Is user active

*/
?>

