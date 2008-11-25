<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

# The autoloader is loaded after LocalSettings.php, so we'll need to load
# that file manually
if ( !class_exists( 'SiteConfiguration' ) )
	require_once( "$IP/includes/SiteConfiguration.php" );

/**
 * Class that hold the configuration
 *
 * @ingroup Extensions
 */
class WebConfiguration extends SiteConfiguration {
	protected $mHandler;               // Configuration handler
	protected $mWiki;                  // Wiki name
	protected $mConf = array();        // Our array of settings
	protected $mOldSettings = array(); // Old settings (before applying our overrides)
	protected $mDefaults = array();    // Default values

	/**
	 * Construct a new object.
	 *
	 * @param string $path path to the directory that contains the configuration
	 *                     files
	 */
	public function __construct( $wiki = 'default' ) {
		global $wgConfigureHandler;
		$class = 'ConfigureHandler' . ucfirst( $wgConfigureHandler );
		$this->mHandler = new $class();
		$this->mWiki = $wiki;
	}

	/**
	 * Load the configuration from the conf-now.ser file in the $this->mDir
	 * directory
	 */
	public function initialise( $useCache = true ) {
		parent::initialise();
		$this->mConf = $this->mHandler->getCurrent( $useCache );

		# Restore first version of $this->settings if called a second time so
		# that it doesn't duplicate arrays
		if( !count( $this->mOldSettings ) )
			$this->mOldSettings = $this->settings;
		else
			$this->settings = $this->mOldSettings;
			
		# We'll need to invert the order of keys as SiteConfiguration uses
		# $settings[$setting][$wiki] and the extension uses $settings[$wiki][$setting]
		foreach ( $this->mConf as $site => $settings ) {
			if ( !is_array( $settings ) )
				continue;
			foreach ( $settings as $name => $val ) {
				if ( $name != '__includes' ) {
					# Merge if possible
					if ( isset( $this->settings[$name][$site] ) && is_array( $this->settings[$name][$site] ) && is_array( $val ) ) {
						$this->settings[$name][$site] = self::mergeArrays( $val, $this->settings[$name][$site] );
					}
					elseif ( isset( $this->settings[$name]["+$site"] ) && is_array( $this->settings[$name]["+$site"] ) && is_array( $val ) ) {
						$this->settings[$name]["+$site"] = self::mergeArrays( $val, $this->settings[$name]["+$site"] );
					}
					elseif ( isset( $this->settings["+$name"][$site] ) && is_array( $this->settings["+$name"][$site] ) && is_array( $val ) ) {
						$this->settings["+$name"][$site] = self::mergeArrays( $val, $this->settings["+$name"][$site] );
					}
					elseif ( isset( $this->settings["+$name"]["+$site"] ) && is_array( $this->settings["+$name"]["+$site"] ) && is_array( $val ) ) {
						$this->settings["+$name"]["+$site"] = self::mergeArrays( $val, $this->settings["+$name"]["+$site"] );
					}
					elseif ( isset( $this->settings["+$name"] ) && is_array( $val ) ) {
						$this->settings["+$name"][$site] = $val;
					}
					else {
						$this->settings[$name][$site] = $val;
					}
				}
			}
		}
	}

	/**
	 * extract settings for this wiki in $GLOBALS
	 */
	public function extract() {
		// Special case for manage.php maintenance script so that it can work
		// even if the current configuration is broken
		if ( defined( 'EXT_CONFIGURE_NO_EXTRACT' ) )
			return;

		// Include files before so that customized settings won't be overriden
		// by the default ones
		$this->includeFiles();

		list( $site, $lang ) = $this->siteFromDB( $this->mWiki );
		$rewrites = array( 'wiki' => $this->mWiki, 'site' => $site, 'lang' => $lang );
		$this->extractAllGlobals( $this->mWiki, $site, $rewrites );
	}

	public function getIncludedFiles() {
		if ( isset( $this->mConf[$this->mWiki]['__includes'] ) )
			return $this->mConf[$this->mWiki]['__includes'];
		else
			return array();
	}

	/**
	 * Include all extensions files of actived extensions
	 */
	public function includeFiles() {
		$includes = $this->getIncludedFiles();
		if ( !count( $includes ) )
			return;

		// Since the files should be included from the global scope, we'll need
		// to import that variabled in this function
		extract( $GLOBALS, EXTR_REFS );

		foreach ( $includes as $file ) {
			if ( file_exists( $file ) ) {
				require_once( $file );
			} else {
				trigger_error( __METHOD__ . ": required file $file doesn't exist", E_USER_WARNING );
			}
		}
	}

	/**
	 * Get the array representing the current configuration
	 *
	 * @param $wiki String: wiki name
	 * @return array
	 */
	public function getCurrent( $wiki ) {
		list( $site, $lang ) = $this->siteFromDB( $wiki );
		$rewrites = array( 'wiki' => $wiki, 'site' => $site, 'lang' => $lang );
		return $this->getAll( $wiki, $site, $rewrites );
	}

