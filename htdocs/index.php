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
$response_renderer = response_renderer::getInstance();

error_logging('DEBUG', "method=$method params=".print_r($params, true)." format=$format");

/*
 * POST variables are not cleaned here
 */
#foreach ($_POST as $k => $v) {
        $params['POST'] = $_POST;
#}

if (!$method) {
	error_logging('ERROR', "No method");
  echo $response_renderer->render(new error("Method required"));
  exit(0);
}
elseif (!$format) {
	error_logging('ERROR', "No format");
  echo $response_renderer->render(new error("Format required"));
  exit(0);
}
else {
  $response_renderer->set_format($format);
}

if (is_null($params['POST']['session_id'])) {
    # Problem, complain not logged in and boot out, unless doing a login
    if ($method == 'wrms_login' && class_exists($method)) {
    	error_logging('DEBUG', "Creating class login::");
    	$class = new wrms_login;
    	$result = $class->run($params);
    } else {
      $result = new error("Session not set.");
      error_logging('WARNING', 'session_id not set');
    }
}
else {
    currentuser::set(new user(login::check_session($params['POST']['session_id'])));
    if (currentuser::getInstance() != null) {
      if (substr($method, 0, 5) == 'wrms_' && class_exists($method)) {
        $access = access::getInstance();
        $access->setUser(currentuser::getInstance());
      	  error_logging('DEBUG', "method $method exists");
      	$class = new $method();
      	  error_logging('DEBUG', "about to run $method");
    	  $result = $class->run($params);
      }
      else {
      	error_logging('WARNING', "Method $method does not exist");
      	$result = new error("$method does not exist");
      }
    }
    else {
    	error_logging('DEBUG', "Session is invalid, timed out, or no longer exists.");
  	  $result = new error("Session is invalid, timed out, or no longer exists.");
    }
}

echo $response_renderer->render($result);
