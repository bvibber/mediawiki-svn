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
			$this->connection = @ftp_ssl_connect( $this->options['hostname'], $this->options['port'], $this->options['timeout'] );
		}
		else {
			// If this is true, ftp_ssl_connect was not defined, so add an error.
			if ( $this->options['ssl'] ) {
				$this->addError( 'deploy-ftp-ssl-not-loaded' );
			}
			
			$this->connection = @ftp_connect( $this->options['hostname'], $this->options['port'], $this->options['timeout'] );
		}
		
		// Check if a connection has been established.
		if ( !$this->connection ) {
			$this->addErrorMessage( wfMsgExt( 'deploy-ftp-connect-failed', $this->options['hostname'], $this->options['port'] ) );
			return false;
		}
		
		// Attempt to set the connection to use passive FTP.
		@ftp_pasv( $this->connection, true );
		
		// Make sure the timeout is at least as much as the option.
		if ( @ftp_get_option( $this->connection, FTP_TIMEOUT_SEC ) < $this->options['timeout'] ) {
			@ftp_set_option( $this->connection, FTP_TIMEOUT_SEC, $this->options['timeout'] );
		}
		
		return true;
	}
	
	/**
	 * @see Filesystem::changeDir
	 */
	public function changeDir( $dir ) {
		return @ftp_chdir( $this->connection, $dir );
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
		
	}

	/**
	 * @see Filesystem::chown
	 */
	public function chown( $file, $owner, $recursive = false ) {
		
	}

	/**
	 * @see Filesystem::delete
	 */
	public function delete( $path, $recursive = false ) {
		
	}

	/**
	 * @see Filesystem::doCopy
	 */
	protected function doCopy( $from, $to ) {
		
	}

	/**
	 * @see Filesystem::doMove
	 */
	protected function doMove( $from, $to ) {
		
	}

	/**
	 * @see Filesystem::exists
	 */
	public function exists( $file ) {
		
	}

	/**
	 * @see Filesystem::getChmod
	 */
	public function getChmod( $file ) {
		
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