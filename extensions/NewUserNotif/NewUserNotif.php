<?php
if ( ! defined( 'MEDIAWIKI' ) )
    die();

/**
 * Extension to provide customisable email notification of new user creation
 *
 * @file
 * @author Rob Church <robchur@gmail.com>
 * @ingroup Extensions
 * @copyright © 2006 Rob Church
 * @license GNU General Public Licence 2.0 or later
 */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name'           => 'New User Email Notification',
	'version'        => '1.5.2',
	'author'         => 'Rob Church',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:New_User_Email_Notification',
	'descriptionmsg' => 'newusernotif-desc',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['NewUserNotifier'] = $dir . 'NewUserNotif.i18n.php';
$wgAutoloadClasses['NewUserNotifier'] = $dir . 'NewUserNotif.class.php';
$wgExtensionFunctions[] = 'efNewUserNotifSetup';

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
 *  These are the parameters that will be passed into MediaWiki:newusernotifbody
 *  Can use anthing available as part of $this, $user (created user object), $recipient (target),
 *		or from globals $wfContLang, $wgSitename
 */
$wgNewUserNotifSenderParam = array(
			'$recipient',										// $1 Recipient (of notification message)
			'$user->getname()',									// $2 User Name
			'$wgSitename',										// $3 Site Name
			'$wgContLang->timeAndDate( wfTimestampNow() )',		// $4 Time and date stamp
			'$wgContLang->date( wfTimestampNow() )',			// $5 Date Stamp
			'$wgContLang->time( wfTimestampNow() )',			// $6 Time Stamp
			);
/**
 *  These are the parameters that will be passed into MediaWiki:Newusernotifsubj (for use in the "subject:" line)
 *  parameters defs have same options as $wgNewUserNotifSenderParam
 */
$wgNewUserNotifSenderSubjParam = array(
			'$wgSitename',										// $1 Site Name
			);

/**
 * Extension setup
 */
function efNewUserNotifSetup() {
	global $wgHooks;
	$wgHooks['AddNewAccount'][] = 'efNewUserNotif';
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
