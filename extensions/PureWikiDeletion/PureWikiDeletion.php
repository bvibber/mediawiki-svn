<?php
/**
 * Pure Wiki Deletion extension by Tisane
 * URL: http://www.mediawiki.org/wiki/Extension:PureWikiDeletion
 *
 * This program is free software. You can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version. You can also redistribute it and/or
 * modify it under the terms of the Creative Commons Attribution 3.0 license.
 *
 * This program implements pure wiki deletion. Whenever a page is blanked,
 * it is treated as deleted for all practical purposes, except that any
 * user can view the history and/or unblank it. A link to a blanked page
 * will also show up as a red link.
 */

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the
#	special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
        echo <<<EOT
To install the Pure Wiki Deletion extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/PureWikiDeletion/PureWikiDeletion.php" );
EOT;
        exit( 1 );
}
 
$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'Pure wiki deletion',
	'author' => 'Tisane',
	'url' => 'http://www.mediawiki.org/wiki/Extension:PureWikiDeletion',
	'description' => 'Implements pure wiki deletion',
	'descriptionmsg' => 'purewikideletion-desc',
	'version' => '1.0.3',
);
 
$dir = dirname( __FILE__ ) . '/';

$wgPureWikiDeletionInEffect=true; # Tell other extensions that this is installed

$wgAutoloadClasses['PureWikiDeletionHooks'] = "$dir/PureWikiDeletion.hooks.php";
$wgAutoloadClasses['RandomExcludeBlank'] = "$dir/SpecialPureWikiDeletion.php";
$wgAutoloadClasses['AllPagesExcludeBlank'] = "$dir/SpecialPureWikiDeletion.php";
$wgAutoloadClasses['PopulateBlankedPagesTable'] = "$dir/SpecialPureWikiDeletion.php";
$wgExtensionMessagesFiles['PureWikiDeletion'] = $dir . 'PureWikiDeletion.i18n.php';
$wgExtensionAliasesFiles['PureWikiDeletion'] = $dir . 'PureWikiDeletion.alias.php';
$wgExtensionFunctions[] = 'wfPureWikiDeletion';

$wgHooks['LoadExtensionSchemaUpdates'][] = 'PureWikiDeletionHooks::PureWikiDeletionCreateTable';
$wgHooks['ArticleSaveComplete'][] = 'PureWikiDeletionHooks::PureWikiDeletionSaveCompleteHook';
$wgHooks['ArticleSave'][] = 'PureWikiDeletionSaveHook';
$wgHooks['LinkBegin'][] = 'PureWikiDeletionHooks::PureWikiDeletionLink';
$wgHooks['EditPage::showEditForm:initial'][] = 'PureWikiDeletionHooks::PureWikiDeletionEditHook';
$wgHooks['ArticleDeleteComplete'][] = 'PureWikiDeletionHooks::PureWikiDeletionDeleteHook';
$wgHooks['ArticleUndelete'][] = 'PureWikiDeletionHooks::PureWikiDeletionUndeleteHook';
$wgHooks['GetPreferences'][] = 'PureWikiDeletionGetPreferences';
$wgHooks['ParserFirstCallInit'][] = 'PureWikiDeletionHooks::efPureWikiDeletionParserFunction_Setup';
$wgHooks['LanguageGetMagic'][]       = 'PureWikiDeletionHooks::efPureWikiDeletionParserFunction_Magic';
$wgHooks['AlternateEdit'][] = 'PureWikiDeletionAlternateEditHook';
$wgHooks['OutputPageParserOutput'][] = 'PureWikiDeletionHooks::PureWikiDeletionOutputPageParserOutputHook';

$wgDefaultUserOptions['watchblank'] = 0;
$wgDefaultUserOptions['watchunblank'] = 0;

$wgLogTypes[] 			= 'blank';
$wgLogNames['blank']		= 'blank-log-name';
$wgLogHeaders['blank']		= 'blank-log-header';
$wgLogActions['blank/blank'] 	= 'blank-log-entry-blank';
$wgLogActions['blank/unblank']  = 'blank-log-entry-unblank';

