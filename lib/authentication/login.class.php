<?php

/**
 * @author ben
 * Included from htdocs/index.php
 */

class login {

    // Assumes we've already checked for an existing session
    public function do_login($params) {

        $username = $params['POST']['username']; # Don't allow logins via GET
        $password = $params['POST']['password']; # Don't allow logins via GET

        /*
         * Make sure we were called properly
         */
        if (is_null($username)) {
            return new error('No username supplied');
        }
        if (is_null($password)) {
            return new error('No password supplied');
        }

        if (login::valid_credentials($username, $password, &$user_id, &$response)) {
            /*
             * Make a session and all that lovely stuff
             */
            // If we successfully put out session into memcache
            if (login::create_session($user_id, &$response)) {
                currentuser::set(new user($user_id));
                return new response($response);
            }
            // If putting our session into memcache failed
            else {
                return new error($response);
            }

        } 
        else {
                return new error($response);
        }

    }


    /*
     * Nice and simple: do they have a valid login?
     */
    private function valid_credentials($username, $password, $user_id, $response) {        
        assert(!is_null($username));
        assert(!is_null($password));

        error_logging('DEBUG', "checking credentials of $username, $password");
        // See if they even exist
        $result = db_query("SELECT user_no, password from usr where username = '$username'");
        
        $row = db_fetch_object($result);
        $hash = $row->password;

        /*
         * WRMS has passwords in the format: *salt*md5hash
         * We need to get the salt and then salt the password the user provided to us
         */
        // If the password is in the format we expect
        if (preg_match('/^\*(.+)\*.+$/', $hash, $matches)) { 
            
            // Get the salt and has the password we received
            $salt = $matches[1];
            $hash_of_received = sprintf("*%s*%s", $salt, md5($salt . $password));

            // Compare our hashes
            if ($hash_of_received == $hash) {
                $user_id = $row->user_no;
                return true;
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

    /*
     * Create a session for this user - if the user already has a session, give them their current one
     * Allows multiple scripts to run at the same time and not cause each other to fail
     */
    private function create_session($user_id, $response) {

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

    private function __generate_session_id() {
        return time().':'.rand(0000000000000, 9999999999999);
    }

}
