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
define( 'RECORDADMIN_VERSION', '1.0.1, 2010-09-20' );

$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['RecordAdmin'] = $dir . 'RecordAdmin.i18n.php';
$wgExtensionAliasesFiles['RecordAdmin']  = $dir . 'RecordAdmin.alias.php';
$wgAutoloadClasses['RecordAdmin']        = $dir . 'RecordAdmin_body.php';

$wgRecordAdminTableMagic = 'recordtable';
$wgRecordAdminDataMagic  = 'recorddata';
$wgRecordAdminTag        = 'recordid';

$wgGroupPermissions['sysop']['recordadmin'] = true;
$wgAvailableRights[] = 'recordadmin';

$wgExtensionCredits['other'][] = array(
	'path'           => __FILE__,
	'name'           => 'Record administration',
	'author'         => array( '[http://www.organicdesign.co.nz/nad Aran Dunkley]', 'Bertrand GRONDIN', 'Siebrand Mazeland' ),
	'descriptionmsg' => 'recordadmin-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:RecordAdmin',
	'version'        => RECORDADMIN_VERSION,
);

$wgExtensionFunctions[] = 'wfSetupRecordAdmin';
function wfSetupRecordAdmin() {
	global $wgRecordAdmin;
	$wgRecordAdmin = new RecordAdmin();
}

$wgHooks['LanguageGetMagic'][] = 'wfRecordAdminLanguageGetMagic';
function wfRecordAdminLanguageGetMagic( &$magicWords, $langCode = 0 ) {
	global $wgRecordAdminTableMagic, $wgRecordAdminDataMagic;
	$magicWords[$wgRecordAdminTableMagic] = array( $langCode, $wgRecordAdminTableMagic );
	$magicWords[$wgRecordAdminDataMagic]  = array( $langCode, $wgRecordAdminDataMagic );
	return true;
}
