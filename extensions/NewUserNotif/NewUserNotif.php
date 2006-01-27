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

	$wgExtensionFunctions[]  = 'NewUserNotif_Init';
	$wgExtensionCredits['other'][] = array(
		'name' => 'New user notification',
		'description' => 'provides customisable email notification of new user creation',
		'author' => 'Rob Church'
		);
	
	$wgNewUserNotifSender    = $wgPasswordSender;
	$wgNewUserNotifTargets[] = 1; # TODO: Support usernames instead of/in addition to IDs
	
	/** Initialise the extension */
	function NewUserNotif_Init() {
		global $wgHooks, $wgMessageCache;
		$wgHooks['AddNewAccount'][] = 'NewUserNotif_Hook';
		$wgMessageCache->addMessage( 'newusernotifsubj', 'New User Notification for $1' );
		$wgMessageCache->addMessage( 'newusernotifbody', "Hello $1,\n\nA new user account, $2, has been created on $3 at $4." );
	}
	
	/** Send the notifications where possible */
	function NewUserNotif_Hook() {
		global $wgUser, $wgContLang, $wgSitename, $wgNewUserNotifSender, $wgNewUserNotifTargets;
		$timestamp = $wgContLang->timeAndDate( date( 'YmdHis' ), false, false ) . ' (' . date( 'T' ) . ')';
		foreach( $wgNewUserNotifTargets as $target ) {
			$recipient = new User();
			$recipient->setId( $target );
			$recipient->loadFromDatabase();
	
			# TODO: The target might not exist
			if( $recipient->isEmailConfirmed() ) {
				$subject = wfMsg( 'newusernotifsubj', $wgSitename );
				$message = wfMsg( 'newusernotifbody', $recipient->getName(), $wgUser->getName(), $wgSitename, $timestamp );
				if( $err = $recipient->sendMail( $subject, $message, $wgNewUserNotifSender ) !== true ) {
					wfDebug( "Couldn't send account creation notification for " . $wgUser->getName() . " to " . $recipient->getName() . "; " . $err->getMessage() . "\n" ); 
				}
			} else {
				wfDebug( "Couldn't send account creation notification for " . $wgUser->getName() . " to " . $recipient->getName() . "; user can't receive email.\n" );
			}
		}
	}

} else {
	die( 'This file is an extension to the MediaWiki package, and cannot be executed separately.' );
}

?>