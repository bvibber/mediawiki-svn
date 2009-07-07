<?php
/**
 * Why yes, this *is* another special-purpose Wikimedia maintenance script!
 * Should be fixed up and generalized.
 *
 * @file
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class RenameWiki extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Rename external storage dbs and leave a new one";
		$this->addArgs( array( 'olddb', 'newdb' ) );
	}

	public function execute() {
		global $wgDefaultExternalStore;

		# Setup
		$from = $this->getArg( 0 );
		$to = $this->getArg( 1 );
		$this->output( "Renaming blob tables in ES from $from to $to...\n" );
		$this->output( "Sleeping 5 seconds...\n" );
		sleep(5);

		# Initialise external storage
		if ( is_array( $wgDefaultExternalStore ) ) {
			$stores = $wgDefaultExternalStore;
		} elseif ( $wgDefaultExternalStore ) {
			$stores = array( $wgDefaultExternalStore );
		} else {
			$stores = array();
		}

		if ( count( $stores ) ) {
			$this->output( "Initialising external storage $store...\n" );
			global $wgDBuser, $wgDBpassword, $wgExternalServers;
			foreach ( $stores as $storeURL ) {
				$m = array();
				if ( !preg_match( '!^DB://(.*)$!', $storeURL, $m ) ) {
					continue;
				}
	
				$cluster = $m[1];
	
				# Hack
				$wgExternalServers[$cluster][0]['user'] = $wgDBuser;
				$wgExternalServers[$cluster][0]['password'] = $wgDBpassword;
	
				$store = new ExternalStoreDB;
				$extdb =& $store->getMaster( $cluster );
				$extdb->query( "SET table_type=InnoDB" );
				$extdb->query( "CREATE DATABASE {$to}" );
				$extdb->query( "ALTER TABLE {$from}.blobs RENAME TO {$to}.blobs" );
				$extdb->selectDB( $from );
				$extdb->sourceFile( $this->getDir() . '/storage/blobs.sql' );
				$extdb->immediateCommit();
			}
		}
		$this->output( "done.\n" );
	}
}

$maintClass = "RenameWiki";
require_once( DO_MAINTENANCE );
