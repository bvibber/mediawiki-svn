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
$egWikimediaMobileVersion = '2';

/**
 * The base URL of the mobile gateway
 */
$wgWikimediaMobileUrl = 'http://en.m.wikipedia.org/wiki';


$wgHooks['BeforePageDisplay'][] = 'efWikimediaMobileAddJs';
$wgHooks['MakeGlobalVariablesScript'][] = 'efWikimediaMobileVars';

function wfWikimediaMobileAddJs( &$outputPage, &$skin ) {
	global $wgOut, $wgExtensionAssetsPath, $egWikimediaMobileVersion;
	
	$wgOut->addScript( Html::linkedScript( 
		"$egExtensionAssetsPath/WikimediaMobile/MobileRedirect.js?$egWikimediaMobileVersion"
	) );
	return true;
}

function efWikimediaMobileVars( &$vars ) {
	global $wgWikimediaMobileUrl;
	$vars['wgWikimediaMobileUrl'] = $wgWikimediaMobileUrl;
	return true;
}

