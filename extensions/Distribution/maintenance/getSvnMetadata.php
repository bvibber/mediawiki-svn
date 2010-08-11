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
 * @since 0.1
 *
 * @author Jeroen De Dauw
 * 
 * @ingroup Maintenance
 */

require_once( dirname( __FILE__ ) . '/../../../phase3/maintenance/Maintenance.php' );

class GetSvnMetadata extends Maintenance {
	
	/**
	 * @var array or false
	 */
	protected $extensionList = false;
	
	/**
	 * @see Maintenance::execute
	 * 
	 * @since 0.1
	 */	
	public function __construct() {
		parent::__construct();
		
		$this->mDescription = "Scrapes the WMF SVN repo for package meta-data and stores it so it can be queried via the API.";
	}
	
	/**
	 * @see Maintenance::execute
	 * 
	 * @since 0.1
	 */
	public function execute() {
		global $wgExtDistWorkingCopy;
		
		$this->output( "Extension metadata import started...\n" );
		
		$extensionGroups = $this->getExtensionList();
		
		$extensionAmount = 0;
		
		// TODO: after initial version, make this more generic to also work with branches
		
		foreach ( $extensionGroups as $group => $extensions ) {
			$extensionAmount += count( $extensions );
			
			foreach( $extensions as $extension ) {
				$extensionDir = "$wgExtDistWorkingCopy/trunk/$extension";
				$this->output( "Starting work on $extension..." );
				$this->saveExtensionMetadata( $this->getExtensionMetadata( $extension, $extensionDir ) );
				$this->output( " done.\n" );
			}			
		}
		
		$this->output( "Finished importing metadata from $extensionAmount extensions." );
	}
	
	/**
	 * Goes through a local checkout of the svn repository and returns a list
	 * with all found extensions. Based on getExtensionList from ExtensionDistributor.
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	protected function getExtensionList() {
		global $wgExtDistWorkingCopy, $wgExtDistBranches;

		if ( $this->extensionList !== false ) {
			return $this->extensionList;
		}

		$this->extensionList = array();
		
		foreach ( $wgExtDistBranches as $branchPath => $branch ) {
			$wc = "$wgExtDistWorkingCopy/$branchPath/extensions";
			$dir = opendir( $wc );
			
			if ( !$dir ) {
				return array();
			}

			$this->extensionList[$branchPath] = array();
			
			while ( false !== ( $file = readdir( $dir ) ) ) {
				if ( substr( $file, 0, 1 ) == '.' 
					|| !is_dir( "$wc/$file" )
					|| file_exists( "$wc/$file/NO-DIST" ) ) {
						continue;
				}

				$this->extensionList[$branchPath][] = $file;
			}
			
			natcasesort( $this->extensionList[$branchPath] );
		}
		
		return $this->extensionList;
	}
	
	/**
	 * Gets the metadata for a single extension from the checked out
	 * extension directory and returns it as an array.
	 * 
	 * @since 0.1
	 * 
	 * @param $extensionName String
	 * @param $extensionDir String
	 * 
	 * @return array
	 */
	protected function getExtensionMetadata( $extensionName, $extensionDir ) {
		
		// TODO: implement method (currently returning dummy data)
		$extension = array(
			'name' => $extensionName,
			'description' => 'Awesome extension will be awesome when fully implemented.',
			'version' => 4.2,
			'authors' => 'James T. Kirk, Luke Skywalker',
			'url' => 'http://www.mediawiki.org/wiki/Special:ExtensionDistributor/' . $extensionName
		);
		
		return $extension;
	}
	
	/**
	 * Saves the metadata of a single extension version into the db.
	 * 
	 * @since 0.1
	 * 
	 * @param $metaData Array
	 */
	protected function saveExtensionMetadata( array $metaData ) {
		// Get the database connections.
		$dbr = wfGetDB( DB_SLAVE );

		// Query for existing units with the same name.
		$unit = $dbr->selectRow(
			'distribution_units',
			array( 'unit_id' ),
			array( 'unit_name' => $metaData['name'] )
		);		
		
		// Map the unit values to the db schema.
		$unitValues = array(
			'unit_name' => $metaData['name'],
			'current_version_nr' => $metaData['version'],
			'current_desc' => $metaData['description'],
			'current_authors' => $metaData['authors'],
			'current_url' => $metaData['url'],
		);
		
		// Map the version values to the db schema.
		$versionValues = array(
			'version_status' => DistributionRelease::mapState( DistributionRelease::getDefaultState() ), // TODO
			'version_desc' => $metaData['description'],
			'version_authors' => $metaData['authors'],
			'version_url' => $metaData['url'],			
		);		
		
		// Insert or update the unit.
		if ( $unit == false ) {
			$this->insertUnit( $unitValues, $versionValues );
		}
		else {
			$this->updateUnit( $unit, $unitValues, $versionValues, $dbr );
		}
	}
	
	/**
	 * Inserts a new unit and creates a new version for this unit.
	 * 
	 * @since 0.1
	 * 
	 * @param $unitValues Array
	 * @param $versionValues Array
	 */
	protected function insertUnit( array $unitValues, array $versionValues ) {
		$dbw = wfGetDB( DB_MASTER );
		
		$dbw->insert(
			'distribution_units',
			$unitValues
		);
		
		$versionValues['version_nr'] = $unitValues['current_version_nr'];
		$versionValues['unit_id'] = $dbw->insertId();
		
		$dbw->insert(
			'distribution_unit_versions',
			$versionValues
		);		
	}
	
	/**
	 * Updates an existing unit. If the unit already had a version for the current number,
	 * it will be updated, otherwise a new one will be created.
	 * 
	 * @since 0.1
	 * 
	 * @param $unit
	 * @param $unitValues Array
	 * @param $versionValues Array
	 * @param $dbr DatabaseBase
	 */
	protected function updateUnit( $unit, array $unitValues, array $versionValues, DatabaseBase $dbr ) {
		$dbw = wfGetDB( DB_MASTER );
		
		$versionValues['unit_id'] = $unit->unit_id;
		
		// Query for existing versions of this unit with the same version number.
		$version = $dbr->selectRow(
			'distribution_unit_versions',
			array( 'version_id' ),
			array( 
				'unit_id' => $unit->unit_id,
				'version_nr' => $unitValues['current_version_nr']
			)
		);
		
		if ( $version == false ) {
			$versionValues['version_nr'] = $unitValues['current_version_nr'];
			
			$dbw->insert(
				'distribution_unit_versions',
				$versionValues
			);
			
			$unitValues['current_version_nr'] = $dbw->insertId();			
		}
		else {
			$dbw->update(
				'distribution_unit_versions',
				$versionValues,
				array( 'version_id' => $version->version_id )
			);	

			$unitValues['current_version_nr'] = $version->version_id;
		}
		
		$dbw->update(
			'distribution_units',
			$unitValues,
			array( 'unit_id' => $unit->unit_id )
		);		
	}	
	
}

$maintClass = "GetSvnMetadata";
require_once( DO_MAINTENANCE );