<?php

/**
 * Extension to provide customisable email notification of new user creation
 *
 * @author Rob Church <robchur@gmail.com>
 * @package MediaWiki
 * @subpackage Extensions
 * @copyright © 2006 Rob Church
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0
 */

if( defined( 'MEDIAWIKI' ) ) {

	require_once( 'UserMailer.php' );
	
	$wgExtensionFunctions[]  = 'NewUserNotif_Init';
	$wgExtensionCredits['other'][] = array( 'name' => 'New User Email Notification', 'author' => 'Rob Church', 'url' => 'http://meta.wikimedia.org/wiki/New_User_Email_Notification' );
	
	$wgNewUserNotifSender    = $wgPasswordSender;
	$wgNewUserNotifTargets[] = 1; # TODO: Support usernames instead of/in addition to IDs
	$wgNewUserNotifEmailTargets = array();
	
	/** Initialise the extension */
	function NewUserNotif_Init() {
		global $wgHooks, $wgMessageCache;
		$wgHooks['AddNewAccount'][] = 'NewUserNotif_Hook';
		$wgMessageCache->addMessage( 'newusernotifsubj', 'New User Notification for $1' );
		$wgMessageCache->addMessage( 'newusernotifbody', "Hello $1,\n\nA new user account, $2, has been created on $3 at $4." );
	}
	
	/** Send the notifications where possible */
	function NewUserNotif_Hook( $user = NULL ) {
		global $wgUser, $wgSitename, $wgNewUserNotifSender, $wgNewUserNotifTargets;
		
		# Some backwards-compatible fiddling
		if( is_null( $user ) )
			$user =& $wgUser;
		
		# Do external emails first
		NewUserNotif_EmailExternal( $user );
		
		foreach( $wgNewUserNotifTargets as $target ) {
			$recipient = new User();
			$recipient->setId( $target );
			$recipient->loadFromDatabase();
	
			# TODO: The target might not exist
			if( $recipient->isEmailConfirmed() ) {
				$subject = wfMsg( 'newusernotifsubj', $wgSitename );
				$message = NewUserNotif_MakeEmail( $user, $recipient->getName() );
				$recipient->sendMail( $subject, $message, $wgNewUserNotifSender );
			}
		}
	}
	
	/** Send a notification email to the external addresses */
	function NewUserNotif_EmailExternal( &$user ) {
		global $wgSitename, $wgNewUserNotifEmailTargets;
		$sender = new MailAddress( $wgNewUserNotifSender, $wgSitename );
		
		foreach( $wgNewUserNotifEmailTargets as $target ) {
			$recipient = new MailAddress( $target );
			$subject   = wfMsg( 'newusernotifsubj', $wgSitename );
			$message   = NewUserNotif_MakeEmail( $user, $target );
			userMailer( $recipient, $sender, $subject, $message );
		}
	}
	
	/** Make the notification email */
	function NewUserNotif_MakeEmail( &$user, $recipient ) {
		global $wgContLang, $wgSitename;
		$timestamp = $wgContLang->timeAndDate( wfTimestampNow() );
		$message   = wfMsg( 'newusernotifbody', $recipient, $user->getName(), $wgSitename, $timestamp );
		return( $message );
	}

} else {
	die( 'This file is an extension to the MediaWiki package, and cannot be executed separately.' );
}

?>