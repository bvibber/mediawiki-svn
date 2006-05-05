<?php

/**
 * The sleep madness take you!
 */

$wgCentralAuthDatabase = 'authtest';

class CentralAuthUser {
	function __construct( $username ) {
		$this->mName = $username;
	}
	
	/**
	 * this code is crap
	 */
	function exists() {
		$ok = $this->db->selectField(
			'globaluser',
			'1',
			array( 'gu_name' => $this->mName ),
			__CLASS__ . '::' . __FUNCTION__ );
		return (bool)$ok;
	}
	
	/**
	 * @param string $password
	 * @return bool
	 */
	public function authenticate( $password ) {
		global $wgDBname;
		$fname = __CLASS__ . '::' . __FUNCTION__;
		
		// First check that this wiki is attached
		$res = $this->db->safeQuery(
			"SELECT gu_id, gu_salt, gu_password, lu_database
			FROM authtest.globaluser
			LEFT JOIN authtest.localuser
			ON gu_name=lu_name AND lu_dbname=?
			WHERE gu_name=?",
			$wgDBname,
			$this->mName );
		$row = $this->db->fetchObject( $res );
		$this->db->freeResult( $res );
		
		if( !$row ) {
			// this shouldn't happen :)
			throw new ImpossibleError( 'Global account vanished while we were looking at it' );
		}
		
		$isAttached = !is_null( $row->lu_database );
		$salt = $row->gu_salt;
		$crypt = $row->gu_password;
		
		if( $isAttached ) {
			return $this->matchHash( $password, $salt, $crypt );
		} elseif( $wgCentralAuthAllowUnattachedLogin ) {
			throw new UnimplementedError( 'unattached login not implemented' );
		} else {
			throw new UnimplementedError( 'login-time conflict resolver not implemented' );
		}
	}
	
	/**
	 * @param $plaintext  User-provided password plaintext.
	 * @param $salt       The hash "salt", eg a local id for migrated passwords.
	 * @param $encrypted  Fully salted and hashed database crypto text from db.
	 * @return bool true on match.
	 */
	private function matchHash( $plaintext, $salt, $encrypted ) {
		return md5( $salt . "-" . md5( $plaintext ) ) === $encrypted;
	}
	
	/**
	 * Fetch a list of databases where this account name is registered,
	 * but not yet attached to the global account. It would be used for
	 * an alert or management system to show which accounts have still
	 * to be dealt with.
	 *
	 * @return array of database name strings
	 */
	function listUnattached() {
		$res = $this->db->select(
			array( 'globaluser', 'localuser' ),
			array( 'lu_database' ),
			array(
				'lu_name' => $this->mName,
				'lu_attached' => 0 ) );
		$list = array();
		while( $row = $this->db->fetchObject( $res ) ) {
			$list[] = $row->lu_database;
		}
		$this->db->freeResult( $res );
		return $list;
	}
}

/**
 * Quickie test implementation using local test database
 */
class CentralAuth extends AuthPlugin {
	/**
	 * Check whether there exists a user account with the given name.
	 * The name will be normalized to MediaWiki's requirements, so
	 * you might need to munge it (for instance, for lowercase initial
	 * letters).
	 *
	 * @param $username String: username.
	 * @return bool
	 * @public
	 */
	function userExists( $username ) {
		$user = CentralAuthUser( $username );
		return $user->exists();
	}

	/**
	 * Check if a username+password pair is a valid login.
	 * The name will be normalized to MediaWiki's requirements, so
	 * you might need to munge it (for instance, for lowercase initial
	 * letters).
	 *
	 * @param $username String: username.
	 * @param $password String: user password.
	 * @return bool
	 * @public
	 */
	function authenticate( $username, $password ) {
		$user = CentralAuthUser( $username );
		return $user->authenticate( $password );
	}

	/**
	 * When a user logs in, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * @param User $user
	 * @public
	 */
	function updateUser( &$user ) {
		# Override this and do something
		return true;
	}


	/**
	 * Return true if the wiki should create a new local account automatically
	 * when asked to login a user who doesn't exist locally but does in the
	 * external auth database.
	 *
	 * If you don't automatically create accounts, you must still create
	 * accounts in some way. It's not possible to authenticate without
	 * a local account.
	 *
	 * This is just a question, and shouldn't perform any actions.
	 *
	 * @return bool
	 * @public
	 */
	function autoCreate() {
		return true;
	}

	/**
	 * Set the given password in the authentication database.
	 * Return true if successful.
	 *
	 * @param $password String: password.
	 * @return bool
	 * @public
	 */
	function setPassword( $password ) {
		// Fixme: password changes should happen through central interface.
		return false;
	}

	/**
	 * Update user information in the external authentication database.
	 * Return true if successful.
	 *
	 * @param $user User object.
	 * @return bool
	 * @public
	 */
	function updateExternalDB( $user ) {
		return true;
	}

	/**
	 * Check to see if external accounts can be created.
	 * Return true if external accounts can be created.
	 * @return bool
	 * @public
	 */
	function canCreateAccounts() {
		// Require accounts to be created through the central login interface?
		return true;
	}

	/**
	 * Add a user to the external authentication database.
	 * Return true if successful.
	 *
	 * @param User $user
	 * @param string $password
	 * @return bool
	 * @public
	 */
	function addUser( $user, $password ) {
		throw new UnimplementedError( 'Trying to add a new account' );
	}


	/**
	 * Return true to prevent logins that don't authenticate here from being
	 * checked against the local database's password fields.
	 *
	 * This is just a question, and shouldn't perform any actions.
	 *
	 * @return bool
	 * @public
	 */
	function strict() {
		return true;
	}

	/**
	 * When creating a user account, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * @param $user User object.
	 * @public
	 */
	function initUser( &$user ) {
		# Override this to do something.
		$user->setEmail( $global->getEmail() );
		// etc
	}
}

?>
