<?php

/**
 * @author ben
 * Included from htdocs/login.php
 */

/*
 * Nice and simple: do they have a valid login
 */
function check_credentials($username, $password, $userid, $response) {        
    assert(!is_null($username));
    assert(!is_null($password));

	error_logging('DEBUG', "checking credentials of $username, $password");
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
}

/*
 * Create a session for this user - if the user already has a session, give them their current one
 * Allows multiple scripts to run at the same time and not cause each other to fail
 */
function create_session($current_session, $response) {

    // Check if we are already logged in - of so return our current session ID
    if (is_logged_in($current_session, &$response)) {
        return;
    }

    $session_id = __generate_session_id();

    /*
     * Write value to memcache
     */
    // If the write fails
    if (!memcache::set('medusa_sessionid_'.$session_id, true)) {
        $response->set('500', memcache::report_last_error()." (memcache::set)");
        return;
    }

    // Ok, I know this is a bit pedantic and doubles the cost but it's better to be safe than sorry
    if (!memcache::get('medusa_sessionid_'.$session_id)) {
        $response->set('500', memcache::report_last_error()." (memcache::get)");
        return;
    }

    // If we get this far we have a value in memcache, let the user login
    $response->set('200', "Login Success");
    $response->set_var('session_id', $session_id); 
}

function is_logged_in($session_id, $response) {
    if (memcache::get('medusa_sessionid_'.$session_id)) {
        // Refresh their session
        if (!memcache::set('medusa_sessionid_'.$session_id, true)) {
            $response->set('500', memcache::report_last_error()." (memcache::set)");
            return;
        }
        $response->set('200', 'Login still valid');
        return true;
    }
    else {
        return false;
    }
}

function __generate_session_id() {
    return time().':'.rand(0000000000000,9999999999999);
}


