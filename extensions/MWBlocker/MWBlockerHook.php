<?php

require_once( 'MWBlocker.php' );

$wgExtensionFunctions[] = 'mwBlockerHookSetup';

function mwBlockerHookSetup() {
	global $wgHooks;
	// create account
	$wgHooks['AddNewAccount'][] = 'mwBlockerCheck';
}

function mwBlockerCheck() {
	$ip = wfGetIP();
	MWBlocker::queueCheck( $ip, "creating account" );
	return true;
}

?>
