<?php

/**
 * Special page to allow local bureaucrats to grant/revoke the bot flag
 * for a particular user
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0 or later
 */

if( defined( 'MEDIAWIKI' ) ) {

	define( 'MW_MAKEBOT_GRANT', 1 );
	define( 'MW_MAKEBOT_REVOKE', 2 );

	$wgExtensionFunctions[] = 'efMakeBot';
	$wgAvailableRights[] = 'makebot';
	$wgExtensionCredits['specialpage'][] = array(
		'name' => 'MakeBot',
		'version' => '1.1',
		'author' => 'Rob Church',
		'url' => 'http://www.mediawiki.org/wiki/Extension:MakeBot',
		'description' => 'Special page allows local bureaucrats to grant and revoke bot permissions',
	);

	/**
	 * Determines who can use the extension; as a default, bureaucrats are permitted
	 */
	$wgGroupPermissions['bureaucrat']['makebot'] = true;

	/**
	 * Toggles whether or not a bot flag can be given to a user who is also a sysop or bureaucrat
	 */
	$wgMakeBotPrivileged = false;

	/**
	 * Register the special page
	 */
	$wgAutoloadClasses['Makebot'] = dirname( __FILE__ ) . '/Makebot.class.php';
	$wgSpecialPages['Makebot'] = 'Makebot';

	/**
	 * Populate the message cache and set up the auditing
	 */
	function efMakeBot() {
		global $wgMessageCache, $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
		require_once( dirname( __FILE__ ) . '/Makebot.i18n.php' );
		foreach( efMakeBotMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
		$wgLogTypes[] = 'makebot';
		$wgLogNames['makebot'] = 'makebot-logpage';
		$wgLogHeaders['makebot'] = 'makebot-logpagetext';
		$wgLogActions['makebot/grant']  = 'makebot-logentrygrant';
		$wgLogActions['makebot/revoke'] = 'makebot-logentryrevoke';
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit( 1 );
}
