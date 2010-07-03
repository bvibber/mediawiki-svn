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
		wfSuppressWarnings();
		$result = (bool)chdir( $dir );
		wfRestoreWarnings();
		return $result;
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
			wfSuppressWarnings();
			$result = chgrp( $file, $group );
			wfRestoreWarnings();
			return $result;
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
			wfSuppressWarnings();
			$result = (bool)chmod( $file, $mode );
			wfRestoreWarnings();
			return $result; 
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
			wfSuppressWarnings();
			$result = (bool)chown( $file, $owner );
			wfRestoreWarnings();			
			return $result;
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
			wfSuppressWarnings();
			$result = (bool)unlink( $path );
			wfRestoreWarnings();			
			return $result;
		}
			
		if ( !$recursive && $this->isDir( $path ) ) {
			wfSuppressWarnings();
			$result = (bool)rmdir( $path );
			wfRestoreWarnings();			
			return $result;
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

		if ( $success && file_exists( $path ) ) {
			wfSuppressWarnings();
			$rmdirRes = rmdir( $path );
			wfRestoreWarnings();	

			if ( !$rmdirRes ) {
				$success = false;
			}
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
			wfSuppressWarnings();
			$renameRes = rename( $from, $to);
			wfRestoreWarnings();		
		if ( $renameRes ) {
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
		wfSuppressWarnings();
		$result = file_exists( $file );
		wfRestoreWarnings();		
		return $result;
	}

	/**
	 * @see Filesystem::getChmod
	 */
	public function getChmod( $file ) {
		wfSuppressWarnings();
		$fileperms = fileperms( $file );
		wfRestoreWarnings();		
		return substr( decoct( $fileperms ), 3 );
	}

	/**
	 * @see Filesystem::getContents
	 */
	public function getContents( $file ) {
		wfSuppressWarnings();
		$result = file_get_contents( $file );
		wfRestoreWarnings();		
		return $result;
	}

	/**
	 * @see Filesystem::getCurrentWorkingDir
	 */
	public function getCurrentWorkingDir() {
		wfSuppressWarnings();
		$result = getcwd();
		wfRestoreWarnings();		
		return $result;		
	}

	/**
	 * @see Filesystem::getGroup
	 */
	public function getGroup( $file ) {
		wfSuppressWarnings();
		$gid = filegroup( $file );
		wfRestoreWarnings();
		
		if ( !$gid ) {
			return false;
		}
			
		if ( function_exists( 'posix_getgrgid' ) ) {
			$groupArray = posix_getgrgid( $gid );
			return $groupArray['name'];				
		}
		
		return $gid;
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