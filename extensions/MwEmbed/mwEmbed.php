<?php
/**
 * MwEmbed extension, supports mwEmbed based extensions
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

$wgAutoloadClasses['MwEmbedHooks'] = dirname( __FILE__ ) . '/MwEmbed.hooks.php';
$wgExtensionMessagesFiles['MwEmbed'] = dirname( __FILE__ ) . '/MwEmbed.i18n.php';

$wgHooks['ResourceLoaderRegisterModules'][] = 'MwEmbedHooks::resourceLoaderRegisterModules';
