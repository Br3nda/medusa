<?php

/**
 * @author ben
 * Included from htdocs/index.php
 */

class login {


    /**
     * Calls all the methods necessary to do a login
     *
     * @param $params
     *      Array of parameters
     *      - $params['POST']['username']: The username of the user POSTed to the page 
     *      - $params['POST']['password']: The password of the user POSTed to the page 
     * @return
     *      A response object with a session ID on success, an error object on failure to login
     */
   public function do_login($params) {
    /* 
     * Assumes we've already checked for an existing session - which we do in index
     * Will hand out as many sessions for a valid login as the user wants
     * If we had malicious users they could use this to flood memcache and force other users sessions to expire
     */
 
        $username = $params['POST']['username']; # Don't allow logins via GET!
        $password = $params['POST']['password']; # Don't allow logins via GET!

        /*
         * Make sure we were called properly
         */
        if (is_null($username) || empty($username)) {
            return new error('No username supplied', 403);
        }
        if (is_null($password) || empty($password)) {
            return new error('No password supplied', 403);
        }
        if (login::valid_credentials($username, $password, $user_id, $response)) {
            // Make a session and all that lovely stuff
            // If we successfully put out session into memcache
            if (login::create_session($user_id, &$response)) {
                currentuser::set(new user($user_id));
                $resp = new response('Login success');
                $resp->set('session_id', $response);
                $resp->set('user_id', $user_id);
                return $resp;
            }
            // If putting our session into memcache failed
            else {
                return new error($response, 500);
            }
        } 
        else {
                return new error($response, 403);
        }
    }

   /**
    * Checks the username and password of a user and returns their ID if they are valid
     * @param $username The username of the person logging in - unclean data
     * @param $password The password of the person logging in - unclean data
     * @param $user_id The ID of the user, which we will set if their details are correct (passed by reference)
     * @param $response A string of text explaining the true/false result
     * @return TRUE if credentials are valid, FALSE if they are not
     */
    private function valid_credentials($username, $password, &$user_id, &$response) {        
        assert(!is_null($username));
        assert(!is_null($password));

        error_logging('DEBUG', "checking credentials of $username, $password");
        // See if they even exist
        $result = db_query("SELECT user_no, password, active from usr where username=%s", $username); // Handles the unclean username - <3 Database Abstraction
        
        if (!$row = db_fetch_object($result)) {
          // Invalid username, but lets not give any clues.
          error_logging('DEBUG', "$username was not found in the usr table");
          $response = "Invalid username or password";
          return false;
        }
        $hash = $row->password;

        /*
         * This is a cheap and easy way to check mulitple passwords, should eventually refactor into something better
         * 
         * Alternate password format: *salt*SHA1hash
         */
        if (preg_match('/^\*(.+)\*{[A-Z]+}.+$/', $hash, $matches)) {
            //Get the salf and the hash of the password received
            $salt = $matches[1];
            $hash_of_received = sprintf("*%s*{SSHA}%s", $salt, base64_encode(sha1($password.$salt, true) . $salt));

            //Compare our hashes
            if ($hash_of_received == $hash) {
                //Check to see if they are still active
                if ($row->active == 't') {
                    $user_id = $row->user_no;
                    return true;
                }
                else {
                    $response = "Your account has been disabled.";
                    return false;
                }
            }
            else {
                $response = "Invalid username or password";
                return false;
            }
        }
        /*
         * WRMS has passwords in the format: *salt*md5hash
         * We need to get the salt and then salt the password the user provided to us
         */
        // If the password is in the format we expect
        elseif (preg_match('/^\*(.+)\*.+$/', $hash, $matches)) { 
            // Get the salt and has the password we received
            $salt = $matches[1];
            $hash_of_received = sprintf("*%s*%s", $salt, md5($salt . $password)); // Handles the unclean password

            // Compare our hashes
            if ($hash_of_received == $hash) {

                // Check to see if they are still active.
                if ($row->active == 't') {
                    $user_id = $row->user_no;
                    return true;
                }
                else {
                    $response = "Your account has been disabled.";
                    return false;
                }
            } 
            else {
                $response = "Invalid username or password";
                return false;
            }
        }

        else {
            $response = "Invalid password format";
            return false;
        }
    }

    /**
     * Creates a session for this user - the user can have multiple sessions
     * Allows multiple scripts to run at the same time and not cause each other to fail
     *
     * @param $user_id: the ID of the current user
     * @param $response: passed by reference, either the reason for the failure or the session ID
     * @return TRUE if the session is created, FALSE if it is not
     */
    private function create_session($user_id, $response) {

        assert(is_numeric($user_id));
        $session_id = login::__generate_session_id();

        /*
         * Write value to memcache
         */
        // If the write fails
        if (!memcached::set('medusa_sessionid_'.$session_id, $user_id)) {
            $response = memcached::report_last_error().' (memcached::set)';
            return false;
        }

        // Ok, I know this is a bit pedantic and doubles the cost but it's better to be safe than sorry
        if (!is_numeric(memcached::get('medusa_sessionid_'.$session_id))) {
            $response = memcached::report_last_error().' (memcached::get)';
            return false;
        }

        // If we get this far we have a value in memcache, let the user login
        $response = $session_id; 
        return true;
    }

    /**
     * Checks to see if their session is still valid in memcache
     * @param
     *      $session_id: The ID of the session we want to check
     * @return
     *      An error string if memcache fails
     *      A user ID if we find their session
     *      A null if we don't find their session
     */
    function check_session($session_id) {
        // If this person is logged in
        $user_id = memcached::get('medusa_sessionid_'.$session_id);
        if (is_numeric($user_id)) {
            // Refresh their session - if it fails, report it
            if (!memcached::set('medusa_sessionid_'.$session_id, $user_id)) {
                return 'Memcache Error: '.memcached::report_last_error().' (memcached::set)';
            }
            // Login still valid, tell them who the user is
            return $user_id;
        }
        else {
            // Login isn't valid, tell them we aren't a user
            return null;
        }
    }
/**
 * Generates a session ID
 * @return
 *      A session ID
 */
    private function __generate_session_id() {
        return uniqid(mt_rand(), true);
    }

}
