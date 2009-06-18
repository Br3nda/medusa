<?php
/*
* @class Default class for authenticating using the same database as the rest of WRMS
*/
class AuthenticateUserLocalDB extends AuthenticateUserPassword {
    public function getType() {
      return 'local';
    }
    public function getUriPrefix() {
      return array('');
    }

    public static function usable($string) { # Where are we gettting this uri from?
    }

    public function authenticate() {
    }

    public function updatePassword($oldpassword = null,$newpassword = null) {
    }

}

