<?php
/**
 * FullEditor extension for the InlineEditor.
 *
 * @file
 * @ingroup Extensions
 *
 * This is the include file for the FullEditor.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/InlineEditor/FullEditor/FullEditor.php" );
 *
 * @author Jan Paul Posma <jp.posma@gmail.com>
 * @license GPL v2 or later
 * @version 0.0.0
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

// credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'FullEditor',
	'author' => array( 'Jan Paul Posma' ),
	'version' => '0.1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InlineEditor#FullEditor',
	'descriptionmsg' => 'fulleditor-desc',
);

// current directory including trailing slash
$dir = dirname( __FILE__ ) . '/';

// add autoload classes
$wgAutoloadClasses['FullEditor']        = $dir . 'FullEditor.class.php';

// register hooks
$wgHooks['InlineEditorDefineEditors'][] = 'FullEditor::defineEditors';

// i18n messages
$wgExtensionMessagesFiles['FullEditor'] = $dir . 'FullEditor.i18n.php';