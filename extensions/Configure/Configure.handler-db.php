<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Class that hold the configuration
 *
 * @ingroup Extensions
 */
class ConfigureHandlerDb implements ConfigureHandler {
	protected $mDb; // Database name

	/**
	 * Construct a new object.
	 *
	 * @param string $path path to the directory that contains the configuration
	 *                     files
	 */
	public function __construct(){
		global $IP, $wgConfigureDatabase;
		require_once( "$IP/includes/GlobalFunctions.php" );
		require_once( "$IP/includes/ObjectCache.php" );
		$this->mDb = $wgConfigureDatabase;
	}

	/**
	 * Get a slave DB connection, used for reads
	 * @return Database object
	 */
	public function getSlaveDB() {
		return wfGetDB( DB_SLAVE, 'config', $this->mDb );	
	}

	/**
	 * Get a master DB connection, used for writes
	 * @return Database object
	 */
	public function getMasterDB() {
		return wfGetDB( DB_MASTER, 'config', $this->mDb );	
	}

	/**
	 * Get cache key
	 */
	protected function cacheKey( /* ... */ ){
		$args = func_get_args();
		$args = array_merge( wfSplitWikiID( $this->mDb ), $args );
		return call_user_func_array( 'wfForeignMemcKey', $args );
	}

	/**
	 * Returns a cache object
	 */
	protected function getCache() {
		return wfGetMainCache();
	}

	/**
	 * Load the current configuration the database (i.e. cv_is_latest == 1)
	 * directory
	 */
	public function getCurrent(){
		$cacheKey = $this->cacheKey( 'configure', 'current' );
		$cached = $this->getCache()->get( $cacheKey );
		if( is_array( $cached ) ){
			#var_dump( $cached ); die;
			return $cached;
		} else {
			#var_dump( $this->getCache() ); die;
		}
		
		try {
			$dbr = $this->getSlaveDB();
			$ret = $dbr->select(
				array( 'config_setting', 'config_version' ),
				array( 'cs_name', 'cv_wiki', 'cs_value' ),
				array( 'cv_is_latest' => 1 ),
				__METHOD__,
				array(),
				array( 'config_version' => array( 'LEFT JOIN', 'cs_id = cv_id' ) )
			);
			$arr = array();
			foreach( $ret as $row ){
				$arr[$row->cv_wiki][$row->cs_name] = unserialize( $row->cs_value );
			}
			$this->getCache()->set( $cacheKey, $arr, 3600 );
			return $arr;
		} catch( MWException $e ) {
			return array();
		}
	}

	/**
	 * Return the old configuration from $ts
	 * Does *not* return site specific settings but *all* settings
	 *
	 * @param $ts timestamp
	 * @return array
	 */
	public function getOldSettings( $ts ){
		$db = $this->getSlaveDB();
		$ret = $db->select(
			array( 'config_setting', 'config_version' ),
			array( 'cs_name', 'cv_wiki', 'cs_value' ),
			array( 'cv_timestamp' => $ts ),
			__METHOD__,
			array(),
			array( 'config_version' => array( 'LEFT JOIN', 'cs_id = cv_id' ) )
		);
		$arr = array();
		foreach( $ret as $row ){
			$arr[$row->cv_wiki][$row->cs_name] = unserialize( $row->cs_value );
		}
		return $arr;
	}

	/**
	 * Returns the wikis in $ts version
	 *
	 * @param $ts timestamp
	 * @return array
	 */
	public function getWikisInVersion( $ts ){
		$wiki = $this->getSlaveDB()->selectField( 'config_version', 'cv_wiki', array( 'cv_timestamp' => $ts ), __METHOD__ );
		if( $wiki === false )
			return array();
		return array( $wiki );
	}

	/**
	 * Returns a pager for this handler
	 *
	 * @return Pager
	 */
	public function getPager(){
		return new ConfigurationPagerDb( $this );
	}

	/**
	 * Save a new configuration
	 * @param $settings array of settings
	 * @param $wiki String: wiki name or true for all
	 * @param $ts 
	 * @return bool true on success
	 */
	public function saveNewSettings( $settings, $wiki, $ts = false ){
		if( $wiki === true ){
			foreach( $settings as $name => $val ){
				$this->saveSettingsForWiki( $val, $name, $ts );
			}
		} else {
			if( !isset( $settings[$wiki] ) )
				return false;
			$this->saveSettingsForWiki( $settings[$wiki], $wiki, $ts );
		}
		$this->getCache()->delete( $this->cacheKey( 'configure', 'current' ) );
		return true;
	}

	/**
	 * save the configuration for $wiki
	 */
	protected function saveSettingsForWiki( $settings, $wiki, $ts ){
		$dbw = $this->getMasterDB();
		if( !$ts )
			$ts = wfTimestampNow();
		$dbw->begin();
		$dbw->insert( 'config_version',
			array(
				'cv_wiki' => $wiki,
				'cv_timestamp' => $ts,
				'cv_is_latest' => 1,
			),
			__METHOD__
		);
		$newId = $dbw->insertId();
		$dbw->update( 'config_version', array( 'cv_is_latest' => 0 ), array( 'cv_wiki' => $wiki, 'cv_timestamp <> '.$dbw->addQuotes( $ts ) ), __METHOD__ );
		$insert = array();
		foreach( $settings as $name => $val ){
			$insert[] = array(
				'cs_id' => $newId,
				'cs_name' => $name,
				'cs_value' => serialize( $val ),
			);
		}
		$dbw->insert( 'config_setting', $insert, __METHOD__ );
		$dbw->commit();
		return true;
	}

	/**
	 * List all archived versions
	 * @return array of timestamps
	 */
	public function listArchiveVersions(){
		$db = $this->getSlaveDB();
		$ret = $db->select(
			array( 'config_version' ),
			array( 'cv_timestamp' ),
			array(),
			__METHOD__,
			array( 'ORDER BY' => 'cv_timestamp DESC' )
		);
		$arr = array();
		foreach( $ret as $row ){
			$arr[] = $row->cv_timestamp;
		}
		return $arr;
	}

	/**
	 * Do some checks
	 */
	public function doChecks(){
		try {
			$dbw = $this->getMasterDB();
		} catch( MWException $e ) {
			return array( 'configure-db-error', $this->mDb );
		}
		if( !$dbw->tableExists( 'config_version' ) )
			return array( 'configure-db-table-error' );
		return array();
	}
	
	/**
	 * Get settings that are not editable with the database handler
	 */
	public function getNotEditableSettings(){
		return array(
		# Database
			'wgAllDBsAreLocalhost',
			'wgCheckDBSchema',
			'wgDBAvgStatusPoll',
			'wgDBerrorLog',
			'wgDBname',
			'wgDBpassword',
			'wgDBport',
			'wgDBserver',
			'wgDBtype',
			'wgDBuser',
			'wgLegacySchemaConversion',
			'wgSharedDB',
			'wgSharedPrefix',
			'wgSharedTables',
			'wgDBClusterTimeout',
			'wgDBservers',
			'wgLBFactoryConf',
			'wgMasterWaitTimeout',
			'wgDBmysql5',
			'wgDBprefix',
			'wgDBTableOptions',
			'wgDBtransactions',
			'wgDBmwschema',
			'wgDBts2schema',
			'wgSQLiteDataDir',
		# Memcached
			'wgMainCacheType',
			'wgMemCachedDebug',
			'wgMemCachedPersistent',
			'wgMemCachedServers',
		);	
	}
}
