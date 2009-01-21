<?php
/**
 * Created on 5/01/2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


require_once('logging/logging.inc.php');
require('medusa/uri_parser.class.php');
require('response/response.class.php');
require('response/error.class.php');
require('response/response_renderer.class.php');
require('authentication/login.class.php');
require_once('database/database.class.php');
require_once('config/general.php');
require_once('config/database.php');
require_once('authentication/session.class.php');
require_once('authorization/user.class.php');
require_once('authorization/currentuser.class.php');
require_once('authorization/org.class.php');
require_once('authorization/access.class.php');
require_once('medusa/globals.php');
require_once('memcache/memcache.wrapper.class.php');
require('methods/wrms_base_method.php');

if (DEBUG_MODE) {
  require_once('medusa/debug.php');
}

/**
 * Pull all the possible include paths out of the include directory into
 * a var called $path
 */
function __autoload($class_name) {

  foreach (split(':', get_include_path()) as $path) {
    $filename = $path . '/methods/' . $class_name . '.php';
    error_logging('DEBUG', "Include path is: '$path'");
    error_logging('DEBUG', "Including class: $filename");
    // ensure absolute pathing
    if ($path[0] == '/' && is_file($filename) && is_readable($path)) {
      include_once($filename);
      break;
    }
    $filename = $path . '/wrms/' . $class_name . '.php';
    error_logging('DEBUG', "Include path is: '$path'");
    error_logging('DEBUG', "Including class: $filename");
    // ensure absolute pathing
    if ($path[0] == '/' && is_file($filename) && is_readable($path)) { 
      include_once($filename);
      break;
    }
  }

}
