<?php
/**
 * wrms.login
 * Logs a user in
 */
class wrms_login {
    
  function run($params) {
    return login::do_login($params);
  }
}
