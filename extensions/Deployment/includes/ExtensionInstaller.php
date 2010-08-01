<?php

/**
 * File holding the ExtensionInstaller class.
 * Based on the WordPress 3.0 class WP_Upgrader.
 *
 * @file ExtensionInstaller.php
 * @ingroup Deployment
 * @ingroup Installer
 *
 * @author Jeroen De Dauw
 */

/**
 * Class for Installing or upgrading MediaWiki extensions via the Filesystem Abstraction classes from a Zip file.
 * 
 * @author Jeroen De Dauw
 */
class ExtensionInstaller extends Installer {
	
	
	/**
	 * Constructor
	 */
	public function __construct() {
		// TODO
	}	
	
	/**
	 * Initiates the installation procedure.
	 */
	public function doInstallation() {
		
	}
	
	/**
	 * Downloads a package needed for the installation.
	 */
	protected function downloadPackage() {
		
	}
	
	/**
	 * Unpacks a package needed for the installation.
	 */
	protected function unpackPackage() {
		
	}
	
	/**
	 * Installs a package.
	 */
	protected function installPackage() {
		
	}

	/**
	 * @param unknown_type $msg
	 */
	public function showMessage($msg) {
		
	}

	/**
	 * @param unknown_type $status
	 */
	public function showStatusMessage($status) {
		
	}	
	
}