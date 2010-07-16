<?php
/**
* EditUser extension by Ryan Schmidt
*/

if(!defined('MEDIAWIKI')) {
	echo "This file is an extension to the MediaWiki software and is not a valid access point";
	die(1);
}

$dir = dirname(__FILE__) . '/';

#in case we're running a maintenance script and GlobalFunctions.php isn't loaded...
require_once("$IP/includes/GlobalFunctions.php");

$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'EditUser',
	'version'        => '1.6.1',
	'author'         => 'Ryan Schmidt',
	'descriptionmsg' => 'edituser-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:EditUser',
);

$wgExtensionMessagesFiles['EditUser'] = $dir . 'EditUser.i18n.php';
$wgExtensionAliasesFiles['EditUser'] = $dir . 'EditUser.alias.php';
$wgAutoloadClasses['EditUser'] = $dir . '/EditUser_body.php';
$wgSpecialPages['EditUser'] = 'EditUser';
$wgAvailableRights[] = 'edituser';
$wgAvailableRights[] = 'edituser-exempt';
$wgSpecialPageGroups['EditUser'] = 'users';

#Default group permissions
$wgGroupPermissions['bureaucrat']['edituser'] = true;
$wgGroupPermissions['sysop']['edituser-exempt'] = true;

#Debug mode, enable only if you are testing this extension or if you are having an issue
$wgEditUserDebug = false;
$wgEditUserDebugLog = $dir . 'debug.log';

$wgHooks['SavePreferences'][] = 'efEditUserDebug';

function efEditUserDebug( $eu, $user, &$msg, $old = array() ) {
	global $wgEditUserDebug, $wgEditUserDebugLog;
	if( !$wgEditUserDebug || !$eu instanceOf EditUser )
		return true;
	// $old was added in 1.13, so let's have this work for earlier versions :)
	if( $old === array() ) {
		wfErrorLog( "\n===== BEGIN EDITUSER REQUEST =====\nTime: "
			. wfTime() . "\nNew user state: ".var_export( $user->mOptions, true ), $wgEditUserDebugLog );
	} else {
		wfErrorLog( "\n===== BEGIN EDITUSER REQUEST =====\nTime: "
			. wfTime() . "\nCurrent user state: " . var_export( $old, true )
			. "\nNew user state: ".var_export( $user->mOptions, true ), $wgEditUserDebugLog );
	}
	return true;
}
