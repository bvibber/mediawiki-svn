<?php
define('INVITE_RESULT_OK', 0); // No problem
define('INVITE_RESULT_ALREADY_INVITED', 1); // The user has already been invited
define('INVITE_RESULT_NOT_ALLOWED', 2); // The inviter has not been invited
define('INVITE_RESULT_NONE_LEFT', 3); // The inviter has no invites left.
define('INVITE_RESULT_NO_SUCH_FEATURE', 4); // The feature has not been defined.
define('INVITE_RESULT_NONE_YET', 5); // The inviter has no invites yet.

class Invitations {

	/*
	 * Does this user have a valid invite to this feature?
	 * @param string $feature The feature to check.
	 * @param object $user The user to check, or null for the current user ($wgUser)
	 * @return boolean Whether or not the user has a valid invite to the feature.
	 */
	public static function hasInvite( $feature, $user = null ) {
		global $wgUser, $wgInvitationTypes;
		if ($user == null)
			$user = $wgUser;

		// No such invitation type.
		if (!is_array($wgInvitationTypes[$feature]))
			return false;

		$dbr = wfGetDb( DB_SLAVE );

		$res = $dbr->select( 'invitation', array( 1 ), array( inv_invitee => $user->getId(), inv_type => $feature ), __METHOD__ );

		return ($dbr->numRows($res) > 0);
	}

	/*
	 * Can the given inviter invite the given invitee to the given feature?
	 * @param string $feature The feature to check.
	 * @param object $invitee The user to be invited.
	 * @param object $inviter The inviting user, or null for $wgUser.
	 * @return integer One of the INVITE_RESULT constants.
	 */
	public static function checkInviteOperation( $feature, $invitee, $inviter = null ) {
		global $wgUser, $wgInvitationTypes;

		if (!is_array($wgInvitationTypes[$feature]))
			return INVITE_NO_SUCH_FEATURE;

		if ($inviter == null)
			$inviter = $wgUser;

		if (!Invitations::hasInvite($feature, $inviter))
			return INVITE_RESULT_NOT_ALLOWED;

		if (Invitations::hasInvite($feature, $invitee))
			return INVITE_RESULT_ALREADY_INVITED;

		if ($wgInvitationTypes[$feature][limitedinvites]) {
			if ($wgInvitationTypes[$feature][invitedelay] > 0) {
				// Is the account old enough to have invites?
				$accountAge = time() - wfTimestampOrNull( TS_UNIX, $inviter->mRegistration );
				if ($accountAge < $wgInvitationTypes[$feature][invitedelay]) {
					return INVITE_RESULT_NONE_YET;
				}
			}

			if (Invitations::getRemainingInvites( $feature, $inviter ) == 0) {
				return INVITE_RESULT_NONE_LEFT;
			}

		}

		return INVITE_RESULT_OK;
	}

	/*
	 * How many invites does the given inviter have?
	 * @param string $feature The feature to check.
	 * @param object $inviter The user to check, or null for $wgUser.
	 * @return integer The number of invites left, or -1 for infinity.
	 */
	private static function getRemainingInvites( $feature, $user = null ) {
		global $wgUser, $wgInvitationTypes;
		if ($user == null)
			$user = $wgUser;

		// No such invitation type.
		if (!is_array($wgInvitationTypes[$feature]))
			return 0;

		// Has none: not invited.
		if (!Invitations::hasInvite($feature, $inviter))
			return 0;

		if (!$wgInvitationTypes[$feature][limitedinvites])
			return -1;

		if ($wgInvitationTypes[$feature][invitedelay] > 0) {
			// Is the account old enough to have invites?
			$accountAge = time() - wfTimestampOrNull( TS_UNIX, $inviter->mRegistration );
			if ($accountAge < $wgInvitationTypes[$feature][invitedelay]) {
				return 0;
			}
		}

		$dbr = wfGetDb( DB_SLAVE );

		$res = $dbr->select( 'invite_count', array( 'ic_count' ), array( ic_user => $invitee->getId(), ic_type => $feature ), __METHOD__ );

		if ($dbr->numRows($res) > 0) {
			$num = $dbr->fetchObject($res)->ic_count;
			return $num;
		} else {
			Invitations::insertCountRow( $feature, $user );
			return $wgInvitationTypes[$feature][reserve];
		}
	}

	/*
	 * Insert a row into the invite_count table for the given user and feature.
	 * @param string $feature The feature to check.
	 * @param object $user The user to check, or null for $wgUser.
	 * @param object $count The number to insert, or NULL to insert the amount left normally.
	 * @return integer The number of invites left, or -1 for infinity.
	 */
	private static function insertCountRow( $feature, $user = null, $count = null ) {
		global $wgUser, $wgInvitationTypes;
		if ($user == null)
			$user = $wgUser;

		// No such invitation type.
		if (!is_array($wgInvitationTypes[$feature]))
			return false;

		if ($count === null)
			$count = Invitations::getRemainingInvites( $feature, $user );

		if ($count) {
			$dbw = wfGetDb( DB_MASTER );

			$dbw->replace( 'invite_count',
				array( 'ic_user' => $user->getId(), 'ic_type' => $feature, 'ic_count' => $count ),
				__METHOD__ );
		}
	}

	/*
	 * Add an invitation for the given invitee, from the given inviter.
	 * @param string $feature The feature to invite to.
	 * @param object $invitee The user to be invited.
	 * @param object $inviter The inviting user, or null for $wgUser.
	 * @return integer One of the INVITE_RESULT constants.
	 */
	public static function inviteUser( $feature, $invitee, $inviter = null ) {
		global $wgUser, $wgInvitationTypes;
		if ($user == null)
			$user = $wgUser;

		if ( ($res = Invitations::checkInviteOperation) != INVITE_RESULT_OK) {
			return $res;
		}

		// We /should/ be OK to go.
		$dbw = wfGetDB( DB_MASTER );

		$dbw->update( 'invite_count', array( 'ic_count=ic_count-1' ), 
				array( ic_user => $inviter->getId(), ic_type => $feature ), __METHOD__ );

		$dbw->insert( 'invitation',
		array( 'inv_invitee' => $invitee->getId(), 'inv_inviter' => $inviter->getId(),
			'inv_type' => $feature ), __METHOD__ );

		// Log it.
		$log = new LogPage( 'invite' );

		$log->addEntry( 'invite', $invitee->getUserName, '', array( $feature ) );

		Invitations::insertCountRow( $feature, $invitee );
	}
}
