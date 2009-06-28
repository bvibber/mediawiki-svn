<?php
/**
 * Fix the user_registration field.
 * In particular, for values which are NULL, set them to the date of the first edit
 *
 * @file
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class FixUserRegistration extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Fix the user_registration field";
	}

	public function execute() {
		$dbr = wfGetDB( DB_SLAVE );
		$dbw = wfGetDB( DB_MASTER );

		// Get user IDs which need fixing
		$res = $dbr->select( 'user', 'user_id', 'user_registration IS NULL', __METHOD__ );
		while ( $row = $dbr->fetchObject( $res ) ) {
			$id = $row->user_id;
			// Get first edit time
			$timestamp = $dbr->selectField( 'revision', 'MIN(rev_timestamp)', array( 'rev_user' => $id ), __METHOD__ );
			// Update
			if ( !empty( $timestamp ) ) {
				$dbw->update( 'user', array( 'user_registration' => $timestamp ), array( 'user_id' => $id ), __METHOD__ );
				$this->output( "$id $timestamp\n" );
			} else {
				$this->output( "$id NULL\n" );
			}
		}
		$this->output( "\n" );
	}
}

$maintClass = "FixUserRegistration";
require_once( DO_MAINTENANCE );
