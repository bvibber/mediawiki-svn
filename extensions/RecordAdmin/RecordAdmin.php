<?php
if ( !defined( 'MEDIAWIKI' ) ) die( 'Not an entry point.' );
/**
 * Extension:RecordAdmin - MediaWiki extension
 * {{Category:Extensions|RecordAdmin}}{{php}}{{Category:Extensions created with Template:SpecialPage}}
 * @package MediaWiki
 * @subpackage Extensions
 * @author Aran Dunkley [http://www.organicdesign.co.nz/nad User:Nad]
 * @author Bertrand GRONDIN
 * @author Siebrand Mazeland
 * @licence GNU General Public Licence 2.0 or later
 */

define( 'RECORDADMIN_VERSION', '0.8.11, 2009-11-18' );

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

$wgExtensionFunctions[] = 'wfSetupRecordAdmin';
$wgHooks['LanguageGetMagic'][] = 'wfRecordAdminLanguageGetMagic';

$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'Record administration',
	'author'         => array( '[http://www.organicdesign.co.nz/nad User:Nad]', 'Bertrand GRONDIN', 'Siebrand Mazeland' ),
	'description'    => 'A special page for finding and editing record articles using a form',
	'descriptionmsg' => 'recordadmin-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:RecordAdmin',
	'version'        => RECORDADMIN_VERSION,
);

/**
 * Called from $wgExtensionFunctions array when initialising extensions
 */
function wfSetupRecordAdmin() {
	global $wgSpecialRecordAdmin, $wgTitle, $wgParser, $wgHooks, $wgRequest, $wgRecordAdminCategory,
		$wgRecordAdminTag, $wgRecordAdminTableMagic, $wgRecordAdminDataMagic, $wgRecordAdminEditWithForm;

	# Make a global singleton so methods are accessible as callbacks etc
	$wgSpecialRecordAdmin = new SpecialRecordAdmin();

	# Make recordID's of articles created with public forms available via recordid tag
	$wgParser->setHook( $wgRecordAdminTag, array( $wgSpecialRecordAdmin, 'expandTag' ) );

	# Add the parser-functions
	$wgParser->setFunctionHook( $wgRecordAdminTableMagic, array( $wgSpecialRecordAdmin, 'expandTableMagic' ) );
	$wgParser->setFunctionHook( $wgRecordAdminDataMagic,  array( $wgSpecialRecordAdmin, 'expandDataMagic'  ) );

	# Check if posting a public creation form
	$title = Title::newFromText( $wgRequest->getText( 'title' ) );
	if ( is_object( $title ) && $title->getNamespace() != NS_SPECIAL && $wgRequest->getText( 'wpType' ) && $wgRequest->getText( 'wpCreate' ) )
		$wgSpecialRecordAdmin->createRecord();

	# Add some hooks if the current title is a record
	if ( is_object( $title ) ) {
		$types = array();
		$id    = $title->getArticleID();
		$dbr   = &wfGetDB(DB_SLAVE);
		$cat   = $dbr->addQuotes( $wgRecordAdminCategory );
		$cl    = $dbr->tableName( 'categorylinks' );
		$tl    = $dbr->tableName( 'templatelinks' );
		$res   = $dbr->select( $cl, 'cl_from', "cl_to = $cat" );
		while ( $row = $dbr->fetchRow( $res ) ) $types[] = 'tl_title = ' . $dbr->addQuotes( Title::newFromID( $row[0] )->getText() );
		$dbr->freeResult( $res );
		$uses = join( ' OR ', $types );
		if ( $uses && $row = $dbr->selectRow( $tl, 'tl_title', "tl_from = $id AND ($uses)" ) ) {
			global $wgRecordAdminEditWithForm, $wgRecordAdminActionUrl, $wgRecordAdminCurrentType, $wgRecordAdminAddTitleInfo;
			$wgRecordAdminCurrentType = $row->tl_title;

			# Add title info
			if ( $wgRecordAdminAddTitleInfo ) {
				$wgHooks['OutputPageBeforeHTML'][] = 'wfRecordAdminAddTypeInfo';
			}

			# Add an "edit with form" action link
			if ( $wgRecordAdminEditWithForm ) {
				$wgHooks['SkinTemplateTabs'][] = 'wfRecordAdminEditWithForm';
				$qs = "wpType=$wgRecordAdminCurrentType&wpRecord=" . $title->getPrefixedText();
				$wgRecordAdminActionUrl = Title::makeTitle( NS_SPECIAL, 'RecordAdmin' )->getLocalURL( $qs );
			}
		}
	}

}

/**
 * Setup parser-function magic
 */
function wfRecordAdminLanguageGetMagic( &$magicWords, $langCode = 0 ) {
	global $wgRecordAdminTableMagic, $wgRecordAdminDataMagic;
	$magicWords[$wgRecordAdminTableMagic] = array( $langCode, $wgRecordAdminTableMagic );
	$magicWords[$wgRecordAdminDataMagic]  = array( $langCode, $wgRecordAdminDataMagic );
	return true;
}

/**
 * Add action link
 */
function wfRecordAdminEditWithForm( &$skin, &$actions ) {
	global $wgRecordAdminActionUrl;
	$tmp = array();
	foreach ( $actions as $k => $v ) {
		$tmp[$k] = $v;
		if ( $k == 'edit' ) $tmp['editwithform'] = array(
			'text' => wfMsg( 'recordadmin-editwithform' ),
			'class' => false,
			'href' => $wgRecordAdminActionUrl
		);
	}
	$actions = $tmp;
	return true;
}

/**
 * Add record type info below title
 */
function wfRecordAdminAddTypeInfo( &$out, &$text ) {
	global $wgRecordAdminCurrentType;
	$text = '<div class="recordadmin-typeinfo">' . wfMsg( 'recordadmin-typeinfo', $wgRecordAdminCurrentType ) . "</div>\n" . $text;
	return true;
}
