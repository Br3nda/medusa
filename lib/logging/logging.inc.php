<?php
function error_logging($type, $message) {
	//TODO only log if debug level reached
	error_log("$type $message");
	echo "<li>$type $message</li>";
}