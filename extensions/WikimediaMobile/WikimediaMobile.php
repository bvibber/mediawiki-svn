<?php

/**
 * MediaWiki support functions for the Wikimedia-mobile project hosted 
 * at http://github.com/hcatlin/wikimedia-mobile
 */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'WikimediaMobile',
	'author' => 'Tim Starling',
	'url' => 'http://www.mediawiki.org/wiki/Extension:WikimediaMobile',
	'descriptionmsg' => 'wikimediamobile-desc',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['WikimediaMobile'] = $dir . 'WikimediaMobile.i18n.php';

/**
 * Increment this when the JS file changes
 */
$wgWikimediaMobileVersion = '2';

/**
 * The base URL of the mobile gateway
 */
$wgWikimediaMobileUrl = '.m.wikipedia.org';

$wgHooks['BeforePageDisplay'][] = 'wfWikimediaMobileAddJs';
$wgHooks['MakeGlobalVariablesScript'][] = 'wfWikimediaMobileVars';

function wfWikimediaMobileAddJs( &$outputPage, &$skin ) {
	global $wgOut, $wgExtensionAssetsPath, $wgWikimediaMobileVersion;
	
	$wgOut->addScript( Html::linkedScript( 
		"$wgExtensionAssetsPath/WikimediaMobile/MobileRedirect.js?$wgWikimediaMobileVersion"
	) );
	return true;
}

function wfWikimediaMobileVars( &$vars ) {
	global $wgWikimediaMobileUrl;
	$vars['wgWikimediaMobileUrl'] = $wgWikimediaMobileUrl;
	return true;
}

