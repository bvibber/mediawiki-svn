<?php

/**
 * Delete archived (deleted from public) revisions from the database
 *
 * @file
 * @ingroup Maintenance
 * @author Aaron Schulz
 * Shamelessly stolen from deleteOldRevisions.php by Rob Church :)
 */

require_once( "Maintenance.php" );

class DeleteArchivedRevisions extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Deletes all archived revisions\nThese revisions will no longer be restorable";
		$this->addOption( 'delete', 'Performs the deletion' );
	}

	public function execute() {
		$this->output( "Delete archived revisions\n\n" );
		# Data should come off the master, wrapped in a transaction
		$dbw = wfGetDB( DB_MASTER );
		if( $this->hasOption('delete') ) {
			$dbw->begin();
	
			$tbl_arch = $dbw->tableName( 'archive' );
	
			# Delete as appropriate
			$this->output( "Deleting archived revisions... " );
			$dbw->query( "TRUNCATE TABLE $tbl_arch" );
	
			$count = $dbw->affectedRows();
			$deletedRows = $count != 0;
	
			$this->output( "done. $count revisions deleted.\n" );
	
			# This bit's done
			# Purge redundant text records
			$dbw->commit();
			if( $deletedRows ) {
				$this->purgeRedundantText( true );
			}
		} else {
			$res = $dbw->selectRow( 'archive', 'COUNT(*) as count', array(), __FUNCTION__ );
			$this->output( "Found {$res->count} revisions to delete.\n" );
			$this->output( "Please run the script again with the --delete option to really delete the revisions.\n" );
		}
	}
}

$maintClass = "DeleteArchivedRevisions";
require_once( DO_MAINTENANCE );
