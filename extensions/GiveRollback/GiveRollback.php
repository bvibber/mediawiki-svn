<?php

/**
 * Special page to allow local bureaucrats to give rollback permissions to
 * a non-sysop user
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0 or later
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionCredits['specialpage'][] = array( 'name' => 'Give Rollback', 'author' => 'Rob Church' );
	$wgAutoloadClasses['GiveRollback'] = dirname( __FILE__ ) . '/GiveRollback.page.php';
	$wgSpecialPages['Giverollback'] = 'GiveRollback';
	$wgAvailableRights[] = 'giverollback';

	require_once( 'GiveRollback.i18n.php' );
	$wgExtensionFunctions[] = 'efGiveRollback';
	
	/**
	 * Determines who can use the extension; as a default, bureaucrats are permitted
	 */
	$wgGroupPermissions['bureaucrat']['giverollback'] = true;
	
	/**
	 * User group with rollback capabilities
	 */
	$wgGroupPermissions['rollback']['rollback'] = true;
	
	/**
	 * Populate the message cache, set up the auditing and register the special page
	 */
	function efGiveRollback() {
		global $wgMessageCache, $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
		$wgMessageCache->addMessages( efGiveRollbackMessages() );
		$wgLogTypes[] = 'gvrollback';
		$wgLogNames['gvrollback'] = 'giverollback-logpage';
		$wgLogHeaders['gvrollback'] = 'giverollback-logpagetext';
		$wgLogActions['gvrollback/grant']  = 'giverollback-logentrygrant';
		$wgLogActions['gvrollback/revoke'] = 'giverollback-logentryrevoke';
	}
	
} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}
?>
