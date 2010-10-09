<?php
/**
 * TemplateEditor extension for the InlineEditor.
 *
 * @file
 * @ingroup Extensions
 *
 * This is the include file for the TemplateEditor.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/InlineEditor/TemplateEditor/TemplateEditor.php" );
 *
 * @author Jan Paul Posma <jp.posma@gmail.com>
 * @license GPL v2 or later
 * @version 0.0.0
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

// credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'TemplateEditor',
	'author' => array( 'Jan Paul Posma' ),
	'version' => '0.1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InlineEditor#TemplateEditor',
	'descriptionmsg' => 'template-editor-desc',
);

// current directory including trailing slash
$dir = dirname( __FILE__ ) . '/';

// add autoload classes
$wgAutoloadClasses['TemplateEditor']        = $dir . 'TemplateEditor.class.php';

// register hooks
$wgHooks['InlineEditorMark'][]              = 'TemplateEditor::mark';
$wgHooks['InlineEditorDefineEditors'][]     = 'TemplateEditor::defineEditors';

// i18n messages
$wgExtensionMessagesFiles['TemplateEditor'] = $dir . 'TemplateEditor.i18n.php';