$wgSpecialPages['RandomExcludeBlank'] = 'RandomExcludeBlank';
$wgSpecialPages['AllPagesExcludeBlank'] = 'AllPagesExcludeBlank';
$wgSpecialPages['PopulateBlankedPagesTable'] = 'PopulateBlankedPagesTable';
$wgSpecialPageGroups['RandomExcludeBlank'] = 'redirects';
$wgSpecialPageGroups['AllPagesExcludeBlank'] = 'pages';
$wgSpecialPageGroups['PopulateBlankedPagesTable'] = 'wiki';

# User right to execute Special:PopulateBlankedPagesTable
$wgAvailableRights[] = 'purewikideletion';
$wgGroupPermissions['bureaucrat']['purewikideletion']    = true;

function wfPureWikiDeletion() {
    wfLoadExtensionMessages( 'PureWikiDeletion' );
}

function wfBlankLogActionText( $type, $action, $title = null, $skin = null, $params = array(), $filterWikilinks = false ) {
	$rv = wfMsgReal( 'blank-log-entry-blank', $params );
	return $rv;
}

function wfUnblankLogActionText( $type, $action, $title = null, $skin = null, $params = array(), $filterWikilinks = false ) {
	$rv = wfMsgReal( 'blank-log-entry-unblank', $params );
	return $rv;
}

function PureWikiDeletionSaveHook( &$article, &$user, &$text, &$summary,
       $minor, $watch, $sectionanchor, &$flags ) {
       global $wgOut, $wgPureWikiDeletionLoginRequiredToBlank, $wgPureWikiDeletionLoginRequiredToUnblank;
       if ( $text == "" ) {
		if ( $wgPureWikiDeletionLoginRequiredToBlank && !( $user->isLoggedIn() ) ) {
			$wgOut->showErrorPage( 'purewikideletion-blanknologin', 'purewikideletion-blanknologintext' );
			return false;
		}
		if ( $summary == wfMsgForContent( 'autosumm-blank' ) ) {
			$hasHistory = false;
			$summary = $article->generateReason( $hasHistory );
		}
	} else {
		$dbr = wfGetDB( DB_SLAVE );
		$blank_page_id = $article->getID();
		$result = $dbr->selectRow( 'blanked_page', 'blank_page_id'
			, array( "blank_page_id" => $blank_page_id ) );
		if ( $result ) {
			if ( $wgPureWikiDeletionLoginRequiredToUnblank && !( $user->isLoggedIn() ) ) {
				$wgOut->showErrorPage( 'purewikideletion-blanknologin', 'purewikideletion-blanknologintext' );
				return false;
			}
			if ( $summary == '' ) {
				$summary = $article->getAutosummary( '', $text, EDIT_NEW );
			}
		}
	}
	return true;
}

function PureWikiDeletionGetPreferences( $user, &$preferences ) {
	$prefs = array(
			'watchblank' => array(
				'type' => 'check',
				'section' => 'watchlist/advancedwatchlist',
				'label-message' => 'purewikideletion-pref-watchblank',
			),
			'watchunblank' => array(
				'type' => 'check',
				'section' => 'watchlist/advancedwatchlist',
				'label-message' => 'purewikideletion-pref-watchunblank',
			)
		);
	$after = array_key_exists( 'watchcreations', $preferences ) ? 'watchcreations' : 'watchdefault';
	$preferences = wfArrayInsertAfter( $preferences, $prefs, $after );
	return true;
}

function PureWikiDeletionAlternateEditHook ( $editPage ) {
	global $wgUser, $wgOut, $wgPureWikiDeletionLoginRequiredToUnblank;
	if ( $wgUser->isLoggedIn() || !$wgPureWikiDeletionLoginRequiredToUnblank ) {
		return true;
	}
	$dbr = wfGetDB( DB_SLAVE );
	$blank_page_id = $editPage->getArticle()->getID();
	$result = $dbr->selectRow( 'blanked_page', 'blank_page_id', array
	       ( "blank_page_id" => $blank_page_id ) );
	if ( !$result ) {
	       return true;
	}
	$wgOut->showErrorPage( 'purewikideletion-blanknologin', 'purewikideletion-unblanknologintext' );
	return false;
}