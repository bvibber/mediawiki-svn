<?php

/**
 * File holding the FtpFilesystem class.
 *
 * @file FtpFilesystem.php
 * @ingroup Deployment
 * @ingroup Filesystem
 *
 * @author Jeroen De Dauw
 */

/**
 * Filesystem class for file and folder manipulation over FTP.
 * 
 * @author Jeroen De Dauw
 */
class FtpFilesystem extends Filesystem {
	
	/**
	 * A list of options.
	 * 
	 * @var array
	 */
	protected $options = array();
	
	/**
	 * The FTP connection link.
	 * 
	 * @var unknown_type
	 */
	protected $connection;
	
	/**
	 * Constructor.
	 */
	public function __construct( $options ) {
		$this->options = $options;
		
		// Check if possible to use ftp functions.
		if ( !extension_loaded('ftp') ) {
			$this->addError( 'deploy-ftp-not-loaded' );
			return false;
		}
		
		// Check for missing required options.
		if ( !array_key_exists( 'username', $options ) ) {
			$this->addError( 'deploy-ftp-username-required' );
		}
		
		if ( !array_key_exists( 'password', $options ) ) {
			$this->addError( 'deploy-ftp-password-required' );
		}	

		if ( !array_key_exists( 'hostname', $options ) ) {
			$this->addError( 'deploy-ftp-hostname-required' );
		}			
		
		// Set default option values for those not provided.
		if ( !array_key_exists( 'port', $options ) ) {
			$options['port'] = 21;
		}
		
		if ( !array_key_exists( 'timeout', $options ) ) {
			$options['timeout'] = 240;
		}		
		
		// Other option handling.
		$options['ssl'] = array_key_exists( 'connection_type', $options ) && $options['connection_type'] == 'ftps';
		
		// Store the options.
		$this->options = $options;
	}
	
	/**
	 * @see Filesystem::connect
	 */
	public function connect() {
		// Attempt to create a connection, either with ssl or without.
		if ( $this->options['ssl'] && function_exists( 'ftp_ssl_connect' ) ) {
			wfSuppressWarnings();
			$this->connection = ftp_ssl_connect( $this->options['hostname'], $this->options['port'], $this->options['timeout'] );
			wfRestoreWarnings();			
		}
		else {
			// If this is true, ftp_ssl_connect was not defined, so add an error.
			if ( $this->options['ssl'] ) {
				$this->addError( 'deploy-ftp-ssl-not-loaded' );
			}
			
			wfSuppressWarnings();
			$this->connection = ftp_connect( $this->options['hostname'], $this->options['port'], $this->options['timeout'] );
			wfRestoreWarnings();			
		}
		
		// Check if a connection has been established.
		if ( !$this->connection ) {
			$this->addErrorMessage( wfMsgExt( 'deploy-ftp-connect-failed', $this->options['hostname'], $this->options['port'] ) );
			return false;
		}
		
		// Attempt to set the connection to use passive FTP.
		wfSuppressWarnings();
		ftp_pasv( $this->connection, true );		
		wfRestoreWarnings();		

		// Make sure the timeout is at least as much as the option.
		wfSuppressWarnings();
		if ( ftp_get_option( $this->connection, FTP_TIMEOUT_SEC ) < $this->options['timeout'] ) {
			ftp_set_option( $this->connection, FTP_TIMEOUT_SEC, $this->options['timeout'] );
		}		
		wfRestoreWarnings();		
		
		return true;
	}
	
	/**
	 * @see Filesystem::changeDir
	 */
	public function changeDir( $dir ) {
		wfSuppressWarnings();
		$result = (bool)ftp_chdir( $this->connection, $dir );
		wfRestoreWarnings();		
		return $result;
	}

	/**
	 * @see Filesystem::changeFileGroup
	 */
	public function changeFileGroup( $file, $group, $recursive = false ) {
		return false;
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
			if ( !function_exists( 'ftp_chmod' ) ) {
				wfSuppressWarnings();
				$result = (bool)ftp_site( $this->connection, sprintf( 'CHMOD %o %s', $mode, $file ) );
				wfRestoreWarnings();				
				return $result;
			}
			else {
				wfSuppressWarnings();
				$result = (bool)ftp_chmod( $this->connection, $mode, $file );
				wfRestoreWarnings();				
				return $result;
			}
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
		return false;
	}

