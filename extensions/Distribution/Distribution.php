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

include_once 'Distribution_Settings.php';

// Register the initialization function.
$wgExtensionFunctions[] = 'efDistributionSetup';

// Register the internationalization file.
$wgExtensionMessagesFiles['Distribution'] = dirname( __FILE__ ) . '/Distribution.i18n.php';

// Load classes.
$wgAutoloadClasses['DistributionRelease'] = dirname( __FILE__ ) . '/includes/DistributionRelease.php'; 
$wgAutoloadClasses['ExtensionDataImporter'] = dirname( __FILE__ ) . '/includes/ExtensionDataImporter.php';

// Hook registration.
$wgHooks['LoadExtensionSchemaUpdates'][] = 'efDistributionSchemaUpdate';

// API modules registration.
$wgAutoloadClasses['ApiQueryExtensions'] = dirname( __FILE__ ) . '/api/ApiQueryExtensions.php';
$wgAPIListModules['extensions'] = 'ApiQueryExtensions';

$wgAutoloadClasses['ApiQueryPackages'] = dirname( __FILE__ ) . '/api/ApiQueryPackages.php';
$wgAPIModules['packages'] = 'ApiQueryPackages';

$wgAutoloadClasses['ApiExtensionVersions'] = dirname( __FILE__ ) . '/api/ApiExtensionVersions.php';
$wgAPIModules['extensionversions'] = 'ApiExtensionVersions';

$wgAutoloadClasses['ApiUpdates'] = dirname( __FILE__ ) . '/api/ApiUpdates.php';
$wgAPIModules['updates'] = 'ApiUpdates';

/**
 * Initialization function for the Distribution extension.
 * 
 * @since 0.1
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

/**
 * LoadExtensionSchemaUpdates hook.
 * 
 * @since 0.1
 * 
 * @return true
 */
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