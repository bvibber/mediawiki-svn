<?php

/**
 * Class for manipulating the bad_image table
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence Copyright holder allows use of the code for any purpose
 */

class BadImageList {

	function check( $name ) {
		wfProfileIn( __METHOD__ );
		$dbr =& wfGetDB( DB_SLAVE ); # This might need to be DB_MASTER in future
		$res = $dbr->selectField( 'bad_images', 'COUNT(*)', array( 'bil_name' => $name ), __METHOD__ );
		wfProfileOut( __METHOD__ );
		return $res > 0;
	}

	function add( $name, $user, $reason ) {
		wfProfileIn( __METHOD__ );
		$dbw =& wfGetDB( DB_MASTER );
		$dbw->insert( 'bad_images', array( 'bil_name' => $name, 'bil_user' => $user, 'bil_timestamp' => wfTimestampNow(), 'bil_reason' => $reason ), __METHOD__, 'IGNORE' );
		wfProfileOut( __METHOD__ );
	}
	
	function remove( $name ) {
		wfProfileIn( __METHOD__ );
		$dbw =& wfGetDB( DB_MASTER );
		$dbw->delete( 'bad_images', array( 'bil_name' => $name ), __METHOD__ );
		wfProfileOut( __METHOD__ );
	}

}

?>