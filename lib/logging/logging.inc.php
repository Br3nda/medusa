<?php
function error_logging($type, $message) {
	//TODO only log if debug level reached
	if (preg_match('!^DEBUG!', $type) && !DEBUG_MODE) {
		return;
	}
	error_log("$type $message");
}
