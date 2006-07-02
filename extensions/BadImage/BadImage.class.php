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
		$dbr =& wfGetDB( DB_SLAVE ); # This might need to be DB_MASTER in future
		$res = $dbr->selectField( 'bad_images', 'COUNT(*) AS count', array( 'bil_name' => $name ), __METHOD__ );
		return $res > 0;
	}

	function add( &$image, &$user, $reason ) {
		$dbw =& wfGetDB( DB_MASTER );
		$dbw->insert( 'bad_images', array( 'bil_name' => $image->getName(), 'bil_user' => $user->getId(), 'bil_reason' => $reason ), __METHOD__, 'IGNORE' );
	}
	
	function remove( &$image ) {
		$dbw =& wfGetDB( DB_MASTER );
		$dbw->delete( 'bad_images', array( 'bil_name' => $image->getName() ), __METHOD__ );
	}

}

?>