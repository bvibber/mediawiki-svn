<?php

/**
 * Initialization file for the Deployment extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Deployment
 *
 * @file Deployment.php
 * @ingroup Deployment
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define( 'Deployment_VERSION', '0.1 alpha' );

// Register the initialization function.
$wgExtensionFunctions[] = 'efDeploymentSetup';

// Register the internationalization file.
$wgExtensionMessagesFiles['Deployment'] = dirname( __FILE__ ) . '/Deployment.i18n.php';

// Load and register Special:Dashboard.
$wgAutoloadClasses['SpecialDashboard'] = dirname( __FILE__ ) . '/specials/SpecialDashboard.php';
$wgSpecialPages['Dashboard'] = 'SpecialDashboard';
$wgSpecialPageGroups['Dashboard'] = 'administration';

// Load and register Special:Extensions.
$wgAutoloadClasses['SpecialExtensions'] = dirname( __FILE__ ) . '/specials/SpecialExtensions.php';
$wgSpecialPages['Extensions'] = 'SpecialExtensions';
$wgSpecialPageGroups['Extensions'] = 'administration';

// Load and register Special:Install.
$wgAutoloadClasses['SpecialInstall'] = dirname( __FILE__ ) . '/specials/SpecialInstall.php';
$wgSpecialPages['Install'] = 'SpecialInstall';
$wgSpecialPageGroups['Install'] = 'administration';

// Load and register Special:Update.
$wgAutoloadClasses['SpecialUpdate'] = dirname( __FILE__ ) . '/specials/SpecialUpdate.php';
$wgSpecialPages['Update'] = 'SpecialUpdate';
$wgSpecialPageGroups['Update'] = 'administration';


/**
 * Initialization function for the Deployment extension.
 */
function efDeploymentSetup() {
	global $wgExtensionCredits;
	
	$wgExtensionCredits['other'][] = array(
		'path' => __FILE__,
		'name' => 'Deployment',
		'version' => Deployment_VERSION,
		'author' => '[http://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
		'url' => 'http://www.mediawiki.org/wiki/Extension:Deployment',
		'descriptionmsg' => 'deployment-desc',
	);	
	
}