<?php

/**
 * File holding the DirectFilesystem class.
 *
 * @file DirectFilesystem.php
 * @ingroup Deployment
 * @ingroup Filesystem
 *
 * @author Jeroen De Dauw
 */

/**
 * Filesystem class for direct PHP file and folder manipulation.
 * 
 * @author Jeroen De Dauw
 */
class DirectFilesystem extends Filesystem {

	/**
	 * Constructor.
	 */
	public function __construct() {
		
	}
	
	/**
	 * @see Filesystem::connect
	 */
	public function connect() {
		return true;
	}
	
	/**
	 * @see Filesystem::changeDir
	 */
	public function changeDir( $dir ) {
		return (bool)@chdir( $dir );
	}

	/**
	 * @see Filesystem::changeFileGroup
	 */
	public function changeFileGroup( $file, $group, $recursive = false ) {
		if ( !$this->exists( $file ) ) {
			return false;
		}
		
		// Not recursive, so just use chgrp.
		if ( !$recursive || !$this->isDir( $file ) ) {
			return @chgrp( $file, $group );
		}
		
		// Recursive approach required.
		$file = rtrim( $file, '/' ) . '/';
		$files = $this->listDir( $file );
		
		foreach ( $files as $fileName ) {
			$this->changeFileGroup( $file . $fileName, $group, $recursive );
		}

		return true;		
	}

	/**
	 * @see Filesystem::chmod
	 */
	public function chmod( $file, $mode = false, $recursive = false ) {
		// TODO: refactor up?
		if ( !$mode ) {
			if ( $this->isFile( $file ) ) {
				$mode = FS_CHMOD_FILE;
			}
			elseif ( $this->isDir( $file ) ) {
				$mode = FS_CHMOD_DIR;
			}
			else {
				return false;
			}
		}

		// Not recursive, so just use chmod.
		if ( !$recursive || !$this->isDir( $file ) ) {
			return (bool)@chmod( $file, $mode );
		}
			
		// Recursive approach required.
		$file = rtrim( $file, '/' ) . '/';
		$files = $this->listDir( $file );
		
		foreach ( $files as $fileName ) {
			$this->chmod( $file . $fileName, $mode, $recursive );
		}

		return true;	
	}

	/**
	 * @see Filesystem::chown
	 */
	public function chown( $file, $owner, $recursive = false ) {
		if ( !$this->exists( $file ) ) {
			return false;
		}
		
		// Not recursive, so just use chown.
		if ( !$recursive || !$this->isDir( $file ) ) {
			return (bool)@chown( $file, $owner );
		}
			
		// Recursive approach required.
		$file = rtrim( $file, '/' ) . '/';
		$files = $this->listDir( $file );
		
		foreach ( $files as $fileName ) {
			$this->chown( $file . $fileName, $owner, $recursive );
		}

		return true;	
	}

	/**
	 * @see Filesystem::delete
	 */
	public function delete( $path, $recursive = false ) {
		if ( empty( $path ) ) {
			return false;
		}
			
		// For win32, occasional problems deleteing files otherwise.
		$path = str_replace( '\\', '/', $path ); 

		if ( $this->isFile( $path ) ) {
			return (bool)@unlink( $path );
		}
			
		if ( !$recursive && $this->isDir( $path ) ) {
			return (bool)@rmdir( $path );
		}
			
		// Recursive approach required.
		$path = rtrim( $path, '/' ) . '/';
		$files = $this->listDir( $path );
		
		$success = true;
		
		foreach ( $files as $fileName ) {
			if ( !$this->delete( $path . $fileName, $recursive ) ) {
				$success = false;
			}
		}

		if ( $success && file_exists( $path ) && !@rmdir( $path ) ) {
			$success = false;
		}
		
		return $success;
	}

	/**
	 * @see Filesystem::doCopy
	 */
	protected function doCopy( $from, $to ) {
		return copy( $from, $to );
	}

	/**
	 * @see Filesystem::doMove
	 */
	protected function doMove( $from, $to, $overwrite ) {
		// try using rename first.  if that fails (for example, source is read only) try copy and delete.
		if ( @rename( $from, $to) ) {
			return true;
		}

		if ( $this->copy( $from, $to, $overwrite ) && $this->exists( $to ) ) {
			$this->delete( $from );
			return true;
		} else {
			return false;
		}	
	}

	/**
	 * @see Filesystem::exists
	 */
	public function exists( $file ) {
		return @file_exists( $file );
	}

	/**
	 * FIXME does not handle errors in fileperms()
	 * 
	 * @see Filesystem::getChmod
	 */
	public function getChmod( $file ) {
		return substr( decoct( @fileperms( $file ) ), 3 );
	}

	/**
	 * @see Filesystem::getContents
	 */
	public function getContents() {
		
	}

	/**
	 * @see Filesystem::getCreationTime
	 */
	public function getCreationTime( $file ) {
		
	}

	/**
	 * @see Filesystem::getCurrentWorkingDir
	 */
	public function getCurrentWorkingDir() {
		
	}

	/**
	 * @see Filesystem::getGroup
	 */
	public function getGroup( $file ) {
		
	}

	/**
	 * @see Filesystem::getModificationTime
	 */
	public function getModificationTime( $file ) {
		
	}

	/**
	 * @see Filesystem::getOwner
	 */
	public function getOwner( $file ) {
		
	}

	/**
	 * @see Filesystem::getSize
	 */
	public function getSize( $file ) {
		
	}

	/**
	 * @see Filesystem::isDir
	 */
	public function isDir( $path ) {
		
	}

	/**
	 * @see Filesystem::isFile
	 */
	public function isFile( $path ) {
		
	}

	/**
	 * @see Filesystem::isReadable
	 */
	public function isReadable( $file ) {
		
	}

	/**
	 * @see Filesystem::isWritable
	 */
	public function isWritable( $file ) {
		
	}

	/**
	 * @see Filesystem::listDir
	 */
	public function listDir( $path, $includeHidden = true, $recursive = false ) {
		
	}

	/**
	 * @see Filesystem::makeDir
	 */
	public function makeDir( $path, $chmod = false, $chown = false, $chgrp = false ) {
		
	}

	/**
	 * @see Filesystem::touch
	 */
	public function touch( $file, $time = 0, $atime = 0 ) {
		
	}

	/**
	 * @see Filesystem::writeToFile
	 */
	public function writeToFile( $file, $contents ) {
		
	}	
	
}