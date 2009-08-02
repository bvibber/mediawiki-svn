<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
        exit( 1 );
}
 
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'FlagArticle',
	'author' => 'Church of emacs',
	'url' => 'http://www.mediawiki.org/wiki/Extension:FlagArticle',
	'description' => 'Flag article with predefined templates',
	'descriptionmsg' => 'flagarticle-desc',
	'version' => '0.1.1beta',
);
 
$dir = dirname(__FILE__) . '/';
 
$wgAutoloadClasses['FlagArticle'] = $dir . 'FlagArticle.body.php'; # Tell MediaWiki to load the extension body.
$wgAutoloadClasses['FlagArticleTabInstaller'] = $dir . 'FlagArticle.hooks.php';
$wgExtensionMessagesFiles['FlagArticle'] = $dir . 'FlagArticle.i18n.php';
$wgExtensionAliasesFiles['FlagArticle'] = $dir . 'FlagArticle.alias.php';
$wgSpecialPages['FlagArticle'] = 'FlagArticle'; # Let MediaWiki know about your new special page.
$wgHooks['SkinTemplateTabs'][] = array( new FlagArticleTabInstaller(), 'insertTab' ); # Hook displays the "flag" tab on pages
