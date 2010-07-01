<?php
if ( !defined( 'MEDIAWIKI' ) ) die( 'Not an entry point.' );
/**
 * Extension:RecordAdmin - MediaWiki extension
 *
 * @file
 * @ingroup Extensions
 * @author Aran Dunkley [http://www.organicdesign.co.nz/nad User:Nad]
 * @author Bertrand GRONDIN
 * @author Siebrand Mazeland
 * @licence GNU General Public Licence 2.0 or later
 */

define( 'RECORDADMIN_VERSION', '0.12.1, 2010-06-17' );

$wgRecordAdminUseNamespaces = false;     # Whether record articles should be in a namespace of the same name as their type
$wgRecordAdminCategory      = 'Records'; # Category containing record types

$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['RecordAdmin'] = $dir . 'RecordAdmin.i18n.php';
$wgExtensionAliasesFiles['RecordAdmin']  = $dir . 'RecordAdmin.alias.php';
$wgAutoloadClasses['SpecialRecordAdmin'] = $dir . 'RecordAdmin_body.php';
$wgSpecialPages['RecordAdmin']           = 'SpecialRecordAdmin';
$wgSpecialPageGroups['RecordAdmin']      = 'wiki';
$wgRecordAdminTableMagic                 = 'recordtable';
$wgRecordAdminDataMagic                  = 'recorddata';
$wgRecordAdminTag                        = 'recordid';
$wgRecordAdminEditWithForm               = true;
$wgRecordAdminAddTitleInfo               = false;

$wgGroupPermissions['sysop']['recordadmin'] = true;
$wgAvailableRights[] = 'recordadmin';

$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'Record administration',
	'author'         => array( '[http://www.organicdesign.co.nz/nad User:Nad]', 'Bertrand GRONDIN', 'Siebrand Mazeland' ),
	'descriptionmsg' => 'recordadmin-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:RecordAdmin',
	'version'        => RECORDADMIN_VERSION,
);

$wgExtensionFunctions[] = 'efSetupRecordAdmin';
function efSetupRecordAdmin() {
	global $wgSpecialRecordAdmin;
	$wgSpecialRecordAdmin = new SpecialRecordAdmin();
}

$wgHooks['LanguageGetMagic'][] = 'efRecordAdminLanguageGetMagic';
function efRecordAdminLanguageGetMagic( &$magicWords, $langCode = 0 ) {
	global $wgRecordAdminTableMagic, $wgRecordAdminDataMagic;
	$magicWords[$wgRecordAdminTableMagic] = array( $langCode, $wgRecordAdminTableMagic );
	$magicWords[$wgRecordAdminDataMagic]  = array( $langCode, $wgRecordAdminDataMagic );
	return true;
}
