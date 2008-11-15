<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Interface for configuration handler
 *
 * @ingroup Extensions
 */
interface ConfigureHandler {

	/**
	 * Construct a new object.
	 */
	public function __construct();

	/**
	 * Load the current configuration
	 */
	public function getCurrent( $useCache = true );

	/**
	 * Return the old configuration from $ts timestamp
	 * Does *not* return site specific settings but *all* settings
	 *
	 * @param $ts timestamp
	 * @return array
	 */
	public function getOldSettings( $ts );

	/**
	 * Returns the wikis in $ts version
	 *
	 * @param $ts timestamp
	 * @return array
	 */
	public function getWikisInVersion( $ts );

	/**
	 * Returns a pager for this handler
	 *
	 * @return Pager
	 */
	public function getPager();

	/**
	 * Save a new configuration
	 * @param $settings array of settings
	 * @param $wiki String: wiki name or false to use the current one
	 * @return bool true on success
	 */
	public function saveNewSettings( $settings, $wiki );

	/**
	 * List all archived versions
	 * @return array of timestamps
	 */
	public function listArchiveVersions();

	/**
	 * Do some checks
	 * @return array
	 */
	public function doChecks();

	/**
	 * Get settings that are not editable with this handler
	 * @return array
	 */
	public function getNotEditableSettings();
}
