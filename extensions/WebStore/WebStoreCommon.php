<?php

$wgWebStoreSettings = array(
	/**
	 * Set this in LocalSettings.php to an array of IP ranges allowed to access 
	 * the store. Empty by default for maximum security.
	 */
	'accessRanges' => array(),

	/**
	 * Access ranges for inplace-scaler.php
	 */
	'scalerAccessRanges' => array(),

	/**
	 * Main public directory. If false, uses $wgUploadDirectory
	 */
	'publicDir' => false,

	/**
	 * Private temporary directory. If false, uses $wgTmpDirectory
	 */
	'tmpDir' => false,

	/**
	 * Private directory for deleted files. If false, uses $wgFileStore['deleted']['directory']
	 */
	'deletedDir' => false,

	/**
	 * Expiration time for temporary files in seconds. Must be at least 7200.
	 */
	'tempExpiry' => 7200,

	/**
	 * PHP file to display on 404 errors in 404-handler.php
	 */
	'fallback404' => false,
);


$wgWebStoreAccess = array();

class WebStoreCommon {
	static $httpErrors = array(
		400 => 'Bad Request',
		403 => 'Access Denied',
		500 => 'Internal Server Error',
	);

	static $tempDirFormat = 'Y-m-d\TH';

	var $accessRanges = array(), $publicDir = false, $tmpDir = false, 
		$deletedDir = false, $tempExpiry = 7200,
		$inplaceScalerAccess = array();

	function __construct() {
		global $wgWebStoreSettings, $wgUploadDirectory, $wgTmpDirectory, $wgFileStore;

		foreach ( $wgWebStoreSettings as $name => $value ) {
			$this->$name = $value;
		}
		if ( !$this->tmpDir ) {
			$this->tmpDir = $wgTmpDirectory;
		}
		if ( !$this->publicDir ) {
			$this->publicDir = $wgUploadDirectory;
		}
		if ( !$this->deletedDir ) {
			if ( isset( $wgFileStore['deleted']['directory'] ) ) {
				$this->deletedDir = $wgFileStore['deleted']['directory'];
			} else {
				// No deletion
				$this->deletedDir = false;
			}
		}
		
		self::initialiseMessages();
	}

	function dtd() {
		return <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

EOT;
	}

	function error( $code, $msgName /*, ... */ ) {
		$params = array_slice( func_get_args(), 1 );
		$msgText = htmlspecialchars( call_user_func_array( 'wfMsg', $params ) );
		$encMsgName = htmlspecialchars( $msgName );
		$info = self::$httpErrors[$code];
		header( "HTTP/1.1 $code $info" );
		echo $this->dtd();
		echo <<<EOT
<html><head><title>$info</title></head>
<body><h1>$info</h1><p>
$encMsgName: $msgText
</p></body></html>
EOT;
	}

	function validateFilename( $filename ) {
		if ( strval( $filename ) == '' ) {
			return false;
		}
		/**
		 * Use the same traversal protection as Title::secureAndSplit()
		 */
		if ( strpos( $filename, '.' ) !== false &&
		     ( $filename === '.' || $filename === '..' ||
		       strpos( $filename, './' ) === 0  ||
		       strpos( $filename, '../' ) === 0 ||
		       strpos( $filename, '/./' ) !== false ||
		       strpos( $filename, '/../' ) !== false ) )
		{
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Copy from one open file handle to another, until EOF
	 */
	function copyFile( $src, $dest ) {
		while ( !feof( $src ) ) {
			$data = fread( $src, 1048576 );
			if ( $data === false ) {
				return false;
			}
			if ( fwrite( $dest, $data ) === false ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Move a file from one place to another. Fails if the destination file already exists.
	 * Requires a filesystem with locking semantics to work concurrently, i.e. not NFS.
	 */
	function movePath( $srcPath, $dstPath, $deleteSource = true ) {
		// Create destination directory
		if ( !wfMkdirParents( dirname( $dstPath ) ) ) return 'webstore_dest_mkdir';

		// Open destination file, lock it
		$dstFile = @fopen( $dstPath, 'x' );
		if ( !$dstFile ) return 'webstore_dest_open';
		if ( !flock( $dstFile, LOCK_EX | LOCK_NB ) ) return 'webstore_dest_lock';

		// Open source file
		$srcFile = @fopen( $srcPath, 'r' );
		if ( !$srcFile ) return 'webstore_src_open';

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
	

	function checkAccess() {
		foreach ( $this->accessRanges as $range ) {
			if ( IP::isInRange( $_SERVER['REMOTE_ADDR'], $range ) ) {
				return true;
			}
		}
		return false;
	}

	static function initialiseMessages() {
		static $done = false;
		if ( $done ) {
			return;
		}
		$done = true;

		require( dirname( __FILE__ ) . '/WebStore.i18n.php' );

		global $wgMessageCache;
		foreach ( $messages as $code => $messages2 ) {
			$wgMessageCache->addMessages( $messages2, $code );
		}
	}

	/**
	 * Clean up temporary directories
	 * @param integer $now The current unix timestamp
	 */
	function cleanupTemp( $now ) {
		$expiry = max( $this->tempExpiry, 7200 );
		$cleanupDir = $this->tmpDir . '/' . gmdate( self::$tempDirFormat, $now - $expiry );
		$this->cleanup( $cleanupDir );
	}

	/**
	 * Delete a directory if it's not being deleted already
	 */
	function cleanup( $path ) {
		if ( file_exists( $path )  ) {
			$lockFile = fopen( "$path.deleting", 'a+' );
			if ( @flock( $lockFile, LOCK_EX | LOCK_NB ) ) {
				$dir = @opendir( $path );
				if ( !$dir ) {
					fclose( $lockFile );
					return;
				}
				while ( false !== ( $fileName = readdir( $dir ) ) ) {
					unlink( $fileName );
				}
				closedir( $dir );
				rmdir( $path );
			}
			fclose( $lockFile );
		}
	}

	/**
	 * Get the root directory for a given repository: public, temp or deleted
	 */
	function getRepositoryRoot( $repository ) {
		switch ( $repository ) {
			case 'public':
				return $this->publicDir;
			case 'temp':
				return $this->tmpDir;
			case 'deleted':
				return $this->deletedDir;
			default:
				return false;
		}
	}
}
?>
