<?php

    include("../lib/medusa/common.php");

    /*
     * Nice and simple: do they have a valid login
     */
    function check_credentials($username, $password) {        
        assert(!is_null($username));
        assert(!is_null($password));

        // See if they even exist
var_dump($db);
        //$result = $db::query("SELECT password from usr where username = '$username'");
var_dump($result);

  /*
   * if ( !function_exists("session_validate_password") ) { 
  /** 
  * Checks what a user entered against the actual password on their account.
  * @param string $they_sent What the user entered.
  * @param string $we_have What we have in the database as their password.  Which may (or may not) be a salted MD5.
  * @return boolean Whether or not the users attempt matches what is already on file.
  function session_validate_password( $they_sent, $we_have ) { 
    if ( preg_match('/^\*\*.+$/', $we_have ) ) { 
      //  The "forced" style of "**plaintext" to allow easier admin setting
      return ( "**$they_sent" == $we_have );
    }   

    if ( preg_match('/^\*(.+)\*{[A-Z]+}.+$/', $we_have, $regs ) ) { 
      if ( function_exists("session_salted_sha1") ) { 
        // A nicely salted sha1sum like "*<salt>*{SSHA}<salted_sha1>"
        $salt = $regs[1];
        $sha1_sent = session_salted_sha1( $they_sent, $salt ) ; 
        return ( $sha1_sent == $we_have );
      }   
      else {
        dbg_error_log( "ERROR", "Password is salted SHA-1 but you are using PHP4!" );
        echo <<<EOERRMSG
<html>
<head>
<title>Salted SHA1 Password format not supported with PHP4</title>
</head>
<body>
<h1>Salted SHA1 Password format not supported with PHP4</h1>
<p>At some point you have used PHP5 to set the password for this user and now you are 
   using PHP4.  You will need to assign a new password to this user using PHP4, or ensure
   you use PHP5 everywhere (recommended).</p>
<p>AWL has now switched to using salted SHA-1 passwords by preference in a format
   compatible with OpenLDAP.</p>
</body>
</html>
EOERRMSG;
        exit;
      }   
    }   

    if ( preg_match('/^\*(.+)\*.+$/', $we_have, $regs ) ) { 
      // A nicely salted md5sum like "*<salt>*<salted_md5>"
      $salt = $regs[1];
      $md5_sent = session_salted_md5( $they_sent, $salt ) ; 
      return ( $md5_sent == $we_have );
    }   

    // Anything else is bad
    return false;

  }
}

   */      
         
        //SQL goes here to see if they are in the database       
//        $db::query(


        return false; 
    }


?>
