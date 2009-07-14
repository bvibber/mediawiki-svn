<?php

/**
 * Delete archived (non-current) files from the database
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @ingroup Maintenance
 * @author Aaron Schulz
 * Based on deleteOldRevisions.php by Rob Church
 */

require_once( "Maintenance.php" );

class DeleteArchivedFiles extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Deletes all archived images.";
		$this->addOption( 'delete', 'Perform the deletion' );
	}

	/**
	 * @todo @fixme FSTransaction/FileStore crap needs removing. Does
	 * not work on trunk
	 */
	public function execute() {
		$this->output( "Delete Archived Images\n\n" );

		# Data should come off the master, wrapped in a transaction
		$dbw = wfGetDB( DB_MASTER );
		$transaction = new FSTransaction();
		if( !$dbw->lock() ) {
			wfDebug( __METHOD__ . ": failed to acquire DB lock, aborting\n" );
			return false;
		}

		$tbl_arch = $dbw->tableName( 'filearchive' );

		# Get "active" revisions from the filearchive table
		$this->output( "Searching for and deleting archived files...\n" );
		$res = $dbw->query( "SELECT fa_id,fa_storage_group,fa_storage_key FROM $tbl_arch" );
		while( $row = $dbw->fetchObject( $res ) ) {
			$key = $row->fa_storage_key;
			$group = $row->fa_storage_group;
			$id = $row->fa_id;
	
			$store = FileStore::get( $group );
			if( $store ) {
				$path = $store->filePath( $key );
				$sha1 = substr( $key, 0, strcspn( $key, '.' ) );
				$inuse = $dbw->selectField( 'oldimage', '1',
					array( 'oi_sha1' => $sha1,
						'oi_deleted & '.File::DELETED_FILE => File::DELETED_FILE ),
					__METHOD__, array( 'FOR UPDATE' ) );
				if ( $path && file_exists($path) && !$inuse ) {
					$transaction->addCommit( FSTransaction::DELETE_FILE, $path );
					$dbw->query( "DELETE FROM $tbl_arch WHERE fa_id = $id" );
				} else {
					$this->output( "Notice - file '$key' not found in group '$group'\n" );
				}
			} else {
				$this->output( "Notice - invalid file storage group '$group' for file '$key'\n" );
			}
		}
		$this->output( "done.\n" );
	
		$transaction->commit();
	}
}

$maintClass = "DeleteArchivedFiles";
require_once( DO_MAINTENANCE );
