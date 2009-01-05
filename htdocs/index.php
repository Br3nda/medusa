<?php
/**
 * @mainpage {Medusa} the WRMS API
 *
 * Requirements
 * * Convert existing SQL access, fomr catlayst LAN to Read only - see WRMS/Direct Sql Access 
* Close off psql access from LAN for the general users (that's the one with the super rights) 
* Create a new API to encapsulate the business logic of WRMS, codename = Medusa 
* Convert WRMS web app to use Medusa instead of SQL
* Set up copy if Ned from Egressive, and get working with Medusa
* Get other apps (and teh scripts used by catalysters) to all user Medusa
 *
 *   - @link /api/groups Topics @endlink
 *   - @link /api/constants Constants @endlink
 *   - @link /api/globals Global variables @endlink
 * 
 * Auth 
 */


/**
 * @file 
 * Every request goes through this file
 * 
 * e.g. http://api.wrms.com/wrms.get
 * 
 */

require_once('medusa/common.php');

$uri = $_SERVER['REQUEST_URI'];
$request = new request($uri);
$method = $request->get_method();
$params = $request->get_params();
$format = $request->get_format();
error_logging('DEBUG', "method=$method params=$params format=$format");

if (!$method) {
	error_logging('WARNING', "No method");
	$result = new error("Method required");
}
elseif(!$format) {
	$result = new error("Format required");
}
elseif (class_exists($method)) {
	error_logging('DEBUG', "method $method exists");
	$class = new $method();
	error_logging('DEBUG', "about to run $method");
	$result = $class->run();
}
else {
	error_logging('WARNING', "Method $method does not exist");
	$result = new error("$method does not exist");	
}


$response = new response($result);
error_logging('DEBUG', "Sending response");
echo $response->render($format);