	/**
	 * @see Filesystem::delete
	 */
	public function delete( $path, $recursive = false ) {
		if ( empty( $path ) ) {
			return false;
		}
			
		if ( $this->isFile( $path ) ) {
			wfSuppressWarnings();
			$result = (bool)ftp_delete( $this->connection, $path );
			wfRestoreWarnings();			
			return $result;
		}
			
		if ( !$recursive ) {
			wfSuppressWarnings();
			$result = (bool)ftp_rmdir( $this->connection, $path );
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

		if ( $success && $this->exists( $path ) ) {
			wfSuppressWarnings();
			$ftp_rmdir = ftp_rmdir( $this->connection, $path );
			wfRestoreWarnings();

			if ( !$ftp_rmdir ) {
				$success = false;
			} 
		}
		
		return $success;
	}

	/**
	 * @see Filesystem::doCopy
	 */
	protected function doCopy( $from, $to ) {
		$content = $this->getContents( $from );
		
		if ( $content === false ) {
			return false;
		}
			
		return $this->writeToFile( $to, $content );		
	}

	/**
	 * @see Filesystem::doMove
	 */
	protected function doMove( $from, $to, $overwrite ) {
		wfSuppressWarnings();
		$result = (bool)ftp_rename( $this->connection, $from, $to );
		wfRestoreWarnings();		
		return $result;
	}

	/**
	 * @see Filesystem::exists
	 */
	public function exists( $file ) {
		wfSuppressWarnings();
		$list = ftp_nlist( $this->connection, $file );
		wfRestoreWarnings();		
		return !empty( $list );		
	}

	/**
	 * @see Filesystem::getChmod
	 */
	public function getChmod( $file ) {
		$dir = $this->listDir( $file );
		return $dir[$file]['permsn'];		
	}

	/**
	 * @see Filesystem::getContents
	 */
	public function getContents( $file ) {
		$type = FTP_BINARY;

		// TODO: port wp_tempnam
		$tempFileName = wp_tempnam( $file );
		$temp = fopen( $tempFileName , 'w+' );

		if ( !$temp ) {
			return false;
		}
			
		wfSuppressWarnings();
		$ftp_fget = ftp_fget( $this->connection, $temp, $file, $type );
		wfRestoreWarnings();		
		if ( !$ftp_fget ) {
			return false;
		}

		// Skip back to the start of the file being written to.
		fseek( $temp, 0 ); 
		
		$contents = array();

		while ( !feof( $temp ) ) {
			$contents[] = fread( $temp, 8192 );
		}

		fclose( $temp );
		unlink( $tempFileName );
		
		return implode( '', $contents );		
	}

	/**
	 * @see Filesystem::getCurrentWorkingDir
	 */
	public function getCurrentWorkingDir() {
		wfSuppressWarnings();
		$result = ftp_pwd( $this->connection );
		wfRestoreWarnings();		
		return $result;			
	}

	/**
	 * @see Filesystem::getGroup
	 */
	public function getGroup( $file ) {
		$dir = $this->listDir( $file );
		return $dir[$file]['group'];		
	}

	/**
	 * @see Filesystem::getModificationTime
	 */
	public function getModificationTime( $file ) {
		return ftp_mdtm( $this->connection, $file );
	}

	/**
	 * @see Filesystem::getOwner
	 */
	public function getOwner( $file ) {
		$dir = $this->listDir( $file );
		return $dir[$file]['owner'];		
	}

	/**
	 * @see Filesystem::getSize
	 */
	public function getSize( $file ) {
		return ftp_size( $this->connection, $file );
	}

	/**
	 * @see Filesystem::isDir
	 */
	public function isDir( $path ) {
		$cwd = $this->getCurrentWorkingDir();
		wfSuppressWarnings();
		$result = ftp_chdir( $this->connection, rtrim( $path, '/' ) . '/' );
		wfRestoreWarnings();
		
		if ( $result && $path == $this->getCurrentWorkingDir() || $this->getCurrentWorkingDir() != $cwd ) {
			wfSuppressWarnings();
			@ftp_chdir( $this->connection, $cwd );
			wfRestoreWarnings();
			return true;
		}
		
		return false;		
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