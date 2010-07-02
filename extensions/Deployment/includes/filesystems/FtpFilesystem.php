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
	public function changeDir($dir) {
		
	}

	/**
	 * @see Filesystem::changeFileGroup
	 */
	public function changeFileGroup($file, $group, $recursive = false) {
		
	}

	/**
	 * @see Filesystem::chmod
	 */
	public function chmod($file, $mode = false, $recursive = false) {
		
	}

	/**
	 * @see Filesystem::chown
	 */
	public function chown($file, $owner, $recursive = false) {
		
	}

	/**
	 * @see Filesystem::delete
	 */
	public function delete($path, $recursive = false) {
		
	}

	/**
	 * @see Filesystem::doCopy
	 */
	protected function doCopy($from, $to) {
		
	}

	/**
	 * @see Filesystem::doMove
	 */
	protected function doMove($from, $to) {
		
	}

	/**
	 * @see Filesystem::exists
	 */
	public function exists($file) {
		
	}

	/**
	 * @see Filesystem::getChmod
	 */
	public function getChmod($file) {
		
	}

	/**
	 * @see Filesystem::getContents
	 */
	public function getContents() {
		
	}

	/**
	 * @see Filesystem::getCreationTime
	 */
	public function getCreationTime($file) {
		
	}

	/**
	 * @see Filesystem::getCurrentWorkingDir
	 */
	public function getCurrentWorkingDir() {
		
	}

	/**
	 * @see Filesystem::getGroup
	 */
	public function getGroup($file) {
		
	}

	/**
	 * @see Filesystem::getModificationTime
	 */
	public function getModificationTime($file) {
		
	}

	/**
	 * @see Filesystem::getOwner
	 */
	public function getOwner($file) {
		
	}

	/**
	 * @see Filesystem::getSize
	 */
	public function getSize($file) {
		
	}

	/**
	 * @see Filesystem::isDir
	 */
	public function isDir($path) {
		
	}

	/**
	 * @see Filesystem::isFile
	 */
	public function isFile($path) {
		
	}

	/**
	 * @see Filesystem::isReadable
	 */
	public function isReadable($file) {
		
	}

	/**
	 * @see Filesystem::isWritable
	 */
	public function isWritable($file) {
		
	}

	/**
	 * @see Filesystem::listDir
	 */
	public function listDir($path, $includeHidden = true, $recursive = false) {
		
	}

	/**
	 * @see Filesystem::makeDir
	 */
	public function makeDir($path, $chmod = false, $chown = false, $chgrp = false) {
		
	}

	/**
	 * @see Filesystem::touch
	 */
	public function touch($file, $time = 0, $atime = 0) {
		
	}

	/**
	 * @see Filesystem::writeToFile
	 */
	public function writeToFile($file, $contents) {
		
	}
	
}