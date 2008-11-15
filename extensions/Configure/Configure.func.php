<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Functions for Configure extension
 *
 * @file
 * @author Alexandre Emsenhuber
 * @license GPLv2 or higher
 * @ingroup Extensions
 */

/**
 * Ajax function to create checkboxes for a new group in $wgGroupPermissions
 *
 * @param $group String: new group name
 * @return either <err#> if group already exist or html fragment
 */
function efConfigureAjax( $group ){
	global $wgUser, $wgGroupPermissions;
	if( !$wgUser->isAllowed( 'configure-all' ) ){
		return '<err#>';
	}
	if( isset( $wgGroupPermissions[$group] ) ){
		$html = '<err#>';
	} else {
		$all = User::getAllRights();
		$row = '<div style="-moz-column-count:2"><ul>';
		foreach( $all as $right ){
			$id = Sanitizer::escapeId( 'wpwgGroupPermissions-'.$group.'-'.$right );
			$desc = ( is_callable( array( 'User', 'getRightDescription' ) ) ) ?
				User::getRightDescription( $right ) :
				$right;
			$row .= '<li>'.Xml::checkLabel( $desc, $id, $id ) . "</li>\n";
		}
		$row .= '</ul></div>';
		$groupName = User::getGroupName( $group );
		// Firefox seems to not like that :(
		$html = str_replace( '&nbsp;', ' ', $row );
	}
	return $html;
}

/**
 * Initalize the settings stored in a serialized file.
 * This have to be done before the end of LocalSettings.php but is in a function
 * because administrators might configure some settings between the moment where
 * the file is loaded and the execution of these function.
 * Settings are not filled only if they doesn't exists because of a security
 * hole if the register_globals feature of PHP is enabled.
 *
 * @param $wiki String
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
	if( isset( $oldConf->siteParamsCallback ) ) # 1.14+
		$wgConf->siteParamsCallback = $oldConf->siteParamsCallback;

	# Load the new configuration, and fill in the settings
	$wgConf->initialise();
	$wgConf->extract();
}

/**
 * Declare the API module only if $wgConfigureAPI is true
 */
function efConfigureSetupAPI() {
	global $wgConfigureAPI, $wgAPIModules;
	if( $wgConfigureAPI === true ) {
		$wgAPIModules['configure'] = 'ApiConfigure';
	}
}

/**
 * Add custom rights defined in $wgRestrictionLevels
 */
function efConfigureGetAllRights( &$rights ){
	global $wgRestrictionLevels;
	$newrights = array_diff( $wgRestrictionLevels, array( '', 'sysop' ) ); // Pseudo rights
	$rights = array_unique( array_merge( $rights, $newrights ) );
	return true;
}
