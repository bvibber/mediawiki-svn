<?php
/**
 * @file
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class UploadDumper extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Generates list of uploaded files which can be fed to tar or similar.
By default, outputs relative paths against the parent directory of \$wgUploadDirectory.";
		$this->addOption( 'base', 'Set base relative path instead of wiki include root', false, true );
		$this->addOption( 'local', 'List all local files, used or not. No shared files included' );
		$this->addOption( 'used', 'Skip local images that are not used' );
		$this->addOption( 'shared', 'Include images used from shared repository' );
	}

	public function execute() {
		global $IP, $wgUseSharedUploads;
		$this->mAction = 'fetchLocal';
		$this->mBasePath = $this->getOption( 'base', $IP );
		$this->mShared = false;
		$this->mSharedSupplement = false;

		if( $this->hasOption('local') ) {
			$this->mAction = 'fetchLocal';
		}
		
		if( $this->hasOption('used') ) {
			$this->mAction = 'fetchUsed';
		}
		
		if( $this->hasOption('shared') ) {
			if( $this->hasOption('used') ) {
				// Include shared-repo files in the used check
				$this->mShared = true;
			} else {
				// Grab all local *plus* used shared
				$this->mSharedSupplement = true;
			}
		}
		$this->{$this->mAction}( $this->mShared );
		if( $this->mSharedSupplement ) {
			$this->fetchUsed( true );
		}
	}

	/**
	 * Fetch a list of all or used images from a particular image source.
	 * @param string $table
	 * @param string $directory Base directory where files are located
	 * @param bool $shared true to pass shared-dir settings to hash func
	 */
	function fetchUsed( $shared ) {
		$dbr = wfGetDB( DB_SLAVE );
		$image = $dbr->tableName( 'image' );
		$imagelinks = $dbr->tableName( 'imagelinks' );
		
		$sql = "SELECT DISTINCT il_to, img_name
			FROM $imagelinks
			LEFT OUTER JOIN $image
			ON il_to=img_name";
		$result = $dbr->query( $sql );
		
		foreach( $result as $row ) {
			$this->outputItem( $row->il_to, $shared );
		}
		$dbr->freeResult( $result );
	}

	function fetchLocal( $shared ) {
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->select( 'image',
			array( 'img_name' ),
			'',
			__METHOD__ );
		
		foreach( $result as $row ) {
			$this->outputItem( $row->img_name, $shared );
		}
		$dbr->freeResult( $result );
	}
	
	function outputItem( $name, $shared ) {
		$file = wfFindFile( $name );
		if( $file && $this->filterItem( $file, $shared ) ) {
			$filename = $file->getFullPath();
			$rel = wfRelativePath( $filename, $this->mBasePath );
			$this->output( "$rel\n" );
		} else {
			wfDebug( __METHOD__ . ": base file? $name\n" );
		}
	}

	function filterItem( $file, $shared ) {
		return $shared || $file->isLocal();
	}
}

$maintClass = "UploadDumper";
require_once( DO_MAINTENANCE );
