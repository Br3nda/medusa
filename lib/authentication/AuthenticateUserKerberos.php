<?php
/*
* @class Implements Kerberos Authentication. Returns username porition and domain name only
* @TODO Class needs testing
*/
class AuthenticateUserKerberos extends AuthenticateUserWebserver {
  private $username;
  private $domain;
  private $valid;

  public static function usable($string) {
      $this->valid = false;

      if (preg_match('/^([\w\.\/]+)@([A-Z\.]+)$/', $string, $matches) == 1) {
        $this->domain = $matches[2];
        $splits = preg_split ( '/\//' , $input );
        if (! empty($splits[0])) {
          $this->username = $matches[0]; # we only want the first part
          $this->valid = true;
        }
      }
    return $this->valid;
  }

  public function authenticate($authparams) {
      if (!$this->valid)
        return array ('status' => 'fail');
      else
        return array (
          'status' => 'granted',
          'username' => $this->username,'domain' => $this->domain,
          'password_updatable' => false);
  }

  public function updatePassword($oldpassword = null,$newpassword = null) {
  }
}


