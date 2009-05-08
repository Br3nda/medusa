<?php
/**
 *
 * @ingroup User
 */
class user {
    private $userid;
    private $username;
    private $data = array();
    private $roles = array();
    private $systems = array();

    public function __construct($user) {
        //$user can be either an ID value or a username
        if (intval($user) || is_int($user) && ($user > 0)) {
            $result = db_query("SELECT * FROM usr WHERE usr.user_no=%d", $user);
        }   
        elseif (is_string($user) && (preg_match('!^[a-zA-Z0-9]+$!', $user))) {
            $result = db_query("SELECT * FROM usr WHERE usr.username='%s'", $user);
        }
        else {
            //Provided information isn't whats expected, explode
            return false; 
        }

        //Is there a result
        if (!$result) {
            //No result, explode
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

    private function __set($name, $value) {
        $this->data[$name] = $value;
    }
}
