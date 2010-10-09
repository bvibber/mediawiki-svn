<?php
/**
 * ListEditor extension for the InlineEditor.
 *
 * @file
 * @ingroup Extensions
 *
 * This is the include file for the ListEditor.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/InlineEditor/ListEditor/ListEditor.php" );
 *
 * @author Jan Paul Posma <jp.posma@gmail.com>
 * @license GPL v2 or later
 * @version 0.0.0
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

// credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'ListEditor',
	'author' => array( 'Jan Paul Posma' ),
	'version' => '0.1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InlineEditor#ListEditor',
	'descriptionmsg' => 'list-editor-desc',
);

// current directory including trailing slash
$dir = dirname( __FILE__ ) . '/';

// add autoload classes
$wgAutoloadClasses['ListEditor']         = $dir . 'ListEditor.class.php';

// register hooks
$wgHooks['InlineEditorMark'][]           = 'ListEditor::mark';
$wgHooks['InlineEditorDefineEditors'][]  = 'ListEditor::defineEditors';

// i18n messages
$wgExtensionMessagesFiles['ListEditor']  = $dir . 'ListEditor.i18n.php';
