<?php
/**
 *
 * @ingroup User
 */
class user extends WrmsBase {
    private $userid;
    private $username;
    private $data = array();
    private $roles = array();
    private $systems = array();

    public function __construct($user = null) {
        //$user can be either an ID value or a username
        if (is_null($user)) {
            $this->populated = false;
            return false; 
        }
        if (intval($user) || is_int($user) && ($user > 0)) {
            $result = db_query("SELECT * FROM usr WHERE usr.user_no=%d", $user);
        }   
        elseif (is_string($user) && (preg_match('!^[a-zA-Z0-9]+$!', $user))) {
            $result = db_query("SELECT * FROM usr WHERE usr.username='%s'", $user);
        }
        if (!$result) {
            return false;
        }

        //Loading information into objects
        error_logging('DEBUG', 'Adding in user details');
        $object = db_fetch_object($result);
        $this->userid = $object->user_no;
        $this->username = $object->username;
        foreach ($object as $key=>$val) {
            $this->$key = $val; //Will call the private magic __set function
        }
        $this->loadRoles();
        $this->loadSystemRoles();
    }


    public function populateNow($row = null) {
        error_logging('DEBUG', "usr::populateNow() - begins");
        if (is_null($row)) {
            return false;
        }
        // Convert an object we got with db_fetch_object to an array
        else if (is_object($row)) {
            $row = get_object_vars($row);
        }
        if (is_array($row)) {
            error_logging('DEBUG', "usr::populateNow() - Adding $k -> $v");
            foreach ($row as $k => $v) {
                $this->$k = $v;
            }
        }
        else {
            return false;
        }
    }

    public function populateChildren() {
      $this->loadRoles();
      $this->loadSystemRoles();
    }

    private function loadRoles() {
        //Load roles
        $result = db_query("SELECT m.role_no,r.role_name FROM role_member m INNER JOIN roles r ON m.role_no=r.role_no WHERE m.user_no = %d", $this->userid);
        if ($result) {
            //User has at least one role
            //Add the roles to the list
            while ($obj = db_fetch_object($result)) {
                $this->roles[] = array(
                    'role' => $obj->role_no,
                    'role_name' => $obj->role_name,
                );
            }
        }
    }

    private function loadSystemRoles() {
        //Load system roles
        $result = db_query("SELECT s.role,s.system_id,l.lookup_desc FROM system_usr s INNER JOIN lookup_code l ON s.role=l.lookup_code WHERE l.source_table = 'system_usr' AND s.user_no = %d", $this->userid);
        if ($result) {
            //User has some system roles
            while ($obj = db_fetch_object($result)) {
                $this->systems[$obj->system_id] = array(
                    'system' => $obj->system_id,
                    'role' => $obj->role,
                    'role_name' => $obj->lookup_desc,
                );
            }
        }
    }


    public function getRoles() {
        return $this->roles;
    }

    public function getSystems() {
        return $this->systems;
    }

    public function getID() {
        //Returns the user id
        return $this->userid;
    }

    public function getUsername() {
        //Returns the username
        return $this->username;
    }

    public function __get($name) {
        //Returns information set in the $data array
        return $this->data[$name];
    }

    public function __isset($name) {
        //Checks if a particular value is set in the data array
        return isset($this->data[$name]);
    }

    private function __unset($name) {
        unset($this->data[$name]);
    }

    protected function __set($name, $value) {
        if ($name == 'password') # Let's not show this!
          return;
        $this->data[$name] = $value;
    }
}
