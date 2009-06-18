<?php
/*
* @class Abstract class for using external sources as authentication methods with
*   username and password, such as LDAP, a DB, RADIUS, etc
*/
abstract class AuthenticateUserPassword extends AuthenticateUser {
    abstract public function getType(); # This should return LDAP, or MySQL or PAM, or similar
    abstract public function getUriPrefix(); # This should return an array of acceptable prefixes such as ldap://
}

