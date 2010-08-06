<?php

/**
 * This script scrapes the WMF SVN repo for package meta-data and stores it
 * so it can be queried via the API.
 *
 * Usage:
 *  no parameters
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
 * @author Jeroen De Dauw
 * @author Yaron Koren
 * @ingroup Maintenance
 */

require_once( dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

class GetSvnMetadata extends Maintenance {
	
	public function __construct() {
		parent::__construct();
		
		$this->mDescription = "Scrapes the WMF SVN repo for package meta-data and stores it so it can be queried via the API.";
	}
	
	public function execute() {
		$dbr = wfGetDB( DB_SLAVE );
		
		// TODO
		
		$this->output( "..." );
	}
	
}

$maintClass = "GetSvnMetadata";
require_once( DO_MAINTENANCE );