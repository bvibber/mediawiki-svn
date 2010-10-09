<?php

/**
 * Extension to provide customisable email notification of new user creation
 *
 * @file
 * @ingroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */

require_once( 'UserMailer.php' );

class NewUserNotifier {

	private $sender;
	private $user;

	/**
	 * Constructor
	 */
	public function NewUserNotifier() {
		global $wgNewUserNotifSender;
		$this->sender = $wgNewUserNotifSender;
	}

	/**
	 * Send all email notifications
	 *
	 * @param User $user User that was created
	 */
	public function execute( $user ) {
		$this->user = $user;
		wfLoadExtensionMessages( 'NewUserNotifier' );
		$this->sendExternalMails();
		$this->sendInternalMails();
	}

	/**
	 * Send email to external addresses
	 */
	private function sendExternalMails() {
		global $wgNewUserNotifEmailTargets, $wgNewUserNotifSenderParam, $wgNewUserNotifSenderSubjParam;
		foreach( $wgNewUserNotifEmailTargets as $target ) {
			userMailer(
				new MailAddress( $target ),
				new MailAddress( $this->sender ),
				$this->makeMessage( $target, $this->user, 'newusernotifsubj', $wgNewUserNotifSenderSubjParam),
				$this->makeMessage( $target, $this->user, 'newusernotifbody', $wgNewUserNotifSenderParam)
			);
		}
	}

	/**
	 * Send email to users
	 */
	private function sendInternalMails() {
		global $wgNewUserNotifTargets, $wgNewUserNotifSenderParam, $wgNewUserNotifSenderSubjParam;
		foreach( $wgNewUserNotifTargets as $userSpec ) {
			$user = $this->makeUser( $userSpec );
			if( $user instanceof User && $user->isEmailConfirmed() ) {
				$user->sendMail(
					$this->makeMessage( $user->getName(), $this->user, 'newusernotifsubj', $wgNewUserNotifSenderSubjParam ),
					$this->makeMessage( $user->getName(), $this->user, 'newusernotifbody', $wgNewUserNotifSenderParam ),
					$this->sender
				);
			}
		}
	}

	/**
	 * Initialise a user from an identifier or a username
	 *
	 * @param mixed $spec User identifier or name
	 * @return User
	 */
	private function makeUser( $spec ) {
		$name = is_integer( $spec ) ? User::whoIs( $spec ) : $spec;
		$user = User::newFromName( $name );
		if( $user instanceof User && $user->getId() > 0 )
			return $user;
		return null;
	}

	/**
	 * Build a notification email message (body and subject)
	 *
	 * @param string $recipient Name of the new user notification email recipient
	 * @param User $user User (object) created for new user
	 * @param string $msgId Localised Message Identifier
	 * @param string $parmArr Array of Strings eval'd to pass parameters to message	
	 * @return string
	 */
	private function makeMessage( $recipient, $user, $msgId, $parmArr) {
		global $wgSitename,$wgContLang;
		eval( "\$retval = wfMsgForContent('".$msgId."',".implode(",",$parmArr).");" );
		return ($retval);
	}

	/**
	 * Hook account creation
	 *
	 * @param User $user User that was created
	 * @return bool
	 */
	public static function hook( $user ) {
		$notifier = new self();
		$notifier->execute( $user );
		return true;
	}
}
