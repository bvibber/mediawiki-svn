<?php

/**
 * File holding the DeployInstaller class.
 * Based on the WordPress 3.0 class WP_Upgrader.
 *
 * @file Installer.php
 * @ingroup Deployment
 * @ingroup Installer
 *
 * @author Jeroen De Dauw
 */

/**
 * This documenation group collects source code files with Installer related features.
 *
 * @defgroup Installer Installer
 */

/**
 * Class for Installing or upgrading a local set of files via the Filesystem Abstraction classes from a Zip file.
 * 
 * @author Jeroen De Dauw
 */
abstract class DeployInstaller {
	
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
	
}