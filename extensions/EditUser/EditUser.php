<?php
/**
* EditUser extension by Ryan Schmidt
*/

if(!defined('MEDIAWIKI')) {
	echo "This file is an extension to the MediaWiki software and is not a valid access point";
	die(1);
}

$dir = dirname(__FILE__) . '/';

if(!file_exists($dir . substr($wgVersion, 0, 4) . 'EditUser_body.php')) {
	wfDebug("Your MediaWiki version \"$wgVersion\" is not supported by the EditUser extension");
	return;
}

$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'EditUser',
	'version'        => '1.5.0',
	'author'         => 'Ryan Schmidt',
	'description'    => 'Allows privileged users to edit other users\' preferences',
	'descriptionmsg' => 'edituser-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:EditUser',
);

$wgExtensionMessagesFiles['EditUser'] = $dir . 'EditUser.i18n.php';
$wgExtensionAliasesFiles['EditUser'] = $dir . 'EditUser.alias.php';
$wgAutoloadClasses['EditUser'] = $dir . substr($wgVersion, 0, 4) . 'EditUser_body.php';
$wgSpecialPages['EditUser'] = 'EditUser';
$wgAvailableRights[] = 'edituser';
$wgAvaliableRights[] = 'edituser-exempt';
$wgSpecialPageGroups['EditUser'] = 'users';

#Default group permissions
$wgGroupPermissions['bureaucrat']['edituser'] = true;
$wgGroupPermissions['sysop']['edituser-exempt'] = true;