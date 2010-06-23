<?php

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'inlineSectionEdit',
	'author' => array( 'Michael Dale' ),
	'version' => '0.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'description' => 'Simple example of inline-section editing using js2 & UsabilityInitiative wikiEditor'
);

$wgHooks['BeforePageDisplay'][] = 'inlineSectionJsCheck';

$wgResourceLoaderNamedPaths[ 'inlineSectionEdit' ] =
	"extensions/UsabilityInitiativeJS2/InlineSectionEdit/InlineSectionEdit.js";

function inlineSectionJsCheck( &$out, &$sk ){
	global $wgRequest;
	if( $wgRequest->getText( 'action', 'view' ) == 'view'
	|| $wgRequest->getText( 'action', 'purge' ) == 'purge' ) {
		$out->addNamedResource( 'inlineSectionEdit' );
	}
	return true;
}
?>