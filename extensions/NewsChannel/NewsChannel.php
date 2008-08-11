<?php
/**
* News Channel extension 1.6
* This MediaWiki extension represents a RSS 2.0/Atom 1.0 news channel for wiki project.
* 	The channel is implemented as a dynamic [[Special:NewsChannel|special page]].
* 	All pages from specified category (e.g. "Category:News") are considered
* 	to be articles about news and published on the site's news channel.
* Extension setup file.
* Requires MediaWiki 1.8 or higher.
* Extension's home page: http://www.mediawiki.org/wiki/Extension:News_Channel
*
* Copyright (c) Moscow, 2008, Iaroslav Vassiliev  <codedriller@gmail.com>
* Distributed under GNU General Public License 2.0 or later (http://www.gnu.org/copyleft/gpl.html)
*/

// Set up general channel info here

/** Channel title. */
$wgNewsChannelTitle = 'MyWikiSite.com IT News';
/** Channel description, preferably just one sentence. */
$wgNewsChannelDescription = 'The most hot IT news on MyWikiSite.com.';
/**
* Channel's language code and optional country subcode, e. g. 'en-US'.
* Default is language, specified in $wgLanguageCode variable in LocalSettings.php file. 
*/
$wgNewsChannelLanguage = '';
/** Copyright text. */
$wgNewsChannelCopyright = 'Copyright © MyWikiSite.com. All rights reserved.';
/**
* Channel's logo image. In RSS 2.0 specification only JPG, GIF or PNG formats are allowed;
* recommended default size is 88x31. In Atom 1.0 format an image should have 1:1 aspect ratio.
* The image should be suitable for presentation at a small size.
*/
$wgNewsChannelLogoImage = 'http://www.mywikisite.com/rssicon.png';
/** Time in minutes before channel cache invalidation occurs. */
$wgNewsChannelUpdateInterval = '60';
/** Default number of recent (most fresh) news to list on the channel. */
$wgNewsChannelDefaultItems = 10;
/** Absolute limit of news items to list on the channel. Protects site from overload.*/
$wgNewsChannelMaxItems = 50;
/** Name or alias of channel's editor-in-chief, e. g. 'John Doe'. */
$wgNewsChannelEditorName = '';
/** E-mail of channel's editor-in-chief, e. g. 'newseditor@mywikisite.com'. */
$wgNewsChannelEditorAddress = '';
/** Name or alias of channel's webmaster, e. g. 'Jane Doe'. */
$wgNewsChannelWebMasterName = '';
/** E-mail of channel's webmaster, e. g. 'webmaster@mywikisite.com'. */
$wgNewsChannelWebMasterAddress = '';
/** Title of default category, containing news articles. */
$wgNewsChannelCategory = 'News';
/** Title of default category, that must be absolutely excluded from export. */
$wgNewsChannelExcludeCategory = 'Disputed';
/** Optional prefix to remove from news article title to clean channel headlines. */
$wgNewsChannelRemoveArticlePrefix = 'News/';
/**
* Array of names (wiki accounts) of users, allowed to publish news on the channel;
* leave empty array() to allow everyone, or fill in names e. g. array( 'John Doe', 'Jane Doe', 'Alex' );
* This feature requires MySQL 4.1 or higher.
*/
$wgNewsChannelAuthorizedEditors = array();
/**
* Option to export text only.
* If set to true, links to images and media files are removed from feed.
*/
$wgNewsChannelExportTextOnly = false;

// End of configuration settings

if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install this extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/NewsChannel/NewsChannel.php" );
EOT;
	exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'News Channel',
	'version' => 1.6,
	'author' => 'Iaroslav Vassiliev <codedriller@gmail.com>',
	'description' => 'This MediaWiki extension represents a news channel for wiki project. ' .
		'The channel is implemented as a dynamic [[Special:NewsChannel|special page]].',
	'url' => 'http://www.mediawiki.org/wiki/Extension:News_Channel'
);

//$wgExtensionFunctions[] = 'wfSetupNewsChannelExtension';
$wgAutoloadClasses['NewsChannel'] = dirname( __FILE__ ) . '/NewsChannel_body.php';
$wgExtensionMessagesFiles['NewsChannel'] = dirname( __FILE__ ) . '/NewsChannel.i18n.php';
$wgSpecialPages['NewsChannel'] = 'NewsChannel';
$wgHooks['BeforePageDisplay'][] = 'wfLinkNewsChannelExtensionFeeds';

function wfLinkNewsChannelExtensionFeeds() {
	global $wgOut, $wgNewsChannelTitle;

	$title = Title::newFromText( 'NewsChannel', NS_SPECIAL );
	$wgOut->addLink( array(
		'rel' => 'alternate',
		'type' => 'application/rss+xml',
		'title' => $wgNewsChannelTitle . ' - RSS 2.0',
		'href' => $title->getLocalURL( 'format=rss20' ) ) );
	$wgOut->addLink( array(
		'rel' => 'alternate',
		'type' => 'application/atom+xml',
		'title' => $wgNewsChannelTitle . ' - Atom 1.0',
		'href' => $title->getLocalURL( 'format=atom10' ) ) );

	return true;
}
?>