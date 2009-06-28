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
	}

	public function execute() {
		$this->output( "Refresh Site Statistics\n\n" );
		SiteStats::init( $this->hasOption('update'), $this->hasOption('noviews'), $this->hasOption('active') );
	}
}

$maintClass = "InitStats";
require_once( DO_MAINTENANCE );
