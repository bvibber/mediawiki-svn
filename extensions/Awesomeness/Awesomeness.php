<?php

/**
 * Initialization file for the Awesomeness extension.
 *
 * @file Awesomeness.php
 * @ingroup Awesomeness
 *
 * @author Jeroen De Dauw
 */

/**
 * This documenation group collects source code files belonging to Awesomeness.
 *
 * @defgroup Awesomeness Awesomeness
 */

define( 'Awesomeness_VERSION', 'of awesomeness' );

$wgExtensionFunctions[] = 'efAwesomenessSetup';

$wgExtensionMessagesFiles['Awesomeness'] = dirname( __FILE__ ) . '/Awesomeness.i18n.php';

$wgHooks['ArticleSave'][] = 'efAwesomenessInsertion';

function efAwesomenessSetup() {
	global $wgExtensionCredits;
	
	wfLoadExtensionMessages( 'Awesomeness' );
	
	$wgExtensionCredits['other'][] = array(
		'path' => __FILE__,
		'name' => wfMsg( 'awesomeness' ),
		'version' => Awesomeness_VERSION,
		'author' => array( '[http://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]' ),
		'url' => 'http://www.mediawiki.org/wiki/Extension:Awesomeness',
		'description' => wfMsg( 'awesomeness-desc' ),
	);	
}

function efAwesomenessInsertion( &$article, &$user, &$text, &$summary, $minor, $watch, $sectionanchor, &$flags ) {
	$awesomeness = array( 'awesomeness', 'awesome' );

	foreach( $awesomeness as $awesome ) {
		$awesome = wfMsg( $awesome );
		$text = preg_replace( "/(^|\s|-)({$awesome}[\?!\.\,]?)(\s|$)/i", " '''$2''' ", $text );
	}
	
	return true;
}