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
		} else {
			$error = $this->movePath( $srcPath, $dstPath, $deleteSource );
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
	 */
	function publishAndArchive( $srcPath, $dstPath, $archivePath, $deleteSource = true ) {
		// Create archive directory
		if ( !wfMkdirParents( dirname( $archivePath ) ) ) return 'webstore_archive_mkdir';

		// Open archive file and lock it, fail if it exists
		$archiveFile = @fopen( $archivePath, 'x' );
		if ( !$archiveFile ) return 'webstore_archive_open';
		if ( !flock( $archiveFile, LOCK_EX | LOCK_NB ) ) return 'webstore_archive_lock';

		// Open old destination file, lock it
		$dstFile = @fopen( $dstPath, 'r+' );
		if ( !$dstFile ) return 'webstore_dest_open';
		if ( !flock( $dstFile, LOCK_EX | LOCK_NB ) ) return 'webstore_dest_lock';

		// Open source file
		$srcFile = @fopen( $srcPath, 'r' );
		if ( !$srcFile ) return 'webstore_src_open';

		// Copy dest to archive, close the archive file
		if ( !$this->copyFile( $dstFile, $archiveFile ) ) return 'webstore_archive_copy';
		if ( !fclose( $archiveFile ) ) return 'webstore_archive_close';

		// Truncate destination
		if ( !ftruncate( $dstFile, 0 ) || 0 !== fseek( $dstFile, 0 ) ) {
			return 'webstore_dest_write';
		}

		// Copy source to dest
		if ( !$this->copyFile( $srcFile, $dstFile ) ) return 'webstore_dest_copy';

		// Unlink the source, close the files
		if ( $deleteSource ) {
			if ( wfIsWindows() ) {
				if ( !fclose( $srcFile ) ) return 'webstore_src_close';
				unlink( $srcPath );
			} else {
				unlink( $srcPath );
				if ( !fclose( $srcFile ) ) return 'webstore_src_close';
			}
		} else {
			if ( !fclose( $srcFile ) ) return 'webstore_src_close';
		}
		if ( !fclose( $dstFile ) ) return 'webstore_dest_close';

		return true;
	}
}

$w = new WebStorePublish;
$w->execute();

?>
