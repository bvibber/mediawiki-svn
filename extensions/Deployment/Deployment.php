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

/**
 * This documenation group collects source code files belonging to Deployment.
 *
 * @defgroup Deployment Deployment
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define( 'Deployment_VERSION', '0.0.0' );

// Register the initialization function.
$wgExtensionFunctions[] = 'efDeploymentSetup';

// Register the internationalization file.
$wgExtensionMessagesFiles['Deployment'] = dirname( __FILE__ ) . '/Deployment.i18n.php';

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