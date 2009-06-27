<?php
/**
 * This script purges all language messages from the cache
 * @file
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class RebuildMessages extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Purge all language messages from the cache";
	}

	public function execute() {
		global $wgLocalDatabases, $wgDBname, $wgEnableSidebarCache, $messageMemc;
		if( $wgLocalDatabases ) {
			$databases = $wgLocalDatabases;
		} else {
			$databases = array( $wgDBname );
		}
	
		foreach( $databases as $db ) {
			$this->output( "Deleting message cache for {$db}... " );
			$messageMemc->delete( "{$db}:messages" );
			if( $wgEnableSidebarCache )
				$messageMemc->delete( "{$db}:sidebar" );
			$this->output( "Deleted\n" );
		}
	}
}


