<?php
/**
 * Main file for the Wikiwyg extension that loads all other stuff
 *
 * @ingroup Extensions
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 );
}

$wgWikiwygEnabled = true;
$wgWysiwygEnabled = true;
$wgInPageEnabled = true;
$wgYuiPath = "http://yui.yahooapis.com/2.3.0/build";
require_once("$IP/extensions/wikiwyg/share/MediaWiki/extensions/MediaWikiWyg.php");
require_once("$IP/extensions/wikiwyg/share/MediaWiki/extensions/WikiwygEditing/WikiwygEditing.php");
require_once("$IP/extensions/wikiwyg/share/MediaWiki/extensions/CreatePage/SpecialCreatePage.php");
require_once("$IP/extensions/wikiwyg/share/MediaWiki/extensions/CreatePage/CreatePageCore.php");

$dir = dirname(__FILE__);
$wgExtensionMessagesFiles['Wikiwyg'] = $dir . '/Wikiwyg.i18n.php';