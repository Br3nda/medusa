<?
require_once('../lib/medusa/common.php');

$user = new user($db,$_GET['username']);
echo $user->getFullName();

if (isset($_GET['username'])) {
	$sql = 'SELECT * FROM request INNER JOIN usr ON usr.user_no=request.requester_id WHERE usr.username=\''.$_GET['username'].'\'';
	foreach ($db->query($sql) as $row) {
		array_push($output,$row);
	}
}

?>
