<?php
/**
 * wrms.login
 * Logs a user in
 * TODO !!! list the parameters so it gets documented
 */
class wrms_login {
    
  function run($params) {
    return login::do_login($params);
  }
}
