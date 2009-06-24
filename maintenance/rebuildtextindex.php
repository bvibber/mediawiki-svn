<?php
/**
 * Rebuild search index table from scratch.  This takes several
 * hours, depending on the database size and server configuration.
 *
 * This is only for MySQL (see bug 9905).
 * Postgres is trigger-based and should never need rebuilding.
 *
 * @file
 * @todo document
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class RebuildTextIndex extends Maintenance {

	const RTI_CHUNK_SIZE = 500;

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Rebuild search index table from scratch";
	}

	public function execute() {
		global $wgTitle;
		
		// Only do this for MySQL
		$database = wfGetDB( DB_MASTER );
		if( !$database instanceof DatabaseMysql ) {
			$this->error( "This script is only for MySQL.\n", true );
		}

		$wgTitle = Title::newFromText( "Rebuild text index script" );
	
		$this->dropTextIndex( $database );
		$this->rebuildTextIndex( $database );
		$this->createTextIndex( $database );
	
		$this->output( "Done.\n" );
	}
	
	private function dropTextIndex( &$database ) {
		$searchindex = $database->tableName( 'searchindex' );
		if ( $database->indexExists( "searchindex", "si_title" ) ) {
			$this->output( "Dropping index...\n" );
			$sql = "ALTER TABLE $searchindex DROP INDEX si_title, DROP INDEX si_text";
			$database->query($sql, "dropTextIndex" );
		}
	}

	private function createTextIndex( &$database ) {
		$searchindex = $database->tableName( 'searchindex' );
		$this->output( "\nRebuild the index...\n" );
		$sql = "ALTER TABLE $searchindex ADD FULLTEXT si_title (si_title), " .
		  "ADD FULLTEXT si_text (si_text)";
		$database->query($sql, "createTextIndex" );
	}
	
	private function rebuildTextIndex( &$database ) {
		list ($page, $revision, $text, $searchindex) = $database->tableNamesN( 'page', 'revision', 'text', 'searchindex' );

		$sql = "SELECT MAX(page_id) AS count FROM $page";
		$res = $database->query($sql, "rebuildTextIndex" );
		$s = $database->fetchObject($res);
		$count = $s->count;
		$this->output( "Rebuilding index fields for {$count} pages...\n" );
		$n = 0;
	
		while ( $n < $count ) {
			$this->output( $n . "\n" );
			$end = $n + self::RTI_CHUNK_SIZE - 1;
			$sql = "SELECT page_id, page_namespace, page_title, old_flags, old_text
					  FROM $page, $revision, $text
					 WHERE page_id BETWEEN $n AND $end
					   AND page_latest=rev_id
					   AND rev_text_id=old_id";
			$res = $database->query($sql, "rebuildTextIndex" );
	
			while( $s = $database->fetchObject($res) ) {
				$revtext = Revision::getRevisionText( $s );
				$u = new SearchUpdate( $s->page_id, $s->page_title, $revtext );
				$u->doUpdate();
			}
			$database->freeResult( $res );
			$n += self::RTI_CHUNK_SIZE;
		}
	}
}

$maintClass = "RebuildTextIndex";
require_once( DO_MAINTENANCE );
