<?php
/**
 * SentenceEditor extension for the InlineEditor.
 *
 * @file
 * @ingroup Extensions
 *
 * This is the include file for the SentenceEditor.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/InlineEditor/SentenceEditor/SentenceEditor.php" );
 *
 * @author Jan Paul Posma <jp.posma@gmail.com>
 * @license GPL v2 or later
 * @version 0.0.0
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

// credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'SentenceEditor',
	'author' => array( 'Jan Paul Posma' ),
	'version' => '0.1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InlineEditor#SentenceEditor',
	'descriptionmsg' => 'sentence-editor-desc',
);

// current directory including trailing slash
$dir = dirname( __FILE__ ) . '/';

// add autoload classes
$wgAutoloadClasses['SentenceEditor']         = $dir . 'SentenceEditor.class.php';
$wgAutoloadClasses['ISentenceDetection']     = $dir . 'SentenceDetection/ISentenceDetection.class.php';
$wgAutoloadClasses['SentenceDetectionBasic'] = $dir . 'SentenceDetection/SentenceDetectionBasic.class.php';

// register hooks
$wgHooks['InlineEditorMark'][]               = 'SentenceEditor::mark';
$wgHooks['InlineEditorDefineEditors'][]      = 'SentenceEditor::defineEditors';

// i18n messages
$wgExtensionMessagesFiles['SentenceEditor'] = $dir . 'SentenceEditor.i18n.php';

// default settings
$wgSentenceEditorDetectionDefault = 'SentenceDetectionBasic';
