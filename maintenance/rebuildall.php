<?php
/**
 * Rebuild link tracking tables from scratch.  This takes several
 * hours, depending on the database size and server configuration.
 *
 * @file
 * @todo document
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class RebuildAll extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Rebuild links, text index and recent changes";
	}

	public function execute() {
		global $wgDBtype;
		// Rebuild the text index
		if ( $wgDBtype == 'mysql' ) {
			$this->output( "** Rebuilding fulltext search index (if you abort this will break searching; run this script again to fix):\n" );
			$rebuildText = $this->spawnChild( 'RebuildTextIndex', 'rebuildtextindex.php' );
			$rebuildText->execute();
		}

		// Rebuild RC
		$this->output( "\n\n** Rebuilding recentchanges table:\n" );
		$rebuildRC = $this->spawnChild( 'RebuildRecentchanges', 'rebuildrecentchanges.php' );
		$rebuildRC->execute();

		// Rebuild link tables
		$this->output( "\n\n** Rebuilding links tables -- this can take a long time. It should be safe to abort via ctrl+C if you get bored.\n" );
//		$rebuildLinks = $this->spawnChild( 'RefreshLinks', 'refreshLinks.php' );
//		$rebuildLinks->execute();
		
		$this->output( "Done.\n" );
	}
}

$maintClass = "RebuildAll";
require_once( DO_MAINTENANCE );
