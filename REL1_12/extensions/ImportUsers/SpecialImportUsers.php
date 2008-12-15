<?php
/**
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author RouslanZenetl
 * @author YuriyIlkiv
 * @license You are free to use this extension for any reason and mutilate it to your heart's liking.
 */

if (!defined('MEDIAWIKI')) die();
require_once "$IP/includes/SpecialPage.php";

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Import Users',
	'author' => 'Yuriy Ilkiv, Rouslan Zenetl',
	'version' => '2008-02-10',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ImportUsers',
	'description' => 'Imports users in bulk from CSV-file; encoding: UTF-8',
	'descriptionmsg' => 'importusers-desc',
);

$wgAvailableRights[] = 'import_users';
$wgGroupPermissions['bureaucrat']['import_users'] = true;
$dir = dirname(__FILE__) . '/';
extAddSpecialPage( $dir . 'SpecialImportUsers_body.php', 'ImportUsers', 'SpecialImportUsers' );
$wgExtensionMessagesFiles['ImportUsers'] = $dir . 'SpecialImportUsers.i18n.php';
