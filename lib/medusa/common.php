<?php
/**
 * Created on 5/01/2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require('medusa/uri_parser.class.php');
require('response/error.class.php');
require('response/response.class.php');
require('authentication/login.class.php');
require('methods/search.class.php');
require_once('database/database.class.php');
require_once('config/general.php');
require_once('config/database.php');
require_once('authentication/session.class.php');
require_once('authorization/access.class.php');
require_once('medusa/globals.php');
require_once('logging/logging.inc.php');

function __autoload($class_name) {
    $filename = 'methods/' . $class_name . '.php';
    error_logging('DEBUG', "Including class: $filename");
    if (is_file($filename)) {
      include_once($filename);
    }
}
