<?php

/**
 * Rebuild the localisation cache. Useful if you disabled automatic updates
 * using $wgLocalisationCacheConf['manualRecache'] = true;
 *
 * Usage:
 *    php rebuildLocalisationCache.php [--force]
 *
 * Use --force to rebuild all files, even the ones that are not out of date.
 */

require_once( "Maintenance.php" );

class RebuildLocalisationCache extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Rebuild the localisation cache";
		$this->addOption( 'force', 'Rebuild all files, even ones not out of date' );
	}

	public function execute() {
		global $wgLocalisationCacheConf;

		ini_set( 'memory_limit', '200M' );
	
		$force = $this->hasOption('force');
	
		$conf = $wgLocalisationCacheConf;
		$conf['manualRecache'] = false; // Allow fallbacks to create CDB files
		if ( $force ) {
			$conf['forceRecache'] = true;
		}
		$lc = new LocalisationCache_BulkLoad( $conf );
	
		$codes = array_keys( Language::getLanguageNames( true ) );
		sort( $codes );
		$numRebuilt = 0;
		foreach ( $codes as $code ) {
			if ( $force || $lc->isExpired( $code ) ) {
				$this->output( "Rebuilding $code...\n" );
				$lc->recache( $code );
				$numRebuilt++;
			}
		}
		$this->output( "$numRebuilt languages rebuilt out of " . count( $codes ) . ".\n" );
		if ( $numRebuilt == 0 ) {
			$this->output( "Use --force to rebuild the caches which are still fresh.\n" );
		}
	}
}

$maintClass = "RebuildLocalisationCache";
require_once( DO_MAINTENANCE );
