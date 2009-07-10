<?php

/**
 * Maintenance script to re-initialise or update the site statistics table
 *
 * @file
 * @ingroup Maintenance
 * @author Brion Vibber
 * @author Rob Church <robchur@gmail.com>
 * @licence GNU General Public Licence 2.0 or later
 */

require_once( "Maintenance.php" );

class InitStats extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Re-initialise the site statistics tables";
		$this->addOption( 'update', 'Update the existing statistics (preserves the ss_total_views field)' );
		$this->addOption( 'noviews', "Don't update the page view counter" );
		$this->addOption( 'active', 'Also update active users count' );
		$this->addOption( 'use-master', 'Count using the master database' );
	}

	public function execute() {
		$this->output( "Refresh Site Statistics\n\n" );
		$counter = new SiteStatsInit( $this->hasOption( 'use-master' ) );

		$this->output( "Counting total edits..." );
		$edits = $counter->edits();
		$this->output( "{$edits}\nCounting number of articles..." );

		$good  = $counter->articles();
		$this->output( "{$good}\nCounting total pages..." );

		$pages = $counter->pages();
		$this->output( "{$pages}\nCounting number of users..." );

		$users = $counter->users();
		$this->output( "{$users}\nCounting number of images..." );

		$image = $counter->files();
		$this->output( "{$image}\n" );

		if( !$this->hasOption('noviews') ) {
			$this->output( "Counting total page views..." );
			$views = $counter->views();
			$this->output( "{$views}\n" );
		}

		if( $this->hasOption( 'active' ) ) {
			$this->output( "Counting active users..." );
			$active = SiteStatsUpdate::cacheUpdate();
			$this->output( "{$active}\n" );
		}

		$this->output( "\nUpdating site statistics..." );

		if( $this->hasOption( 'update' ) ) {
			$counter->update();
		} else {
			$counter->refresh();
		}

		$this->output( "done.\n" );
	}
}

$maintClass = "InitStats";
require_once( DO_MAINTENANCE );
