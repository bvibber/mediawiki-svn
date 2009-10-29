<?php
/**
 * Usability Initiative NavigableTOC extension
 *
 * @file
 * @ingroup Extensions
 *
 * This file contains the include file for the NavigableTOC portion of the
 * UsabilityInitiative extension of MediaWiki.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/UsabilityInitiative/NavigableTOC/NavigableTOC.php" );
 *
 * @author Roan Kattouw <roan.kattouw@gmail.com>
 * @license GPL v2 or later
 * @version 0.1.1
 */

/* Configuration */

// Each module may be configured individually to be globally on/off or user preference based
$wgWikiEditorEnable = array(
	'toolbar' => array( 'global' => false, 'user' => true ),
	'toc' => array( 'global' => false, 'user' => true ),
	'code' => array( 'global' => false, 'user' => true ),
);

/* Setup */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'WikiEditor',
	'author' => 'Trevor Parscal, Roan Kattouw and Nimish Goutam',
	'version' => '0.1.2',
	'url' => 'http://www.mediawiki.org/wiki/Extension:UsabilityInitiative',
	'descriptionmsg' => 'wikieditor-desc',
);

// Includes parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Adds Autoload Classes
$wgAutoloadClasses['WikiEditorHooks'] = dirname( __FILE__ ) . '/WikiEditor.hooks.php';

// Adds Internationalized Messages
$wgExtensionMessagesFiles['WikiEditor'] = dirname( __FILE__ ) . '/WikiEditor.i18n.php';

// Registers Hooks
$wgHooks['EditPageBeforeEditToolbar'][] = 'WikiEditorHooks::addModules';
$wgHooks['GetPreferences'][] = 'WikiEditorHooks::addPreferences';
