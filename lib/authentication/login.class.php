<?php

/**
 * @author ben
 * Included from htdocs/login.php
 */

    include("../lib/medusa/common.php");

    /*
     * Nice and simple: do they have a valid login
     */
    function check_credentials($username, $password, $userid, $response) {        
        assert(!is_null($username));
        assert(!is_null($password));

        // See if they even exist
        global $db;
        $query = $db->prepare("SELECT password from usr where username = '$username'");
        $query->execute();
        $hash = $query->fetch();

        $hash = $hash['password'];

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
                // Why no response? Because we still have to make a session, until that happens then no point saying they're logged in
                return true;
            } else {
                $response->set('403', "Invalid username or password");
                return false;
            }
        }
        else {
            $response->set('500', "Invalid password format");
            return false;
        }
        // Catch all, shouldn't get called
        return false; 
    }

    function create_session($userid, $response) {

        $session_id = __generate_session_id();

        // Write value to memcache

        $response->set('200', "Success");
        $response->set_var('session_id', time().':'.rand(0000000000000,9999999999999)); 
    }

    function __generate_session_id() {
        return time().':'.rand(0000000000000,9999999999999);
    }


?>
