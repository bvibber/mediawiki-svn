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