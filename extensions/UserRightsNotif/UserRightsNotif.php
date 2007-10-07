<?php

/**
 * Provide email notification of user rights changes
 *
 * @addtogroup Extensions
 *
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efUserRightsNotifierSetup';
	$wgExtensionCredits['other'][] = array(
		'name' => 'User Rights Email Notification',
		'url' => 'http://www.mediawiki.org/wiki/Extension:User_Rights_Email_Notification',
		'author' => 'Rob Church',
		'desc' => 'Sends email notification to users upon rights changes',
	);

	# Change this to alter the email sender
	$wgUserRightsNotif['sender'] = $wgPasswordSender;

	function efUserRightsNotifierSetup() {
		global $wgMessageCache, $wgHooks;
		$wgMessageCache->addMessage( 'userrightsnotifysubject', 'Group membership change on $1' );
		$wgMessageCache->addMessage( 'userrightsnotifybody', "Hello $1\n\nThis is to inform you that your group memberships on $2 were changed by $3 at $4.\n\nAdded: $5\nRemoved: $6\n\nWith regards,\n\n$2" );
		$wgHooks['UserRights'][] = 'efUserRightsNotifier';
	}

	function efUserRightsNotifier( &$user, $added, $removed ) {
		global $wgUserRightsNotif;
		if( $user->canReceiveEmail() ) {
			global $wgUser, $wgSitename, $wgContLang;
			$added = is_array( $added ) ? implode( ', ', $added ) : '';
			$removed = is_array( $removed ) ? implode( ', ', $removed ) : '';
			$subject = wfMsg( 'userrightsnotifysubject', $wgSitename );
			$message = wfMsg( 'userrightsnotifybody', $user->getName(), $wgSitename, $wgUser->getName(), $wgContLang->timeAndDate( wfTimestampNow() ), $added, $removed );
			$user->sendMail( $subject, $message, $wgUserRightsNotif['sender'] );
		}
		return true;	
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be executed standalone.\n" );
	die( 1 );
}