<?php
/**
 * ParagraphEditor extension for the InlineEditor.
 *
 * @file
 * @ingroup Extensions
 *
 * This is the include file for the ParagraphEditor.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/InlineEditor/ParagraphEditor/ParagraphEditor.php" );
 *
 * @author Jan Paul Posma <jp.posma@gmail.com>
 * @license GPL v2 or later
 * @version 0.0.0
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

// credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'ParagraphEditor',
	'author' => array( 'Jan Paul Posma' ),
	'version' => '0.1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InlineEditor#ParagraphEditor',
	'descriptionmsg' => 'paragraph-editor-desc',
);

// current directory including trailing slash
$dir = dirname( __FILE__ ) . '/';

// add autoload classes
$wgAutoloadClasses['ParagraphEditor']         = $dir . 'ParagraphEditor.class.php';

// register hooks
$wgHooks['InlineEditorMark'][]                = 'ParagraphEditor::mark';
$wgHooks['InlineEditorDefineEditors'][]       = 'ParagraphEditor::defineEditors';

// i18n messages
$wgExtensionMessagesFiles['ParagraphEditor']  = $dir . 'ParagraphEditor.i18n.php';

$wgInlineEditorParagraphEditorVisible = true;