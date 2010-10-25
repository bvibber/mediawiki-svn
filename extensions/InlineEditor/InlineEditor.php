<?php
/**
 * InlineEditor extension.
 *
 * @file
 * @ingroup Extensions
 *
 * This is the include file for the InlineEditor.
 *
 * Usage: It's recommended to use one of the following configurations in LocalSettings.php:
 * 
 * 1. For editors like "Sentences", "Lists", "Media", etc. use:
 * require_once( "$IP/extensions/InlineEditor/InlineEditorFunctional.php" );
 * 
 * 2. For editors "Sentences", "Paragraphs" and "Sections" use:
 * require_once( "$IP/extensions/InlineEditor/InlineEditorBlocks.php" );
 *
 * @author Jan Paul Posma <jp.posma@gmail.com>
 * @license GPL v2 or later
 * @version 0.0.0
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

// credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'InlineEditor',
	'author' => array( 'Jan Paul Posma' ),
	'version' => '0.1.0',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InlineEditor',
	'descriptionmsg' => 'inline-editor-desc',
);

// current directory including trailing slash
$dir = dirname( __FILE__ ) . '/';

// add autoload classes
$wgAutoloadClasses['InlineEditor']                = $dir . 'InlineEditor.class.php';
$wgAutoloadClasses['InlineEditorText']            = $dir . 'InlineEditorText.class.php';
$wgAutoloadClasses['InlineEditorMarking']         = $dir . 'InlineEditorMarking.class.php';
$wgAutoloadClasses['InlineEditorPiece']           = $dir . 'InlineEditorPiece.class.php';
$wgAutoloadClasses['InlineEditorRoot']            = $dir . 'InlineEditorRoot.class.php';
$wgAutoloadClasses['InlineEditorNode']            = $dir . 'InlineEditorNode.class.php';
$wgAutoloadClasses['ExtendedEditPage']            = $dir . 'ExtendedEditPage.class.php';

// register hooks
$wgHooks['MediaWikiPerformAction'][]              = 'InlineEditor::mediaWikiPerformAction';
$wgHooks['EditPage::showEditForm:initial'][]      = 'InlineEditor::showEditForm';

$wgHooks['InlineEditorPartialBeforeParse']        = array();
$wgHooks['InlineEditorPartialAfterParse']         = array();
$wgHooks['InlineEditorPartialBeforeParse'][]      = 'InlineEditor::partialRenderCite';

// i18n messages
$wgExtensionMessagesFiles['InlineEditor']         = $dir . 'InlineEditor.i18n.php';

// ajax functions
$wgAjaxExportList[]                               = 'InlineEditor::ajaxPreview';

// default options
$wgInlineEditorBrowserBlacklist                   = $wgBrowserBlackList;
$wgInlineEditorAllowedNamespaces                  = array( NS_MAIN, NS_TALK, NS_USER, NS_USER_TALK );
