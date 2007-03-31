<?php

/**
 * Move a temporary file to a public directory, and archive the existing file 
 * if there was one.
 */

require( dirname( __FILE__ ) . '/WebStoreCommon.php' );
$IP = dirname( realpath( __FILE__ ) ) . '/../..';
chdir( $IP );
require( './includes/WebStart.php' );

class WebStorePublish extends WebStoreCommon {
	const DELETE_SOURCE = 1;

	function execute() {
		global $wgRequest;

		if ( !$this->checkAccess() ) {
			$this->error( 403, 'webstore_access' );
			return false;
		}

		if ( !$wgRequest->wasPosted() ) {
			echo $this->dtd();
?>
<html>
<head><title>publish.php Test Interface</title></head>
<body>
<form method="post" action="publish.php">
<p>Source repository: <select name="srcRepo" value="public">
<option>public</option>
<option>temp</option>
<option>deleted</option>
</select></p>
<p>Source: <input type="text" name="src"/></p>
<p>Destination: <input type="text" name="dst"/></p>
<p>Archive: <input type="text" name="archive"/></p>
<p><input type="submit" value="OK"/></p>
</form>
</body>
</html>
<?php
			return true;
		}

		$srcRepo = $wgRequest->getVal( 'srcRepo' );
		if ( !$srcRepo ) {
			$srcRepo = 'temp';
		}
		// Delete the source file if the source repo is not the public one
		$deleteSource = ( $srcRepo != 'public' );

		$srcRel = $wgRequest->getVal( 'src' );
		$dstRel = $wgRequest->getVal( 'dst' );
		$archiveRel = $wgRequest->getVal( 'archive' );

		// Check for directory traversal
		if ( !$this->validateFilename( $srcRel ) || 
			!$this->validateFilename( $dstRel ) || 
			!$this->validateFilename( $archiveRel ) )
		{
			$this->error( 400, 'webstore_path_invalid' );
			return false;
		}

		// Don't publish into odd subdirectories of the public repository. 
		// Some directories may be temporary caches with a potential for 
		// data loss.
		if ( !preg_match( '!^archive|[a-zA-Z0-9]/!', $dstRel ) ) {
			$this->error( 400, 'webstore_path_invalid' );
			return false;
		}

		// Don't move anything to a filename that ends with the reserved suffix
		if ( substr( $dstRel, -12 ) == '.MW_WebStore' || 
			substr( $archiveRel, -12 ) == '.MW_WebStore' ) 
		{
			$this->error( 400, 'webstore_path_invalid' );
			return false;
		}

		$srcRoot = $this->getRepositoryRoot( $srcRepo );
		if ( strval( $srcRoot ) == '' ) {
				$this->error( 400, 'webstore_invalid_repository' );
				return false;
		}

		$srcPath = $srcRoot . '/' . $srcRel;
		$dstPath = $this->publicDir .'/'. $dstRel;
		$archivePath = $this->publicDir . '/archive/' . $archiveRel;

		if ( file_exists( $dstPath ) ) {
			$error = $this->publishAndArchive( $srcPath, $dstPath, $archivePath, $deleteSource );
		} elseif ( $deleteSource ) {
			$error = $this->movePath( $srcPath, $dstPath );
		} else {
			$error = $this->copyPath( $srcPath, $dstPath );
		}
		if ( $error !== true ) {
			$this->error( 500, $error );
			return false;
		}

		echo $this->dtd();
?>
<html>
<head><title>MediaWiki publish OK</title></head>
<body>File published successfully</body>
</html>
<?php
		return true;
	}

	/**
	 * Does a three-way move:
	 *    $dstPath -> $archivePath
	 *    $srcPath -> $dstPath
	 * with a reasonable chance of atomic operation under various adverse conditions.
	 *
	 * @return true on success, error message on failure
	 */
	function publishAndArchive( $srcPath, $dstPath, $archivePath, $flags = 0 ) {
		$archiveLockFile = false;
		$dstLockFile = false;
		$error = true;
		do {
			// Create archive directory
			if ( !wfMkdirParents( dirname( $archivePath ) ) ) {
				$error = 'webstore_archive_mkdir';
				break;
			}

			// Obtain both writer locks
			$archiveLockPath = "$archivePath.lock.MW_WebStore";
			$archiveLockFile = fopen( $archiveLockPath, 'w' );
			if ( !$archiveLockFile ) {
				$error = 'webstore_lock_open';
				break;
			}
			if ( !flock( $archiveLockFile, LOCK_EX | LOCK_NB ) ) {
				$error = 'webstore_archive_lock';
				break;
			}

			$dstLockPath = "$dstPath.lock.MW_WebStore";
			$dstLockFile = fopen( $dstLockPath, 'w' );
			if ( !$dstLockFile ) {
				$error = 'webstore_lock_open';
				break;
			}
			if ( !flock( $dstLockFile, LOCK_EX | LOCK_NB ) ) {
				$error = 'webstore_dest_lock';
				break;
			}

			// Copy the old file to the archive. Leave a copy in place in its 
			// current location for now so that webserving continues to work.
			// If we had access to the real C rename() call, then we could use
			// link() instead and avoid the copy, but the chance that PHP might 
			// copy and delete on the subsequent rename() call, thereby overwriting 
			// the archive, makes this a dangerous option.
			//
			// NO_LOCK option because we already have the lock
			$error = $this->copyPath( $dstPath, $archivePath, self::NO_LOCK );
			if ( $error !== true ) {
				break;
			}

			// Move in the new file
			if ( $flags & self::DELETE_SOURCE ) {
				if ( $this->windows ) {
					// PHP doesn't provide access to the MOVEFILE_REPLACE_EXISTING
					unlink( $dstPath );
				}
				if ( !rename( $srcPath, $dstPath ) ) {
					wfDebug( "$srcPath -> $dstPath\n" );
					$error = 'webstore_rename';
					break;
				}
			} else {
				$error = $this->copyPath( $srcPath, $dstPath, self::NO_LOCK | self::OVERWRITE );
			}
		} while (false);
		
		// Close the lock files
		$error2 = true;
		if ( $archiveLockFile ) {
			$error2 = $this->closeAndDelete( $archiveLockFile, $archiveLockPath );
		}
		if ( $dstLockFile ) {
			$error2 = $this->closeAndDelete( $dstLockFile, $dstLockPath );
		}
		if ( $error === true && $error2 !== true ) {
			$error = $error2;
		}

		return $error;
	}
}

$w = new WebStorePublish;
$w->execute();

?>
