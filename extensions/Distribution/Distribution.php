<?php

/**
 * Initialization file for the Distribution extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Distribution
 *
 * @file Distribution.php
 * @ingroup Distribution
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define( 'Distribution_VERSION', '0.1 alpha' );

// Register the initialization function.
$wgExtensionFunctions[] = 'efDistributionSetup';

// Register the internationalization file.
$wgExtensionMessagesFiles['Distribution'] = dirname( __FILE__ ) . '/Distribution.i18n.php';

$wgHooks['LoadExtensionSchemaUpdates'][] = 'efDistributionSchemaUpdate';

/**
 * Initialization function for the Distribution extension.
 */
function efDistributionSetup() {
	global $wgExtensionCredits;
	
	$wgExtensionCredits['other'][] = array(
		'path' => __FILE__,
		'name' => 'Distribution',
		'version' => Distribution_VERSION,
		'author' => '[http://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
		'url' => 'http://www.mediawiki.org/wiki/Extension:Distribution',
		'descriptionmsg' => 'distribution-desc',
	);	
	
}

function efDistributionSchemaUpdate() {
	global $wgExtNewTables;

	$wgExtNewTables[] = array(
		'distribution_packages',
		dirname( __FILE__ ) . '/distribution.sql'
	);
	
	$wgExtNewTables[] = array(
		'distribution_units',
		dirname( __FILE__ ) . '/distribution.sql'
	);	
	
	$wgExtNewTables[] = array(
		'distribution_unit_versions',
		dirname( __FILE__ ) . '/distribution.sql'
	);		

	return true;
}