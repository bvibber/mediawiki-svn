<?php

/**
 * MediaWiki support functions for the wikimedia-mobile project hosted 
 * at http://github.com/hcatlin/wikimedia-mobile
 */

/**
 * Increment this when the JS file changes
 */
$wgWikimediaMobileVersion = '2';

/**
 * The base URL of the mobile gateway
 */
$wgWikimediaMobileUrl = 'http://en.m.wikipedia.org/wiki';


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

