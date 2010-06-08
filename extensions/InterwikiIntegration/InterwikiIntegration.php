<?php
/**
 * InterwikiIntegration extension by Tisane
 * URL: http://www.mediawiki.org/wiki/Extension:InterwikiIntegration
 *
 * This program is free software. You can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version. You can also redistribute it and/or
 * modify it under the terms of the Creative Commons Attribution 3.0 license
 * or any later version.
 * 
 * This extension causes interwiki links to turn blue if the target page exists on
 * the target wiki, and red if it does not. It only works when the target wiki
 * is on the same wiki farm and is set up with this same extension.
 */
 
/* Alert the user that this is not a valid entry point to MediaWiki if they try to access the
special pages file directly.*/
 
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
		To install the InterwikiIntegration extension, put the following line in LocalSettings.php:
		require( "extensions/InterwikiIntegration/InterwikiIntegration.php" );
EOT;
	exit( 1 );
}
 
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Interwiki Integration',
	'author' => 'Tisane',
	'url' => 'http://www.mediawiki.org/wiki/Extension:InterwikiIntegration',
	'descriptionmsg' => 'integration-desc',
	'version' => '1.0.3',
);
 
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['InterwikiIntegrationHooks'] = $dir . 'InterwikiIntegration.hooks.php';
$wgAutoloadClasses['PopulateInterwikiIntegrationTable'] = "$dir/SpecialInterwikiIntegration.php";
$wgExtensionMessagesFiles['InterwikiIntegration'] = $dir . 'InterwikiIntegration.i18n.php';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'InterwikiIntegrationHooks::InterwikiIntegrationCreateTable';
$wgHooks['ArticleEditUpdates'][] = 'InterwikiIntegrationHooks::InterwikiIntegrationArticleEditUpdates';
$wgHooks['LinkBegin'][] = 'InterwikiIntegrationHooks::InterwikiIntegrationLink';
$wgHooks['ArticleInsertComplete'][] = 'InterwikiIntegrationHooks::InterwikiIntegrationArticleInsertComplete';
$wgHooks['ArticleDeleteComplete'][] = 'InterwikiIntegrationHooks::InterwikiIntegrationArticleDeleteComplete';
$wgHooks['ArticleUndelete'][] = 'InterwikiIntegrationHooks::InterwikiIntegrationArticleUndelete';
$wgHooks['TitleMoveComplete'][] = 'InterwikiIntegrationHooks::InterwikiIntegrationTitleMoveComplete';
$wgHooks['PureWikiDeletionArticleBlankComplete'][] = 'InterwikiIntegrationHooks::InterwikiIntegrationArticleBlankComplete';
$wgHooks['PureWikiDeletionArticleUnblankComplete'][] = 'InterwikiIntegrationHooks::InterwikiIntegrationArticleUnblankComplete';

$wgSpecialPages['PopulateInterwikiIntegrationTable'] = 'PopulateInterwikiIntegrationTable'; 
$wgSharedTables[] = 'integration_prefix';
$wgSharedTables[] = 'integration_namespace';
$wgSharedTables[] = 'integration_iwlinks';
$wgInterwikiIntegrationBrokenLinkStyle = "color: red";

$wgAvailableRights[] = 'integration';
$wgGroupPermissions['bureaucrat']['integration']    = true;