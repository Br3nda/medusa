<?php
/*
* @class allows authentication via LDAP bind
* Support URI's are of the format;
* ldap://host:port/DN?attributes?scope
* attributes should specify, in order, fullname, email, phone number - defaults are cn,mail,telephoneNumber
* Example;
* ldaps://ldap.catalyst.net.nz/uid=<username>,ou=Staff,ou=People,dc=catalyst,dc=net,dc=nz?cn,mail?sub
*/
class AuthenticateUserBindLDAP extends AuthenticateUserPassword {
    
    private $prefix;
    private $host;
    private $port;
    private $dn;
    private $attr_name;
    private $attr_email;
    private $attr_phone;
    private $scope;

    private $valid;
    private $auth_only;
  
    public function getType() {
      return 'ldap';
    }
    public function getUriPrefix() {
      return array('ldap://','ldaps://','ldapi://');
    }

    /*
    * @TODO, add support for filters
    */ 
    public function usable($string) {
      #preg_match('/^(ldap[i|s]?):\/\/((:?(:?<domain>)|[a-zA-Z0-9_\.,])+)(?:\:(\d+))?\/((:?(:?<username>)|[a-zA-Z0-9\%_\=,])+)\?([a-zA-Z0-9\%_\,]+)(:?\?((:?base)|(:?one)|(:?sub)|(:?children)))?$/', $string, $matches); # This does work, but a faster/cleaner way has been written below

      $this->valid = true;
      preg_match('/^(ldap[i|s]?):\/\/(.*?)\/(.*?)$/', $string, $matches);
      if (count($matches) != 4)
        return false;
      else {
        $this->prefix = $matches[1]; # This isn't nessacery at this point, but since it's written, we may as well leave it in.
        $this->host = $matches[2];
          $splits = explode('?',$matches[3]);
        $this->dn = $splits[0];
        $this->attrs = $splits[1];
        $this->scope = $splits[2];
        $this->filter = $splits[3];
      }
      if ($this->prefix == 'ldapi') {
        return false; # ldapi not support by PHP at this point
      }
#      if ($this->prefix == 'ldapi' && (!is_null($this->host) || $this->host != 'localhost')) {
#        #echo "prefix is $this->prefix, but host ($this->host) not local<br>";
#        return false; # Because you can't do an local socket connection to something that's not local
#      }
      if (!preg_match('/^(([a-zA-Z0-9-\.])|(<domain>))+(:\d+)?$/', $this->host,$matches)) {
        #echo "Bad host $this->host<br>";
         $this->valid = false;
      }
      else {
        print_r($matches);
        if (count($matches) == 5) {
          $portsplits = explode(':',$host);
          $this->host = $portsplits[0];
          $this->port = $portsplits[1];
        }
      }
      if (!preg_match('/^(([a-zA-Z0-9-\,\.\%\=])|(<username>))+$/', $this->dn)) { # todo, do we want a domain?
        #echo "Bad dn $dn<br>";
         $this->valid = false;
      }
      if (is_null($attrs) || $attrs == '') {
        $this->auth_only = true;
      }
      if (!preg_match('/^[a-zA-Z0-9-\,\.\%]+$/', $attrs)) {
        #echo "Bad attrs $attrs<br>";
         $this->valid = false;
      } else {
        $this->attrs = explode(',',$this->attrs);
        # TODO - this needs fixing
      }
      if (is_null($this->scope) || $this->scope == '') {
        $this->scope = 'sub';
      }
      else if (!preg_match('/^((base)|(one)|(sub)|(children))$/', $this->scope)) {
        #echo "Bad scope $scope<br>";
         $this->valid = false;
      }
      return $this->valid;
    }

    public function authenticate($authparams) {
        if (!is_array($authparams))
          return array ('status' => 'fail', 'message' => 'Internal API failure.');
        if (preg_match('/<username>/',$this->dn) && is_empty($authparams['username']))
          return array ('status' => 'fail', 'message' => 'No username string provided when required.');
        if (preg_match('/<domain>/',$this->host) && is_empty($authparams['domain']))
          return array ('status' => 'fail', 'message' => 'No domain string provided when required.');
        if (is_empty($authparams['password']))
          return array ('status' => 'fail', 'message' => 'No password provided.');

        $this->dn = str_replace('<username>',$authparams['username'], $this->dn);
        $this->host = str_replace('<domain>',$authparams['domain'], $this->host);

        $ldapconn = ldap_connect($this->host,$this->port)
#          or die("Could not connect to LDAP server."); # TODO, replace this with real code that doesn't 'splode everything

        if ($ldapconn) {
          $ldapbind = ldap_bind($ldapconn, $this->dn, $authparams['password']);
          if ($ldapbind) {
            $attributes = array();
            if (!empty($this->attr_name)) $attributes[] = $this->attr_name;
            if (!empty($this->attr_email)) $attributes[] = $this->attr_email;
            if (!empty($this->attr_phone)) $attributes[] = $this->attr_phone;
            if empty($attributes) {
                $results = ldap_search($ldapconn,$dn,'',$attributes);
                $entries = ldap_get_entries($ldapconn, $results);
                return array ('status' => 'granted',
                  'name' =>$entries[0][$this->attr_name)][0],
                  'email' =>$entries[0][$this->attr_email)][0],
                  'phone' =>$entries[0][$this->attr_phone)][0]);
            }
            else {
              ldap_unbind($ldapconn);
              return array ('status' => 'granted');
            }
          }
          else {
            ldap_unbind($ldapconn);
            return array ('status' => 'denied', 'message' => 'Login failed.');
          }
    }

    public function updatePassword($oldpassword = null,$newpassword = null) { # This information may not be retrievable from LDAP at this time
    }
}

