<?php
/**
 * wrms.login
 * Logs a user in
 */
class wrms_login {
    
    function run ($params) {
        # If they haven't given us a session ID then they must want to login
        if (is_null($params['GET']['session_id'])) {
            return login::do_login($params);
        }
        # If they have given us a session ID then they think they're logged in, let's check
        else {
            return login::check_session($params['GET']['session_id']);
        }
    }
}
