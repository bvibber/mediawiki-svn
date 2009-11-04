<?php
/**
 * Usability Initiative EditWarning extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the EditWarning portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/EditWarning/EditWarning.php" );
 *
 * @author Roan Kattouw <roan.kattouw@gmail.com>, Trevor Parscal <tparscal@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

// Bump the version number every time you change any of the .css/.js files
$wgEditWarningStyleVersion = 4;

/* Setup */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'EditWarning',
	'author' => 'Roan Kattouw',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'editwarning-desc',
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$wgAutoloadClasses['EditWarningHooks'] =
	dirname( __FILE__ ) . '/EditWarning.hooks.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['EditWarning'] =
	dirname( __FILE__ ) . '/EditWarning.i18n.php';

// Registers Hooks
$wgHooks['EditPage::showEditForm:initial'][] = 'EditWarningHooks::initialize';
$wgHooks['GetPreferences'][] = 'EditWarningHooks::addPreferences';

// Enable EditWarning by default
$wgDefaultUserOptions['useeditwarning'] = 1;