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
	
	public function __construct() {
		
	}	
	
	/**
	 * @see Filesystem::connect
	 */	
	public function connect() {
		return true;
	}
	
	// TODO
	
}