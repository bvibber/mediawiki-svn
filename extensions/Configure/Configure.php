<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Special page to allow users to configure the wiki by a web based interface
 * Require MediaWiki version 1.7.0 or greater
 *
 * @addtogroup Extensions
 * @author Alexandre Emsenhuber
 */

## Adding credit :)
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Configure',
	'author' => 'Alexandre Emsenhuber',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Configure',
	'description' => 'Allow authorised users to configure the wiki by a web-based interface',
	'descriptionmsg' => 'configure-desc',
	'version' => '0.1',
);

## Adding new rights...
$wgAvailableRights[] = 'configure';
$wgAvailableRights[] = 'configure-all';
$wgGroupPermissions['bureaucrat']['configure'] = true;
#$wgGroupPermissions['developer']['configure-all'] = true;

$dir = dirname( __FILE__ ) . '/';

# Adding internationalisation...
if( isset( $wgExtensionMessagesFiles ) && is_array( $wgExtensionMessagesFiles ) ){
	$wgExtensionMessagesFiles['Configure'] = $dir . 'Configure.i18n.php';
} else {
	$wgHooks['LoadAllMessages'][] = 'efConfigureLoadMessages';
}

## Adding the new special page...
$wgAutoloadClasses['SpecialConfigure'] = $dir . 'Configure.body.php';
$wgSpecialPages['Configure'] = 'SpecialConfigure';

## Default path for the serialized files
$wgConfigureFilesPath = "$IP/serialized";

/**
 * Initalize the settings stored in a serialized file.
 * This have to be done before the end of LocalSettings.php but is in a function
 * because administrators might configure some settings between the moment where
 * the file is loaded and the execution of these function.
 * Settings are not filled only if they doesn't exists because of a security
 * hole if the register_globals feature of PHP is enabled.
 *
 * @param String $wiki
 */
function efConfigureSetup( $wiki = 'default' ){
	global $wgConf, $wgConfigureFilesPath;

	# Create the new configuration object...
	$oldConf = $wgConf;
	require_once( dirname( __FILE__ ) . '/Configure.obj.php' );
	$wgConf = new WebConfiguration( $wiki, $wgConfigureFilesPath );
	
	# Copy the existing settings...
	$wgConf->suffixes = $oldConf->suffixes;
	$wgConf->wikis = $oldConf->wikis;
	$wgConf->settings = $oldConf->settings;
	$wgConf->localVHosts = $oldConf->localVHosts;
	
	# Load the new configuration, and fill in the settings
	$wgConf->initialise();
	$wgConf->extract();
}

/**
 * Function that loads the messages in $wgMessageCache, it is used for backward
 * compatibility with 1.10 and older versions
 */
function efConfigureLoadMessages(){
	if( function_exists( 'wfLoadExtensionMessages' ) ){
		wfLoadExtensionMessages( 'Configure' );
	} else {
		global $wgMessageCache;
		require( dirname( __FILE__ ) . '/Configure.i18n.php' );
		foreach( $messages as $lang => $messages ){
			$wgMessageCache->addMessages( $messages, $lang );
		}
	}
}