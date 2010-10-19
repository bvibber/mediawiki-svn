<?php
/**
 * MwEmbed extension, supports mwEmbed based modules
 * 
 * @file
 * @ingroup Extensions
 * 
 * @author Michael Dale ( michael.dale@kaltura.com )
 * @license GPL v2 or later
 * @version 0.3.0
 */   

/* Configuration */

/* Setup */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'MwEmbed',
	'author' => array( 'Michael Dale' ),
	'version' => '0.0.1',
	'url' => 'http://www.mediawiki.org/wiki/Extension:MwEmbed',
	'descriptionmsg' => 'mwembed-desc',
);

$wgAutoloadClasses['MwEmbedResourceManager'] = dirname( __FILE__ ) . '/MwEmbedResourceManager.php';
$wgExtensionMessagesFiles['MwEmbed'] = dirname( __FILE__ ) . '/MwEmbed.i18n.php';

// Register the core mwEmbed Module:
MwEmbedResourceManager::registerModulePath( 'extensions/MwEmbed/MwEmbed' );

$wgHooks['ResourceLoaderRegisterModules'][] = 'MwEmbedResourceManager::registerModules';

// The mwEmbed module is added to all pages if enabled: 
$wgHooks['BeforePageDisplay'][] = 'MwEmbedResourceManager::addMwEmbedModule';