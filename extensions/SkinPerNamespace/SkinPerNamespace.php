<?php

/**
 * Extension based on SkinPerPage to allow as skin per namespace
 *
 * Warning : does *not* work for some special pages that don't use SpecialPage
 * class.
 * Require MediaWiki 1.12.0 for the MediaWikiPerformAction hook, will only work
 * for special pages on previous versions.
 *
 * @author Alexandre Emsenhuber
 * @license GPLv2
 */

// For ns >= 0
$wgHooks['MediaWikiPerformAction'][] = 'efSkinPerPageSetSkin';

// For ns == -1
$wgHooks['SpecialPageExecuteBeforePage'][] = 'efSkinPerPageSetSkinSpecial';

// Add credits :)
$wgExtensionCredits['other'][] = array(
	'name'        => 'SkinPerNamespace',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:SkinPerNamespace',
	'version'     => preg_replace('/^.* (\d\d\d\d-\d\d-\d\d) .*$/', '\1', '$LastChangedDate$'), #just the date of the last change
	'description' => 'Allow a per-namespace skin',
	'author'      => 'Alexandre Emsenhuber',
	
);

// Configuration part, you can copy it to your LocalSettings.php and change it
// there, *not* here. Also modify it after including this file or you won't see
// any changes.

/**
 * Array mapping namespace index (i.e. numbers) to skin names
 */
$wgSkinPerNamespace = array();

/**
 * Override preferences for logged in users ?
 * if set to false, this will only apply to anonymous users
 */
$wgSkinPerNamespaceOverrideLoggedIn = true;

// Hook functions

/**
 * Hook function for MediaWikiPerformAction
 */
function efSkinPerPageSetSkin( &$output, &$article, &$title, &$user, &$request ){
	global $wgSkinPerNamespace, $wgSkinPerNamespaceOverrideLoggedIn;
	if( !$wgSkinPerNamespaceOverrideLoggedIn && $user->isLoggedIn() )
		return true;

	$ns = $title->getNamespace();
	if( isset( $wgSkinPerNamespace[$ns] ) )
		$user->mSkin = Skin::newFromKey( $wgSkinPerNamespace[$ns] );

	return true;
}

/**
 * Hook function for SpecialPageExecuteBeforePage
 */
function efSkinPerPageSetSkinSpecial(){
	global $wgSkinPerNamespace, $wgSkinPerNamespaceOverrideLoggedIn, $wgUser;
	if( !$wgSkinPerNamespaceOverrideLoggedIn && $wgUser->isLoggedIn() )
		return true;

	if( isset( $wgSkinPerNamespace[-1] ) )
		$wgUser->mSkin = Skin::newFromKey( $wgSkinPerNamespace[-1] );

	return true;
}