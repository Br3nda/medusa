<?php
/**
 * @file
 * The Search method
 * @ingroup Search
 */

/**
 * @defgroup Search wrms.search
 *
 * Longer description of your module, including all other files and modules.
 */

require_once('lib/medusa/common.php');

$user = new user($db,$_GET['username']);
echo $user->getFullName();

if (isset($_GET['username'])) {
	//TODO move to method
	$sql = 'SELECT * FROM request INNER JOIN usr ON usr.user_no=request.requester_id WHERE usr.username=\''.$_GET['username'].'\'';
	//TODO use interpolation
	foreach ($db->query($sql) as $row) {
		array_push($output,$row);
	}
}
