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
 * Usage: This file is included automatically by ../UsabilityInitiative.php
 *
 * @author Roan Kattouw <roan.kattouw@gmail.com>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

// Bump the version number every time you change any of the .css/.js files
$wgEditWarningStyleVersion = 2;

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

// Adds Autoload Classes
$wgAutoloadClasses['EditWarningHooks'] =
	dirname( __FILE__ ) . '/EditWarning.hooks.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['EditWarning'] =
	dirname( __FILE__ ) . '/EditWarning.i18n.php';

// Registers Hooks
$wgHooks['AjaxAddScript'][] = 'EditWarningHooks::initialize';
