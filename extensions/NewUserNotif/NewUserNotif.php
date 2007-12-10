<?php

/**
 * Extension to provide customisable email notification of new user creation
 *
 * @author Rob Church <robchur@gmail.com>
 * @addtogroup Extensions
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0 or later
 */

if( defined( 'MEDIAWIKI' ) ) {

	$wgAutoloadClasses['NewUserNotifier'] = dirname( __FILE__ ) . '/NewUserNotif.class.php';
	$wgExtensionFunctions[] = 'efNewUserNotifSetup';
	$wgExtensionCredits['other'][] = array(
		'name' => 'New User Email Notification',
		'version'     => '1.1',
		'author' => 'Rob Church',
		'url' => 'http://www.mediawiki.org/wiki/Extension:New_User_Email_Notification',
		'description' => 'Sends email notification when user accounts are created',
	);

	/**
	 * Email address to use as the sender
	 */
	$wgNewUserNotifSender = $wgPasswordSender;

	/**
	 * Users who should receive notification mails
	 */
	$wgNewUserNotifTargets[] = 1;

	/**
	 * Additional email addresses to send mails to
	 */
	$wgNewUserNotifEmailTargets = array();

	/**
	 * Extension setup
	 */
	function efNewUserNotifSetup() {
		global $wgHooks, $wgMessageCache;
		$wgHooks['AddNewAccount'][] = 'efNewUserNotif';
		require_once( dirname( __FILE__ ) . '/NewUserNotif.i18n.php' );
		foreach( efNewUserNotifMessages() as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
	}

	/**
	 * Hook account creation
	 *
	 * @param User $user User account that was created
	 * @return bool
	 */
	function efNewUserNotif( $user ) {
		return NewUserNotifier::hook( $user );
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit( 1 );
}
