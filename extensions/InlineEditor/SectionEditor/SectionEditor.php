<?php
/**
 * SectionEditor extension for the InlineEditor.
 *
 * @file
 * @ingroup Extensions
 *
 * This is the include file for the SectionEditor.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/InlineEditor/SectionEditor/SectionEditor.php" );
 *
 * @author Jan Paul Posma <jp.posma@gmail.com>
 * @license GPL v2 or later
 * @version 0.0.0
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

// credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'SectionEditor',
	'author' => array( 'Jan Paul Posma' ),
	'version' => '0.1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InlineEditor#SectionEditor',
	'descriptionmsg' => 'section-editor-desc',
);

// current directory including trailing slash
$dir = dirname( __FILE__ ) . '/';

// add autoload classes
$wgAutoloadClasses['SectionEditor']        = $dir . 'SectionEditor.class.php';

// register hooks
$wgHooks['InlineEditorMark'][]             = 'SectionEditor::mark';
$wgHooks['InlineEditorDefineEditors'][]    = 'SectionEditor::defineEditors';

// i18n messages
$wgExtensionMessagesFiles['SectionEditor'] = $dir . 'SectionEditor.i18n.php';

$wgInlineEditorSectionEditorVisible = true;