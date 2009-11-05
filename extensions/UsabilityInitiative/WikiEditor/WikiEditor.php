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
	'highlight' => array( 'global' => false, 'user' => true ),
	'preview' => array( 'global' => false, 'user' => true ),
	'toc' => array( 'global' => false, 'user' => true ),
	'toolbar' => array( 'global' => false, 'user' => true ),
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

// Include parent extension
require_once( dirname( dirname( __FILE__ ) ) . "/UsabilityInitiative.php" );

// Add Autoload Classes
$wgAutoloadClasses['WikiEditorHooks'] = dirname( __FILE__ ) . '/WikiEditor.hooks.php';

// Add Internationalized Messages
$wgExtensionMessagesFiles['WikiEditor'] = dirname( __FILE__ ) . '/WikiEditor.i18n.php';
$wgExtensionMessagesFiles['WikiEditorHighlight'] = dirname( __FILE__ ) . '/WikiEditor/Modules/Highlight/Highlight.i18n.php';
$wgExtensionMessagesFiles['WikiEditorPreview'] = dirname( __FILE__ ) . '/WikiEditor/Modules/Preview/Preview.i18n.php';
$wgExtensionMessagesFiles['WikiEditorToc'] = dirname( __FILE__ ) . '/WikiEditor/Modules/Toc/Toc.i18n.php';
$wgExtensionMessagesFiles['WikiEditorToolbar'] = dirname( __FILE__ ) . '/WikiEditor/Modules/Toolbar/Toolbar.i18n.php';

// Register Hooks
$wgHooks['EditPageBeforeEditToolbar'][] = 'WikiEditorHooks::addModules';
$wgHooks['GetPreferences'][] = 'WikiEditorHooks::addPreferences';
