<?php
/**
 * InlineEditor extension.
 *
 * @file
 * @ingroup Extensions
 *
 * This is the include file for the InlineEditor.
 *
 * Usage: Include the following line in your LocalSettings.php
 * require_once( "$IP/extensions/InlineEditor.php" );
 *
 * To enable all provided editors, add this in your LocalSettings.php:
 * require_once( "$IP/extensions/InlineEditor/InlineEditor.php" );
 * require_once( "$IP/extensions/InlineEditor/SentenceEditor/SentenceEditor.php" );
 * require_once( "$IP/extensions/InlineEditor/ListEditor/ListEditor.php" );
 * require_once( "$IP/extensions/InlineEditor/ReferenceEditor/ReferenceEditor.php" );
 * require_once( "$IP/extensions/InlineEditor/MediaEditor/MediaEditor.php" );
 * require_once( "$IP/extensions/InlineEditor/TemplateEditor/TemplateEditor.php" );
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
$wgAutoloadClasses['InlineEditorPiece']           = $dir . 'InlineEditorPiece.class.php';
$wgAutoloadClasses['InlineEditorPreviousMarking'] = $dir . 'InlineEditorPreviousMarking.class.php';
$wgAutoloadClasses['ExtendedEditPage']            = $dir . 'ExtendedEditPage.class.php';

// register hooks
$wgHooks['MediaWikiPerformAction'][]              = 'InlineEditor::mediaWikiPerformAction';
$wgHooks['EditPage::showEditForm:initial'][]      = 'InlineEditor::showEditForm';

// i18n messages
$wgExtensionMessagesFiles['InlineEditor']         = $dir . 'InlineEditor.i18n.php';

// ajax functions
$wgAjaxExportList[]                               = 'InlineEditor::ajaxPreview';

// default options
$wgInlineEditorBrowserBlacklist                   = $wgBrowserBlackList;
$wgInlineEditorAllowedNamespaces                  = array( NS_MAIN, NS_TALK, NS_USER, NS_USER_TALK );
