<?php

/*
* @class Parent class for various authenticatio methods
*/
abstract class AuthenticateUser {

  abstract public static function usable($string); # Quickly determine, is it possible to use this class to authenticate?

  /*
  * Return array of;
  * status: granted|denied|fail
  * message: (if applicable) plain english explanation
  * username: (if applicable) username porition if different from authentication string
  * domain: (if applicable) domain porition of domain string
  * fullname: if returned by mechanism (ie; LDAP)
  * email: if returned by mechanism (ie; LDAP)
  * phone: if returned by mechanism (ie; LDAP)
  * password_updatable: one of; true|false
  *
  */
  abstract public function authenticate($authparams); # Array of zero or more of the following parameters username,password,domain,authority,authenticateduser

  abstract public function updatePassword($oldpassword = null,$newpassword = null);
}

?>

