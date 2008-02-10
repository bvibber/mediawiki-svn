<?php
/**
* EditUser extension by Ryan Schmidt
*/

if(!defined('MEDIAWIKI')) {
	echo("This file is an extension to the MediaWiki software and is not a valid access point");
	die(1);
}

$wgExtensionCredits['specialpage'][] = array(
'name' => 'EditUser',
'description' => 'Allows privelaged users to edit other users\' preferences',
'author' => 'Ryan Schmidt',
'version' => '1.2',
'url' => 'http://www.mediawiki.org/wiki/Extension:EditUser',
);

$wgAutoloadClasses['EditUser'] = dirname(__FILE__) . '/EditUser.page.php';
$wgSpecialPages['EditUser'] = 'EditUser';
$wgAvailableRights[] = 'edituser';

$wgExtensionFunctions[] = 'efEditUser';

/**
* Populate the message cache and register the special page
*/
function efEditUser() {
	global $wgMessageCache;
	require_once( dirname( __FILE__ ) . '/EditUser.i18n.php' );
	foreach( efEditUserMessages() as $lang => $messages )
		$wgMessageCache->addMessages( $messages, $lang );
}
