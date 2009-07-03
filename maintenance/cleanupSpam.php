<?php
/**
 * Cleanup all spam from a given hostname
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class CleanupSpam extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Cleanup all spam from a given hostname";
		$this->addOption( 'all', 'Check all wikis in $wgLocalDatabases' );
		$this->addArgs( array( 'hostname' ) );
	}

	public function execute() {
		global $wgLocalDatabases;

		$username = wfMsg( 'spambot_username' );
		$wgUser = User::newFromName( $username );
		// Create the user if necessary
		if ( !$wgUser->getId() ) {
			$wgUser->addToDatabase();
		}
		$spec = $this->getArg();
		$like = LinkFilter::makeLike( $spec );
		if ( !$like ) {
			$this->error( "Not a valid hostname specification: $spec\n", true );
		}

		$dbr = wfGetDB( DB_SLAVE );
	
		if ( $this->hasOption('all') ) {
			// Clean up spam on all wikis
			$dbr = wfGetDB( DB_SLAVE );
			$this->output( "Finding spam on " . count($wgLocalDatabases) . " wikis\n" );
			$found = false;
			foreach ( $wgLocalDatabases as $db ) {
				$count = $dbr->selectField( "`$db`.externallinks", 'COUNT(*)', 
					array( 'el_index LIKE ' . $dbr->addQuotes( $like ) ), __METHOD__ );
				if ( $count ) {
					$found = true;
					passthru( "php cleanupSpam.php $db $spec | sed s/^/$db:  /" );
				}
			}
			if ( $found ) {
				$this->output( "All done\n" );
			} else {
				$this->output( "None found\n" );
			}
		} else {
			// Clean up spam on this wiki
			$res = $dbr->select( 'externallinks', array( 'DISTINCT el_from' ), 
				array( 'el_index LIKE ' . $dbr->addQuotes( $like ) ), __METHOD__ );
			$count = $dbr->numRows( $res );
			$this->output( "Found $count articles containing $spec\n" );
			while ( $row = $dbr->fetchObject( $res ) ) {
				$this->cleanupArticle( $row->el_from, $spec );
			}
			if ( $count ) {
				$this->output( "Done\n" );
			}
		}
	}

	private function cleanupArticle( $id, $domain ) {
		$title = Title::newFromID( $id );
		if ( !$title ) {
			$this->error( "Internal error: no page for ID $id\n" );
			return;
		}
	
		$this->output( $title->getPrefixedDBkey() . " ..." );
		$rev = Revision::newFromTitle( $title );
		$revId = $rev->getId();
		$currentRevId = $revId;
	
		while ( $rev && LinkFilter::matchEntry( $rev->getText() , $domain ) ) {
			# Revision::getPrevious can't be used in this way before MW 1.6 (Revision.php 1.26)
			#$rev = $rev->getPrevious();
			$revId = $title->getPreviousRevisionID( $revId );
			if ( $revId ) {
				$rev = Revision::newFromTitle( $title, $revId );
			} else {
				$rev = false;
			}
		}
		if ( $revId == $currentRevId ) {
			// The regex didn't match the current article text
			// This happens e.g. when a link comes from a template rather than the page itself
			$this->output( "False match\n" );
		} else {
			$dbw = wfGetDB( DB_MASTER );
			$dbw->immediateBegin();
			if ( !$rev ) {
				// Didn't find a non-spammy revision, blank the page
				$this->output( "blanking\n" );
				$article = new Article( $title );
				$article->updateArticle( '', wfMsg( 'spam_blanking', $domain ),
					false, false );
	
			} else {
				// Revert to this revision
				$this->output( "reverting\n" );
				$article = new Article( $title );
				$article->updateArticle( $rev->getText(), wfMsg( 'spam_reverting', $domain ), false, false );
			}
			$dbw->immediateCommit();
			wfDoUpdates();
		}
	}
}

$maintClass = "CleanupSpam";
require_once( DO_MAINTENANCE );
