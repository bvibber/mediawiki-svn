<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

/**
 * This tag extension creates <define> and <display> tags that can be used to display
 * wikicode somewhere in the same page other than where it is initially defined.
 *
 * See http://www.mediawiki.org/wiki/Extension:DelayedDefinition for details.
**/

if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
        $wgHooks['ParserFirstCallInit'][] = 'wfDelayedDefinition';
} else {
        $wgExtensionFunctions[] = 'wfDelayedDefinition';
}

$wgExtensionCredits['parserhook'][] = array(
	'path'           => __FILE__,
	'name'           => 'DelayedDefinition',
	'version'        => '0.5.0',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:DelayedDefinition',
	'author'         => 'Robert Rohde',
	'description'    => 'Allow for wikicode to be defined separately from where it is displayed',
	'descriptionmsg' => 'delaydef_desc',
);

$wgAutoloadClasses['ExtDelayedDefinition'] = dirname( __FILE__ ) . '/DelayedDefinition_body.php';
$wgExtensionMessagesFiles['DelayedDefinition'] = dirname( __FILE__ ) . '/DelayedDefinition.i18n.php';

// Load the classes, which then attaches the parser hooks, etc.
function wfDelayedDefinition() {
	new ExtDelayedDefinition;
	return true;
}