	/**
	 * Return the old configuration from $ts timestamp
	 * Does *not* return site specific settings but *all* settings
	 *
	 * @param $ts timestamp
	 * @return array
	 */
	public function getOldSettings( $ts ) {
		return $this->mHandler->getOldSettings( $ts );
	}

	/**
	 * Returns the wikis in $ts version
	 *
	 * @param $ts timestamp
	 * @return array
	 */
	public function getWikisInVersion( $ts ) {
		return $this->mHandler->getWikisInVersion( $ts );
	}

	/**
	 * Returns a pager for this handler
	 *
	 * @return Pager
	 */
	public function getPager() {
		return $this->mHandler->getPager();
	}

	/**
	 * Get the defalut values for all settings
	 * Very, very hacky...
	 *
	 * @return array
	 */
	public function getDefaults() {
		if ( count( $this->mDefaults ) )
			return $this->mDefaults;

		global $IP;
		require( "$IP/includes/DefaultSettings.php" );
		foreach ( get_defined_vars() as $name => $var ) {
			if ( substr( $name, 0, 2 ) == 'wg' && $name != 'wgConf' )
				$this->mDefaults[$name] = $var;
		}
		return $this->mDefaults;
	}

	/**
	 * Get the default settings (i.e. before apply Configure's overrides)
	 * Very hacky too...
	 *
	 * @param $wiki String
	 * @return array
	 */
	public function getDefaultsForWiki( $wiki ) {
		// Hmm, a better solution would be nice!
		$savedSettings = $this->settings;
		$this->settings = $this->mOldSettings;
		$globalDefaults = $this->getDefaults();

		$savedGlobals = array();
		foreach ( $this->settings as $name => $val ) {
			if ( substr( $name, 0, 1 ) == '+' ) {
				$setting = substr( $name, 1 );
				if ( isset( $globalDefaults[$setting] ) ) {
					$savedGlobals[$setting] = $GLOBALS[$setting];
					$GLOBALS[$setting] = $globalDefaults[$setting];
				}
			}
		}

		$wikiDefaults = $this->getCurrent( $wiki );

		$this->settings = $savedSettings;
		unset( $savedSettings );
		foreach ( $savedGlobals as $name => $val ) {
			$GLOBALS[$setting] = $savedGlobals[$setting];
		}

		$ret = array();
		$keys = array_unique( array_merge( array_keys( $wikiDefaults ), array_keys( $globalDefaults ) ) );
		foreach ( $keys as $setting ) {
			if ( isset( $wikiDefaults[$setting] ) && !is_null( $wikiDefaults[$setting] ) )
				$ret[$setting] = $wikiDefaults[$setting];
			elseif ( isset( $globalDefaults[$setting] ) )
				$ret[$setting] = $globalDefaults[$setting];
		}
		return $ret;
	}

	/**
	 * Save a new configuration
	 * @param $settings array of settings
	 * @param $wiki String: wiki name or false to use the current one
	 * @return bool true on success
	 */
	public function saveNewSettings( $settings, $wiki = false ) {
		if ( !is_array( $settings ) || $settings === array() )
			# hmmm
			return false;

		if ( $wiki === null ) {
			$this->mConf = $settings;
			$wiki = true;
		} else {
			if ( $wiki === false )
				$wiki = $this->getWiki();
			$this->mConf[$wiki] = $settings;
		}

		return $this->mHandler->saveNewSettings( $this->mConf, $wiki );
	}

	/**
	 * List all archived files that are like conf-{$ts}.ser
	 * @return array of timestamps
	 */
	public function listArchiveVersions() {
		return $this->mHandler->listArchiveVersions();
	}
	
	public function getArchiveVersions() {
		return $this->mHandler->getArchiveVersions();
	}

	/**
	 * Do some checks
	 */
	public function doChecks() {
		return $this->mHandler->doChecks();
	}

	/**
	 * Get not editable settings with the current handler
	 * @return array
	 */
	public function getUneditableSettings() {
		return $this->mHandler->getUneditableSettings();
	}

	/**
	 * Get the wiki in use
	 *
	 * @return String
	 */
	public function getWiki() {
		return $this->mWiki;
	}

	/**
	 * Get the configuration handler
	 * @return ConfigurationHandler
	 */
	public function getHandler() {
		return $this->mHandler;
	}

	/**
	 * Merge array settings
	 * TODO: document!
	 * @return Array
	 */
	public static function mergeArrays( /* $array1, ... */ ) {
		$args = func_get_args();
		$canAdd = true;
		foreach ( $args as $arr ) {
			if ( $arr !== array_values( $arr ) ) {
				$canAdd = false;
				break;
			}
		}

		$out = array_shift( $args );
		foreach ( $args as $arr ) {
			foreach ( $arr as $key => $value ) {
				if ( isset( $out[$key] ) && is_array( $out[$key] ) && is_array( $value ) ) {
					$out[$key] = self::mergeArrays( $out[$key], $value );
				} elseif ( $canAdd ) {
					$out[] = $value;
				} else {
					$out[$key] = $value;
				}
			}
		}
		return $out;
	}
}
