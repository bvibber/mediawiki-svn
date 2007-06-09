<?php

/**
 * Functions for managing "new messages" status
 *
 * @addtogroup User
 * @author Rob Church <robchur@gmail.com>
 */
class NewTalk {

	/**
	 * Status flag constants
	 */
	const FLAG_SET = 'set';
	const FLAG_UNSET = 'unset';
	
	/**
	 * Is the "new messages" flag set for a given user?
	 *
	 * @param User $user User to check status for
	 * @return bool
	 */
	public static function get( User $user ) {
		global $wgMemc;
		wfProfileIn( __METHOD__ );
		
		# Attempt to read from object cache
		$k = self::getCacheKey( $user );
		if( ( $c = $wgMemc->get( $k ) ) !== false ) {
			wfDebugLog( 'newtalk', 'Got `newtalk` flag for user `' . $user->getName() . '` from cache' );
			wfProfileOut( __METHOD__ );
			return $c == self::FLAG_SET;
		}
		
		# Fall back to database
		list( $field, $value ) = self::getLookupConds( $user );
		$set = self::getFromDB( $field, $value );
		
		# Stash the value back in the object cache
		$c = $set ? self::FLAG_SET : self::FLAG_UNSET;
		$wgMemc->set( $k, $c, time() + 1800 );
		
		# We're done
		wfDebugLog( 'newtalk', 'Got `newtalk` flag for user `' . $user->getName() . '` from database' );
		wfProfileOut( __METHOD__ );
		return $set;
	}
	
	/**
	 * Set the "new messages" flag for a given user
	 *
	 * @param User $user User to update status for
	 */
	public static function set( User $user ) {
		global $wgMemc;		
		wfProfileIn( __METHOD__ );
		
		# Add to database
		$dbw = wfGetDB( DB_MASTER );
		list( $field, $value ) = self::getLookupConds( $user );
		$dbw->insert( 'user_newtalk', array( $field => $value ), __METHOD__, 'IGNORE' );
		
		# Update object cache
		$wgMemc->set( self::getCacheKey( $user ), self::FLAG_SET, time() + 1800 );
		
		# We're done
		wfDebugLog( 'newtalk', 'Set `newtalk` flag for user `' . $user->getName() . '`' );
		wfProfileOut( __METHOD__ );				
	}
	
	/**
	 * Unset the "new messages" flag for a given user
	 *
	 * @param User $user User to update status for
	 */
	public static function remove( User $user ) {
		global $wgMemc;		
		wfProfileIn( __METHOD__ );
		
		# Remove from database
		$dbw = wfGetDB( DB_MASTER );
		list( $field, $value ) = self::getLookupConds( $user );
		$dbw->delete( 'user_newtalk', array( $field => $value ), __METHOD__ );
		
		# Update object cache
		$wgMemc->set( self::getCacheKey( $user ), self::FLAG_UNSET, time() + 1800 );
		
		# We're done
		wfDebugLog( 'newtalk', 'Removed `newtalk` flag for user `' . $user->getName() . '`' );
		wfProfileOut( __METHOD__ );				
	}
	
	/**
	 * Read "new messages" status from the database
	 *
	 * @param string $field Table field to check
	 * @param string $value Field value to check
	 * @return bool
	 */
	private static function getFromDB( $field, $value ) {
		wfProfileIn( __METHOD__ );
		$dbr = wfGetDB( DB_SLAVE );
		$row = $dbr->selectRow( 'user_newtalk', '*', array( $field => $value ), __METHOD__ );
		wfProfileOut( __METHOD__ );
		return $row !== false;
	}
	
	/**
	 * Build the appropriate "new messages" object cache
	 * key for a given user
	 *
	 * @param User $user User to build key for
	 * @return string
	 */
	private static function getCacheKey( User $user ) {
		return $user->isLoggedIn()
			? wfMemcKey( 'newtalk', 'user', $user->getId() )
			: wfMemcKey( 'newtalk', 'ip', $user->getName() );	
	}
	
	/**
	 * Build the appropriate `user_newtalk` column/value pair
	 * to check for a given user
	 *
	 * @param User $user User to build pair for
	 * @return array
	 */
	private static function getLookupConds( User $user ) {
		return $user->isLoggedIn()
			? array( 'user_id', $user->getId() )
			: array( 'user_ip', $user->getName() );
	}

}

?>
