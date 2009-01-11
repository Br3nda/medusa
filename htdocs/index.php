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
$params = array('GET' => array(), 'POST' => array());

$uri = $_SERVER['REQUEST_URI'];
$Uri_Parser = new Uri_Parser($uri);
$method = $Uri_Parser->get_method();
$params['GET'] = $Uri_Parser->get_params();
$format = $Uri_Parser->get_format();

error_logging('DEBUG', "method=$method params=".print_r($params, true)." format=$format");

/*
 * POST variables are not cleaned here
 */
foreach ($_POST as $k => $v) {
        $params['POST'] = $_POST;
}

if (!is_null($params['POST']['session_id'])) {
    currentuser::set(new user(login::check_session($params['POST']['session_id'])));
}

$access = access::getInstance();
$access->updateInfo(currentuser::getInstance());

if (!$method) {
	error_logging('WARNING', "No method");
	$result = new error("Method required");
}
elseif (!$format) {
	$result = new error("Format required");
}
elseif (class_exists($method)) {
	error_logging('DEBUG', "method $method exists");
	$class = new $method();
	error_logging('DEBUG', "about to run $method");
	$result = $class->run($params);
}
else {
	error_logging('WARNING', "Method $method does not exist");
	$result = new error("$method does not exist");	
}

//$response = new response($result);
error_logging('DEBUG', "Sending response");
$response_renderer = new response_renderer($result);
echo $response_renderer->render($format);
