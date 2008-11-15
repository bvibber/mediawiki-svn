<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Class that hold the configuration
 *
 * @ingroup Extensions
 */
class ConfigureHandlerFiles implements ConfigureHandler {
	protected $mDir; // Directory of files, *with* leading /

	/**
	 * Construct a new object.
	 */
	public function __construct() {
		global $wgConfigureFilesPath;
		if ( $wgConfigureFilesPath === null ) {
			global $IP;
			$wgConfigureFilesPath = "$IP/serialized/";
		} else if ( substr( $wgConfigureFilesPath, -1 ) != '/' && substr( $wgConfigureFilesPath, -1 ) != '\\' ) {
			$wgConfigureFilesPath .= '/';
		}
		$this->mDir = $wgConfigureFilesPath;
	}

	/**
	 * Load the configuration from the conf-now.ser file in the $this->mDir
	 * directory
	 */
	public function getCurrent( $useCache = true ) {
		$file = $this->getFileName();
		if ( !file_exists( $file ) )
			# maybe the first time the user use this extensions, do not override
			# anything
			return array();
		$cont = @file_get_contents( $file );
		if ( empty( $cont ) )
			# Weird, should not happen
			return array();
		$arr = unserialize( $cont );
		if ( !is_array( $arr ) )
			# Weird, should not happen too
			return array();
		return $arr;
	}

	/**
	 * Return the configuration from the conf-{$ts}.ser file in the $this->mDir
	 * Does *not* return site specific settings but *all* settings
	 *
	 * @param $ts timestamp
	 * @return array
	 */
	public function getOldSettings( $ts ) {
		$file = $this->getArchiveFileName( $ts );
		if ( !file_exists( $file ) )
			# maybe the time the user use this extensions, do not override
			# anything
			return array();
		$cont = @file_get_contents( $file );
		if ( empty( $cont ) )
			# Weird, should not happen
			return array();
		$arr = unserialize( $cont );
		if ( !is_array( $arr ) )
			# Weird, should not happen too
			return array();
		return $arr;
	}

	/**
	 * Returns the wikis in $ts version
	 *
	 * @param $ts timestamp
	 * @return array
	 */
	public function getWikisInVersion( $ts ) {
		$settings = $this->getOldSettings( $ts );
		return array_keys( $settings );
	}

	/**
	 * Returns a pager for this handler
	 *
	 * @return Pager
	 */
	public function getPager() {
		return new ConfigurationPagerFiles( $this );
	}

	/**
	 * Save a new configuration
	 * @param $settings array of settings
	 * @param $wiki String: wiki name or true for all
	 * @return bool true on success
	 */
	public function saveNewSettings( $settings, $wiki ) {
		$arch = $this->getArchiveFileName();
		$cur = $this->getFileName();
		$cont = serialize( $settings );
		@file_put_contents( $arch, $cont );
		return ( @file_put_contents( $cur, $cont ) !== false );
	}

	/**
	 * List all archived files that are like conf-{$ts}.ser
	 * @return array of timestamps
	 */
	public function listArchiveVersions() {
		if ( !$dir = opendir( $this->mDir ) )
			return array();
		$files = array();
		while ( ( $file = readdir( $dir ) ) !== false ) {
			if ( preg_match( '/^conf-(\d{14})\.ser$/', $file, $m ) )
				$files[] = $m[1];
		}
		rsort( $files, SORT_NUMERIC );
		return $files;
	}

	/**
	 * Do some checks
	 */
	public function doChecks() {
		// Check that the directory exists...
		if ( !is_dir( $this->getDir() ) ) {
			return array( 'configure-no-directory', $this->getDir() );
		}

		// And that it's writable by PHP
		if ( !is_writable( $this->getDir() ) ) {
			return array( 'configure-directory-not-writable', $this->getDir() );
		}

		return array();
	}

	/**
	 * All settings are editable!
	 */
	public function getNotEditableSettings() {
		return array();
	}

	/**
	 * Get the current file name
	 * @return String full path to the file
	 */
	protected function getFileName() {
		return "{$this->mDir}conf-now.ser";
	}

	/**
	 * Get the an archive file
	 * @param $ts String: 14 char timestamp (YYYYMMDDHHMMSS) or null to use the
	 *            current timestamp
	 * @return String full path to the file
	 */
	public function getArchiveFileName( $ts = null ) {
		if ( $ts === null )
			$ts = wfTimestampNow();

		$file = "{$this->mDir}conf-$ts.ser";
		return $file;
	}

	/**
	 * Get the directory used to store the files
	 *
	 * @return String
	 */
	public function getDir() {
		return $this->mDir;
	}
}
