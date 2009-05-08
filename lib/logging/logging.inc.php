<?php
function error_logging($type, $message) {
	if (preg_match('!^DEBUG!', $type) && !DEBUG_MODE) {
		return;
	}
	error_log("$type $message");
}
