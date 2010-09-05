<?php
/**
 * ReferenceEditor extension for the InlineEditor.
 *
 * @file
 * @ingroup Extensions
 *
 * This is the include file for the ReferenceEditor.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/InlineEditor/ReferenceEditor/ReferenceEditor.php" );
 *
 * @author Jan Paul Posma <jp.posma@gmail.com>
 * @license GPL v2 or later
 * @version 0.0.0
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

// credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'ReferenceEditor',
	'author' => array( 'Jan Paul Posma' ),
	'version' => '0.1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InlineEditor#ReferenceEditor',
	'descriptionmsg' => 'reference-editor-desc',
);

// current directory including trailing slash
$dir = dirname( __FILE__ ) . '/';

// add autoload classes
$wgAutoloadClasses['ReferenceEditor']        = $dir . 'ReferenceEditor.class.php';

// register hooks
$wgHooks['InlineEditorMark'][]               = 'ReferenceEditor::mark';
$wgHooks['InlineEditorDefineEditors'][]      = 'ReferenceEditor::defineEditors';

// i18n messages
$wgExtensionMessagesFiles['ReferenceEditor'] = $dir . 'ReferenceEditor.i18n.php';