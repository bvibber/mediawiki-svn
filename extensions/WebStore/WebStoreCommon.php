<?php

$wgWebStoreSettings = array(
	/**
	 * Set this in LocalSettings.php to an array of IP ranges allowed to access 
	 * the store. Empty by default for maximum security.
	 */
	'accessRanges' => array( '127.0.0.1' ),

	/**
	 * Access ranges for inplace-scaler.php
	 */
	'scalerAccessRanges' => array( '127.0.0.1' ),

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

	/**
	 * Connect timeout for forwarded HTTP requests, in seconds
	 */
	'httpConnectTimeout' => 0.01,

	/**
	 * Overall request timeout for forwarded HTTP requests, in seconds
	 */
	'httpOverallTimeout' => 180,

	/**
	 * Servers that can be used for image scaling
	 */
	'scalerServers' => array( 'localhost' ),
);


$wgWebStoreAccess = array();

class WebStoreCommon {
	const NO_LOCK = 1;
	const OVERWRITE = 2;
	
	static $httpErrors = array(
		400 => 'Bad Request',
		403 => 'Access Denied',
		404 => 'File not found',
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
		$this->windows = wfIsWindows();
		
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
	 * Close and delete a file, using an order of operations appropriate for the OS
	 */
	function closeAndDelete( $file, $path ) {
		wfDebug( "Deleting $file, $path\n" );
		if ( $this->windows ) {
			// Close lock file
			if ( !fclose( $file ) ) return false;

			// Ignore errors on unlink, it may just be a second thread reusing the file
			unlink( $path );
		} else {
			// Unlink first and then close, so that we don't accidentally unlink a lockfile
			// which is locked by someone else.
			if ( !unlink( $path ) ) return false;
			if ( !fclose( $file ) ) return false;
		}
		return true;
	}

	/**
	 * Move a file from one place to another. Fails if the destination file already exists.
	 * Requires a filesystem with locking semantics to work concurrently, i.e. not NFS. 
	 *
	 * $flags may be: 
	 * 		self::NO_LOCK if you already have the destination lock. 
	 */
	function movePath( $srcPath, $dstPath, $flags = 0 ) {
		$lockFile = false;
		$error = true;
		do {
			// Create destination directory
			if ( !wfMkdirParents( dirname( $dstPath ) ) ) {
				$error = 'webstore_dest_mkdir';
				break;
			}

			// Open lock file
			// The MW_WebStore suffix should be protected at the middleware entry points
			if ( !($flags & self::NO_LOCK) ) {
				$lockFileName = "$dstPath.lock.MW_WebStore";
				$lockFile = fopen( $lockFileName , 'w' );
				if ( !$lockFile ) {
					$error = 'webstore_lock_open';
					break;
				}
				if ( !flock( $lockFile, LOCK_EX | LOCK_NB ) ) {
					$error = 'webstore_dest_lock';
					break;
				}
			}

			// Check for destination existence
			if ( file_exists( $dstPath ) ) {
				$error = 'webstore_dest_exists';
				break;
			}

			// This is the critical gap, the reason for the locking.

			// Rename the file
			if ( !rename( $srcPath, $dstPath ) ) {
				wfDebug( "$srcPath -> $dstPath\n" );
				$error = 'webstore_rename';
				break;
			}
		} while (false);

		// Close and delete the lockfile
		$error2 = true;
		if ( $lockFile && !($flags & self::NO_LOCK) ) {
			if ( !$this->closeAndDelete( $lockFile, $lockFileName ) )  {
				$error2 = 'webstore_lock_close';
			}
		}
		if ( $error !== true ) {
			return $error;
		} else {
			return $error2;
		}
	}

	/*
	 * Atomically copy a file from one place to another. Fails if the destination file 
	 * already exists. Requires a filesystem with locking semantics to work concurrently, 
	 * i.e. not NFS.
	 *
	 * $flags may be: 
	 *      * self::NO_LOCK if you already have the destination lock (*.lock.MW_WebStore)
	 *      * self::OVERWRITE to overwrite the destination if it exists
	 */
	function copyPath( $srcPath, $dstPath, $flags = 0 ) {
		$error = true;
		$tempFile = false;
		$srcFile = false;

		do {
			// Create destination directory
			if ( !wfMkdirParents( dirname( $dstPath ) ) ) {
				$error = 'webstore_dest_mkdir';
				break;
			}

			// Open the source file
			$srcFile = fopen( $srcPath, 'r' );
			if ( !$srcFile ) {
				$error = 'webstore_src_open';
				break;
			}

			// Copy the file to a temporary location in the same directory as the target
			// Open the temporary file and lock it
			$tempFileName = "$dstPath.temp.MW_WebStore";
			$tempFile = fopen( $tempFileName, 'a+' );
			if ( !$tempFile ) {
				$error = 'webstore_temp_open';
				break;
			}
			if ( !flock( $tempFile, LOCK_EX | LOCK_NB ) ) {
				$error = 'webstore_temp_lock';
				break;
			}
			// Truncate the file if there's anything in it (unlikely)
			if ( ftell( $tempFile ) ) {
				ftruncate( $tempFile, 0 );
			}
			// Copy the data from filehandle to filehandle
			if ( !$this->copyFile( $srcFile, $tempFile ) ) {
				$error = 'webstore_temp_copy';
				break;
			}

			// On Windows, close the temporary file now so that we don't get a lock error
			// This creates a gap where another process may overwrite the temporary file
			if ( $this->windows ) {
				if ( !fclose( $tempFile ) ) {
					$error = 'webstore_temp_close';
					break;
				}
				$tempFile = false;
			}

			// Atomically move the temporary file into its final destination
			if ( $flags & self::OVERWRITE ) {
				if ( $this->windows && file_exists( $dstPath ) ) {
					unlink( $dstPath );
				}
				if ( !rename( $tempFileName, $dstPath ) ) {
					$error = 'webstore_rename';
				}
			} else {
				$error = $this->movePath( $tempFileName, $dstPath, $flags );
			}
			if ( $error !== true ) {
				break;
			}
		} while ( false );

		// Close the source file
		$error2 = true;
		if ( $srcFile ) {
			if ( !fclose( $srcFile ) ) {
				$error2 = 'webstore_src_close';
			}
		}
		// Close the temporary file
		if ( $tempFile ) {
			if ( !fclose( $tempFile, $tempFileName ) ) {
				$error2 = 'webstore_temp_close';
			}
		}
		if ( $error === true && $error2 !== true ) {
			$error = $error2;
		}

		return $error;
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
			if ( flock( $lockFile, LOCK_EX | LOCK_NB ) ) {
				$dir = opendir( $path );
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
