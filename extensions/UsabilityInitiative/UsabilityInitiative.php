<?php
/**
 * Usability Initiative extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the main include file for the UsabilityInitiative
 * extension of MediaWiki.
 *
 * Usage: Add the following line in LocalSettings.php:
 * require_once( "$IP/extensions/UsabilityInitiative/UsabilityInitiative.php" );
 *
 * @author Trevor Parscal <tparscal@wikimedia.org>
 * Allow "or a later version" here?
 * @license GPL v2
 * @version 0.1.1
 */

/* Configuration */

// Set this to true to simply override the stock toolbar for everyone
$wgUsabilityInitiativeCoesxistWithMvEmbed = false;

/* Setup */

// Sets Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'UsabilityInitiative',
	'author' => 'Trevor Parscal',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'usabilityinitiative-desc',
);

// Adds Autoload Classes
$wgAutoloadClasses['UsabilityInitiativeHooks'] =
	dirname( __FILE__ ) . "/UsabilityInitiative.hooks.php";

// Adds Internationalized Messages
$wgExtensionMessagesFiles['UsabilityInitiative'] =
	dirname( __FILE__ ) . "/UsabilityInitiative.i18n.php";

// Includes sub-extensions
require_once( dirname( __FILE__ ) . "/EditToolbar/EditToolbar.php" );
require_once( dirname( __FILE__ ) . "/EditWarning/EditWarning.php" );
require_once( dirname( __FILE__ ) . "/PrefStats/PrefStats.php" );
require_once( dirname( __FILE__ ) . "/OptIn/OptIn.php" );

// Registers Hooks
$wgHooks['AjaxAddScript'][] = 'UsabilityInitiativeHooks::addJs';
$wgHooks['BeforePageDisplay'][] = 'UsabilityInitiativeHooks::addCss';
