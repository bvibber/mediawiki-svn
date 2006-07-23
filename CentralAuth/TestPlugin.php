<?php

/**
 * The sleep madness take you!
 */

/*

Ye olde milestones:

1) logging in on local DBs
2) new account creation on local DBs
3) migration-on-first-login of matching local accounts on local DBs
4) migration-on-first-login of non-matching local accounts on local DBs
5) renaming-on-first-login of non-matching local accounts on local DBs
6) provision for forced rename on local DBs
7) basic login for remote DBs
8) new account for remote DBs
9) migration for remote DBs
10) profit!

additional goodies:
11) secure login form
12) multiple-domain cookies to allow site-hopping



Ye olde tables:

CREATE TABLE globaluser (
  -- Internal unique ID for the authentication server
  gu_id int auto_increment,
  
  -- Username. [Could change... or not? How to best handle renames...]
  gu_name varchar(255) binary,
  
  -- Registered email address, may be empty.
  gu_email varchar(255) binary,
  
  -- Timestamp when the address was confirmed as belonging to the user.
  -- NULL if not confirmed.
  gu_email_authenticated char(14) binary,
  
  -- Salt and hashed password
  gu_salt char(16), -- or should this be an int? usually the old user_id
  gu_password char(32),
  
  -- If true, this account cannot be used to log in on any wiki.
  gu_locked bool not null default 0,
  
  -- If true, this account should be hidden from most public user lists.
  -- Used for "deleting" accounts without breaking referential integrity.
  gu_hidden bool not null default 0,
  
  -- Registration time
  gu_registration char(14) binary,
  
  primary key (gu_id),
  unique key (gu_name)
) CHARSET=latin1;

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
		$dbr = wfGetDB( DB_MASTER, 'centralauth' );
		$ok = $dbr->selectField(
			'globaluser',
			'1',
			array( 'gu_name' => $this->mName ),
			__METHOD__ );
		return (bool)$ok;
	}
	
	/**
	 * this code is crapper
	 */
	function register( $password ) {
		$dbw = wfGetDB( DB_MASTER, 'centralauth' );
		list( $salt, $hash ) = $this->saltedPassword( $password );
		$ok = $dbw->insert(
			'globaluser',
			array(
				'gu_name'  => $this->mName,
				
				'gu_email' => null, // FIXME
				'gu_email_authenticated' => null, // FIXME
				
				'gu_salt'     => $salt,
				'gu_password' => $hash,
				
				'gu_locked' => 0,
				'gu_hidden' => 0,
				
				'gu_registration' => $dbw->timestamp(),
			),
			__METHOD__ );
		return $ok;
	}
	
	/**
	 * Check if the current username is defined and attached on this wiki yet
	 * @param $dbname Local database key to look up
	 * @return ("attached", "unattached", "no local user")
	 */
	function isAttached( $dbname ) {
		$dbr = wfGetDB( DB_MASTER, 'centralauth' );
		$row = $dbr->selectRow( 'localuser',
			array( 'lu_attached' ),
			array( 'lu_name' => $this->mName, 'lu_database' => $dbname ),
			__METHOD__ );
		
		if( !$row ) {
			return "no local user";
		}
		
		if( $row->lu_attached ) {
			return "attached";
		} else {
			return "unattached";
		}
	}
	
	/**
	 * Add a local account record for the given wiki to the central database.
	 * @param 
	 */
	function addLocal( $dbname, $localid ) {
		$dbw = wfGetDB( DB_MASTER, 'centralauth' );
		$dbw->insert( 'localuser',
			array(
				'lu_dbname'   => $dbname,
				'lu_id'       => $localid,
				'lu_name'     => $this->mName,
				'lu_attached' => 1 ),
			__METHOD__ );
	}
	
	/**
	 * Declare the local account for a given wiki to be attached
	 * to the global account for the current username.
	 *
	 * @return true on success
	 */
	public function attach( $dbname ) {
		$dbw = wfGetDB( DB_MASTER, 'centralauth' );
		$dbw->update( 'localuser',
			array(
				// Boo-yah!
				'lu_attached' => 1,
				
				// Local information fields become obsolete
				'lu_email'               => NULL,
				'lu_email_authenticated' => NULL,
				'lu_salt'                => NULL,
				'lu_password'            => NULL ),
			array(
				'lu_dbname' => $dbname,
				'lu_name'   => $this->mName ),
			__METHOD__ );
		
		$rows = $dbw->affectedRows();
		if( $rows > 0 ) {
			return true;
		} else {
			wfDebug( __METHOD__ . " failed to attach \"{$this->mName}@$dbname\", not in localuser\n" );
			return false;
		}
	}
	
	/**
	 * Attempt to authenticate the global user account with the given password
	 * @param string $password
	 * @return ("ok", "no user", "locked", "bad password")
	 */
	public function authenticate( $password ) {
		$dbw = wfGetDB( DB_MASTER, 'centralauth' );
		$row = $dbw->selectRow( 'globaluser',
			array( 'gu_salt', 'gu_password', 'gu_locked' ),
			array( 'gu_name' => $this->mName ),
			__METHOD__ );
		
		if( !$row ) {
			return "no user";
		}
		
		$salt = $row->gu_salt;
		$crypt = $row->gu_password;
		$locked = $row->gu_locked;
		
		if( $locked ) {
			return "locked";
		}
		
		if( $this->matchHash( $password, $salt, $crypt ) ) {
			return "ok";
		} else {
			return "bad password";
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
		$dbr = wfGetDB( DB_MASTER, 'centralauth' );
		$res = $dbr->select(
			array( 'globaluser', 'localuser' ),
			array( 'lu_database' ),
			array(
				'lu_name' => $this->mName,
				'lu_attached' => 0 ) );
		$list = array();
		while( $row = $db->fetchObject( $res ) ) {
			$list[] = $row->lu_database;
		}
		$db->freeResult( $res );
		return $list;
	}
	
	function getEmail() {
		$dbr = wfGetDB( DB_MASTER, 'centralauth' );
		return $dbr->selectField( 'globaluser', 'gu_email',
			array( 'gu_name' => $this->mName ),
			__METHOD__ );
	}

	function saltedPassword( $password ) {
		$salt = mt_rand( 0, 1000000 );
		$hash = wfEncryptPassword( $salt, $password );
		return array( $salt, $hash );
	}
	
	/**
	 * Set the account's password
	 */
	function setPassword( $password ) {
		list( $salt, $hash ) = $this->saltedPassword( $password );
		
		$dbw = wfGetDB( DB_MASTER, 'centralauth' );
		$result = $dbr->update( 'globaluser',
			array(
				'gu_salt'     => $salt,
				'gu_password' => $hash,
			),
			array(
				'gu_name' => $this->mName,
			),
			__METHOD__ );
		
		$rows = $dbw->numRows( $result );
		$dbw->freeResult( $result );
		
		return $rows > 0;
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
		$user = new CentralAuthUser( $username );
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
		$user = new CentralAuthUser( $username );
		return $user->authenticate( $password ) == "ok";
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
		$global = CentralAuthUser( $user->getName() );
		return $global->setPassword( $password );
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
		$global = new CentralAuthUser( $user->getName() );
		return $global->register( $password );
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
		$global = new CentralAuthUser( $user->getName() );
		$user->setEmail( $global->getEmail() );
		// etc
	}
}

?>
