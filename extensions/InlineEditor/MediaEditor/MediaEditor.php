<?php
/**
 * MediaEditor extension for the InlineEditor.
 *
 * @file
 * @ingroup Extensions
 *
 * This is the include file for the MediaEditor.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/InlineEditor/MediaEditor/MediaEditor.php" );
 *
 * @author Jan Paul Posma <jp.posma@gmail.com>
 * @license GPL v2 or later
 * @version 0.0.0
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

// credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'MediaEditor',
	'author' => array( 'Jan Paul Posma' ),
	'version' => '0.1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InlineEditor#MediaEditor',
	'descriptionmsg' => 'media-editor-desc',
);

// current directory including trailing slash
$dir = dirname( __FILE__ ) . '/';

// add autoload classes
$wgAutoloadClasses['MediaEditor']        = $dir . 'MediaEditor.class.php';

// register hooks
$wgHooks['InlineEditorMark'][]           = 'MediaEditor::mark';
$wgHooks['InlineEditorDefineEditors'][]  = 'MediaEditor::defineEditors';

// i18n messages
$wgExtensionMessagesFiles['MediaEditor'] = $dir . 'MediaEditor.i18n.php';
