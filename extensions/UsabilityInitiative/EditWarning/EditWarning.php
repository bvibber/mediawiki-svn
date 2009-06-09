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

// Shortcut to this extension directory
$dir = dirname( __FILE__ ) . '/';

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'EditWarning',
	'author' => 'Roan Kattouw',
	'version' => '0.1.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'editwarning-desc',
);

// Bump the version number every time you change any of the .css/.js files
$wgEditWarningStyleVersion = 0;

// Autoload Classes
$wgAutoloadClasses['EditWarningHooks'] = $dir . 'EditWarning.hooks.php';

// Internationalization
$wgExtensionMessagesFiles['EditWarning'] = $dir . 'EditWarning.i18n.php';

// Register ajax add script hook
$wgHooks['AjaxAddScript'][] = 'EditWarningHooks::addJS';
