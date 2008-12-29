<?php
include("../lib/authentication/login.class.php");
include("../lib/response/response.class.php");
$response = new response();

$username = $_POST['username']; # Don't allow logins via GET
$password = $_POST['password']; # Don't allow logins via GET
$format = $_GET['format']; # What format do we want

/*
 * Make sure we were called properly
 */
if (is_null($username)) {
    $response->set(403, "No username supplied");
    echo $response->render($format);
    return;
}
if (is_null($password)) {
    $response->set(403, "No password supplied");
    echo $response->render($format);
    return;
}

if (check_credentials($username, $password)) {
    
} 
else {
    $response->set(403, "Invalid username or password");
    echo $response->render($format);
}

?>
