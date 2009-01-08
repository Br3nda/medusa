<?php
include("../lib/authentication/login.class.php");
include("../lib/response/response.class.php");
$response = new response(null);

$username = $_POST['username']; # Don't allow logins via GET
$password = $_POST['password']; # Don't allow logins via GET
$format = $_GET['format']; # What format do we want
$session_id = $_POST['session_id']; # What format do we want

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

if (check_credentials($username, $password, &$userid, &$response)) {
    /*
     * Make a session and all that lovely stuff
     */
    echo "Login success";
    create_session($session_id, &$response);
    echo $response->render($format);
} 
else {
    echo $response->render($format);
}

?>
