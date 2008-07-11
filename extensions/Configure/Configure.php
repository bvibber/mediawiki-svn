<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Special page to allow users to configure the wiki by a web based interface
 * Require MediaWiki version 1.7.0 or greater
 *
 * @file
 * @ingroup Extensions
 * @author Alexandre Emsenhuber
 */

## Configuration part

/**
 * Default path for the serialized files
 */
$wgConfigureFilesPath = "$IP/serialized";

/**
 * Whether to update $wgCacheEpoch when saving changes in Special:Configure
 */
$wgConfigureUpdateCacheEpoch = false; 

/**
 * Styles versions, you shouldn't change it
 */
$wgConfigureStyleVersion = '5';

## Adding credit :)
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Configure',
	'author' => 'Alexandre Emsenhuber',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Configure',
	'description' => 'Allow authorised users to configure the wiki by a web-based interface',
	'descriptionmsg' => 'configure-desc',
	'version' => '0.5.4',
);

## Adding new rights...
$wgAvailableRights[] = 'configure';
$wgAvailableRights[] = 'configure-all';
$wgAvailableRights[] = 'viewconfig';
$wgAvailableRights[] = 'viewconfig-all';
$wgAvailableRights[] = 'extensions';
$wgAvailableRights[] = 'extensions-all';

## Rights for Special:Configure
$wgGroupPermissions['bureaucrat']['configure'] = true;
#$wgGroupPermissions['developer']['configure-all'] = true;

## Rights for Special:ViewConfig
$wgGroupPermissions['sysop']['viewconfig'] = true;
#$wgGroupPermissions['developer']['viewconfig-all'] = true;

## Rights for Special:Extensions
$wgGroupPermissions['bureaucrat']['extensions'] = true;
#$wgGroupPermissions['developer']['extensions-all'] = true;

$dir = dirname( __FILE__ ) . '/';

## Define some functions
require_once( $dir . 'Configure.func.php' );

## Adding internationalisation...
if( isset( $wgExtensionMessagesFiles ) && is_array( $wgExtensionMessagesFiles ) ){
	$wgExtensionMessagesFiles['Configure'] = $dir . 'Configure.i18n.php';
} else {
	$wgHooks['LoadAllMessages'][] = 'efConfigureLoadMessages';
}

## And special pages aliases...
if( isset( $wgExtensionAliasesFiles ) && is_array( $wgExtensionAliasesFiles ) ){
	$wgExtensionAliasesFiles['Configure'] = $dir . 'Configure.alias.php';
} else {
	# For 1.12 and 1.11
	$wgHooks['LanguageGetSpecialPageAliases'][] = 'efConfigureLoadAliases';
	# And for 1.10 and 1.9 :)
	$wgHooks['LangugeGetSpecialPageAliases'][] = 'efConfigureLoadAliases';
}

## Adding the new special pages...
## Common code
$wgAutoloadClasses['ConfigurationPage'] = $dir . 'Configure.page.php';
## Special:Configure
$wgAutoloadClasses['SpecialConfigure'] = $dir . 'SpecialConfigure.php';
$wgSpecialPages['Configure'] = 'SpecialConfigure';
## Special:ViewConfig
$wgAutoloadClasses['SpecialViewConfig'] = $dir . 'SpecialViewConfig.php';
$wgSpecialPages['ViewConfig'] = 'SpecialViewConfig';
## Special:Extensions
$wgAutoloadClasses['SpecialExtensions'] = $dir . 'SpecialExtensions.php';
$wgSpecialPages['Extensions'] = 'SpecialExtensions';

## Helper for Special:Extension
$wgAutoloadClasses['WebExtension'] = $dir . 'Configure.ext.php';

## Diff stuff
$wgAutoloadClasses['ConfigurationDiff'] = $dir . 'Configure.diff.php';
$wgAutoloadClasses['CorePreviewConfigurationDiff'] = $dir . 'Configure.diff.php';
$wgAutoloadClasses['ExtPreviewConfigurationDiff'] = $dir . 'Configure.diff.php';
$wgAutoloadClasses['HistoryConfigurationDiff'] = $dir . 'Configure.diff.php';

## Adding the ajax function
$wgAjaxExportList[] = 'efConfigureAjax';
