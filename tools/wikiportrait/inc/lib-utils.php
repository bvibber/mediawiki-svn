<?php
function printr($msg) {
	echo "<pre>".print_r($msg, true)."</pre>";
}

function bail($msg) {
	// Die, or if in debug mode, with a message
	if(GE_DEBUG > 0) {
		die($msg);
	}
	else {
		die();
	}
}

// returns a constant from the $GLOBALS['constants'] array
function disp($const) {
	if ($GLOBALS['constants'][$const]) {
		//  This constant is available
		return $GLOBALS['constants'][$const];
	}
	else {
		// Not available, display when GE_DEBUG is on
		if (GE_DEBUG >= 1) {
			return "Unknown constant: $const";
		}
	}
}

// Functions for translating entries. __() gives an echo, ___() returns the value
// I know, we could have used gettext() for this, but the messages need to be in the
// db, not in .mo files and i'm lazy
function __($const) {
	echo disp($const);
}

function ___($const) {
	return disp($const);
}

// Simple wrapper for the timthumb.php script
function timthumb($src, $width, $height, $zoomcrop = 0, $echo = true) {
	$url = GE_URL."inc/timthumb.php?src=$src&w=$width&h=$height&zc=$zc";
	if($echo) {
		echo $url;
	}
	else {
		return $url;
	}
}

// shows a variable if GE_DEBUG > 1
function debug($var) {
	if (GE_DEBUG >= 1) {
		echo $var;
	}
}

function sanitize_dashed($value) {
	$value = strip_tags($value);
	$value = strtolower($value);
	// kill entities
	$value = preg_replace('/&.+?;/', '', $value);
	$value = preg_replace('/[^%a-z0-9 _-]/', '', $value);
	$value = preg_replace('/\s+/', '-', $value);
	$value = preg_replace('|-+|', '-', $value);
	$value = trim($value, '-');
	return $value;
}

// Checkes wheter we are in a development environment
// either because the current URI has 'localhost', '127.0.0.1' or
// the explicit GE_DEV_MODE is set to true
function is_development_mode() {
	// TODO add checks for localhost/127.0.0.1, etc
	return GE_DEV_MODE;
}
