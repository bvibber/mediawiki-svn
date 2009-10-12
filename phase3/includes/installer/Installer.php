<?php

/**
 * Base installer class
 * Handles everything that is independent of user interface
 */
abstract class Installer {
	var $settings, $output;

	/**
	 * MediaWiki configuration globals that will eventually be passed through 
	 * to LocalSettings.php. The names only are given here, the defaults 
	 * typically come from DefaultSettings.php.
	 * @protected
	 */
	var $defaultVarNames = array(
		'wgSitename',
		'wgPasswordSender',
		'wgLanguageCode', 
		'wgRightsIcon',
		'wgRightsText',
		'wgRightsUrl',
		'wgMainCacheType',
		'wgEnableEmail', 
		'wgDBtype',
		'wgDiff3',
		'wgImageMagickConvertCommand',
		'IP',
		'wgScriptPath',
		'wgScriptExtension',
		'wgMetaNamespace',
//		'wgDeletedDirectory',
		'wgEnableUploads',
		'wgLogo',
	);

	/**
	 * Variables that are stored alongside globals, and are used for any 
	 * configuration of the installation process aside from the MediaWiki 
	 * configuration. Map of names to defaults.
	 * @protected
	 */
	var $internalDefaults = array(
		'_UserLang' => 'en',
		'_Environment' => false,
		'_CompiledDBs' => array(),
		'_SafeMode' => false,
		'_RaiseMemory' => false,
		'_UpgradeDone' => false,
		'_Caches' => array(),
		'_InstallUser' => 'root',
		'_InstallPassword' => '',
		'_SameAccount' => true,
		'_CreateDBAccount' => false,
		'_NamespaceType' => 'site-name',
		'_AdminName' => '',
		'_AdminPassword' => '',
		'_AdminPassword2' => '',
		'_AdminEmail' => '',
		'_Subscribe' => false,
		'_SkipOptional' => 'continue',
		'_RightsProfile' => 'wiki',
		'_LicenseCode' => 'none',
		'_CCDone' => false,
	);

	/**
	 * Known database types. These correspond to the class names <type>_Installer,
	 * and are also MediaWiki database types valid for $wgDBtype.
	 *
	 * To add a new type, create a <type>_Installer class and a Database<type> 
	 * class, and add a config-type-<type> message to MessagesEn.php.
	 * @private
	 */
	var $dbTypes = array(
		'mysql',
		'postgres',
		'sqlite'
	);

	/**
	 * Minimum memory size in MB
	 */
	private $minMemorySize = 50;

	/**
	 * Cached DB installer instances, access using getDBInstaller()
	 * @private
	 */
	var $dbInstallers = array();

	/**
	 * A list of environment check methods called by doEnvironmentChecks(). 
	 * These may output warnings using showMessage(), and/or abort the 
	 * installation process by returning false.
	 * @protected
	 */
	var $envChecks = array( 
		'envLatestVersion',
		'envCheckDB', 
		'envCheckRegisterGlobals', 
		'envCheckMagicQuotes', 
		'envCheckMagicSybase',
		'envCheckMbstring', 
		'envCheckZE1',
		'envCheckSafeMode',
		'envCheckXML',
		'envCheckPCRE',
		'envCheckMemory',
		'envCheckCache',
		'envCheckDiff3',
		'envCheckGraphics',
		'envCheckPath',
		'envCheckExtension',
	);

	/**
	 * Known object cache types and the functions used to test for their existence
	 * @protected
	 */
	var $objectCaches = array( 
		'xcache' => 'xcache_get',
		'apc' => 'apc_fetch',
		'eaccel' => 'eaccelerator_get'
	);

	/**
	 * User rights profiles
	 */
	var $rightsProfiles = array(	
		'wiki' => array(),
		'no-anon' => array(
			'*' => array( 'edit' => false )
		),
		'fishbowl' => array(
			'*' => array( 
				'createaccount' => false,
				'edit' => false,
			),
		),
		'private' => array(
			'*' => array(
				'createaccount' => false,
				'edit' => false,
				'read' => false,
			),
		),
	);

	/**
	 * License types
	 */
	var $licenses = array(
		'none' => array(
			'url' => '',
			'icon' => '',
			'text' => ''
		),
		'pd' => array(
			'url' => 'http://creativecommons.org/licenses/publicdomain/',
			'icon' => '${wgScriptPath}/skins/common/images/public-domain.png',
		),
		'gfdl-old' => array(
			'url' => 'http://www.gnu.org/licenses/old-licenses/fdl-1.2.html',
			'icon' => '${wgScriptPath}/skins/common/images/gnu-fdl.png',
		),
		'gfdl-current' => array(
			'url' => 'http://www.gnu.org/copyleft/fdl.html',
			'icon' => '${wgScriptPath}/skins/common/images/gnu-fdl.png',
		),
		'cc-choose' => array(
			// details will be filled in by the selector
			'url' => '', 
			'icon' => '',
			'text' => '',
		),
	);
	/**
	 * Cached Title and ParserOptions used by parse()
	 * @private
	 */
	var $parserTitle, $parserOptions;

	/**
	 * Constructor, always call this from child classes
	 */
	function __construct() {
		$this->settings = $this->internalDefaults;
		foreach ( $this->defaultVarNames as $var ) {
			$this->settings[$var] = $GLOBALS[$var];
		}
		foreach ( $this->dbTypes as $type ) {
			$installer = $this->getDBInstaller( $type );
			if ( !$installer->isCompiled() ) {
				continue;
			}
			$defaults = $installer->getGlobalDefaults();
			foreach ( $installer->getGlobalNames() as $var ) {
				if ( isset( $defaults[$var] ) ) {
					$this->settings[$var] = $defaults[$var];
				} else {
					$this->settings[$var] = $GLOBALS[$var];
				}
			}
		}

		$this->parserTitle = Title::newFromText( 'Installer' );
		$this->parserOptions = new ParserOptions;
		$this->parserOptions->setEditSection( false );
	}

	/**
	 * UI interface for displaying a short message
	 * The parameters are like parameters to wfMsg().
	 * The messages will be in wikitext format, which will be converted to an 
	 * output format such as HTML or text before being sent to the user.
	 */
	abstract function showMessage( $msg /*, ... */ );

	/**
	 * Get a list of known DB types
	 */
	function getDBTypes() {
		return $this->dbTypes;
	}

	/**
	 * Get an instance of InstallerDBType for the specified DB type
	 */
	function getDBInstaller( $type ) {
		if ( !isset( $this->dbInstallers[$type] ) ) {
			$class = ucfirst( $type ). 'Installer';
			$this->dbInstallers[$type] = new $class( $this );
		}
		return $this->dbInstallers[$type];
	}

	/**
	 * Do initial checks of the PHP environment. Set variables according to 
	 * the observed environment.
	 *
	 * It's possible that this may be called under the CLI SAPI, not the SAPI
	 * that the wiki will primarily run under. In that case, the subclass should
	 * initialise variables such as wgScriptPath, before calling this function.
	 *
	 * Under the web subclass, it can already be assumed that PHP 5+ is in use 
	 * and that sessions are working.
	 */
	function doEnvironmentChecks() {
		$this->showMessage( 'config-env-php', phpversion() );

		$good = true;
		foreach ( $this->envChecks as $check ) {
			$status = $this->$check();
			if ( $status === false ) {
				$good = false;
			}
		}
		$this->setVar( '_Environment', $good );
		if ( $good ) {
			$this->showMessage( 'config-env-good' );
		} else {
			$this->showMessage( 'config-env-bad' );
		}
		return $good;
	}

	/**
	 * Get an MW configuration variable, or internal installer configuration variable.
	 * The defaults come from $GLOBALS (ultimately DefaultSettings.php).
	 * Installer variables are typically prefixed by an underscore.
	 */
	function getVar( $name, $default = null ) {
		if ( !isset( $this->settings[$name] ) ) {
			return $default;
		} else {
			return $this->settings[$name];
		}
	}

	/**
	 * Set a MW configuration variable, or internal installer configuration variable.
	 */
	function setVar( $name, $value ) {
		$this->settings[$name] = $value;
	}

	/**
	 * Get a fake password for sending back to the user in HTML.
	 * This is a security mechanism to avoid compromise of the password in the
	 * event of session ID compromise.
	 */
	function getFakePassword( $realPassword ) {
		return str_repeat( '*', strlen( $realPassword ) );
	}

	/**
	 * Set a variable which stores a password, except if the new value is a 
	 * fake password in which case leave it as it is.
	 */
	function setPassword( $name, $value ) {
		if ( !preg_match( '/^\*+$/', $value ) ) {
			$this->setVar( $name, $value );
		}
	}

	/**
	 * Returns true if dl() can be used
	 */
	function haveDl() {
		return function_exists( 'dl' )
			&& is_callable( 'dl' )
			&& wfIniGetBool( 'enable_dl' )
			&& !wfIniGetBool( 'safe_mode' );
	}
	
	/** Check if we're installing the latest version */
	function envLatestVersion() {
		global $wgVersion;
		$latestInfo = Http::get( 'http://www.mediawiki.org/w/api.php?action=mwreleases&format=php' );
		if( !$latestInfo ) {
			return;
		}
		$latestInfo = unserialize($latestInfo);
		foreach( $latestInfo['mwreleases'] as $rel ) {
			if( isset( $rel['current'] ) )
				$currentVersion = $rel['version'];
		}
		if( version_compare( $wgVersion, $currentVersion, '<' ) ) {
			$this->showMessage( 'config-env-latest-old' );
			$this->showHelpBox( 'config-env-latest-help', $wgVersion, $currentVersion ); 
		} elseif( version_compare( $wgVersion, $currentVersion, '>' ) ) {
			$this->showMessage( 'config-env-latest-new' );
		}
		$this->showMessage( 'config-env-latest-ok' );
	}

	/** Environment check for DB types */
	function envCheckDB() {
		$compiledDBs = array();
		$haveDl = $this->haveDl();
		$goodNames = array();
		$allNames = array();
		foreach ( $this->dbTypes as $name ) {
			$db = $this->getDBInstaller( $name );
			$readableName = wfMsg( 'config-type-' . $name );
			if ( $db->isCompiled() ) {
				$compiledDBs[$name] = true;
				$goodNames[] = $readableName;
			}
			$allNames[] = $readableName;
		}
		$this->setVar( '_CompiledDBs', $compiledDBs );

		global $wgLang;
		if ( !$compiledDBs ) {
			$this->showMessage( 'config-no-db' );
			$this->showHelpBox( 'config-no-db-help', $wgLang->commaList( $allNames ) );
			return false;
		}
		$this->showMessage( 'config-have-db', $wgLang->commaList( $goodNames ) );
	}

	/** Environment check for register_globals */
	function envCheckRegisterGlobals() {
		if( wfIniGetBool( "magic_quotes_runtime" ) ) {
			$this->showMessage( 'config-register-globals' );
		}
	}

	/** Environment check for magic_quotes_runtime */
	function envCheckMagicQuotes() {
		if( wfIniGetBool( "magic_quotes_runtime" ) ) {
			$this->showMessage( 'config-magic-quotes-runtime' );
			return false;
		}
	}

	/** Environment check for magic_quotes_sybase */
	function envCheckMagicSybase() {
		if ( wfIniGetBool( 'magic_quotes_sybase' ) ) {
			$this->showMessage( 'config-magic-quotes-sybase' );
			return false;
		}
	}

	/* Environment check for mbstring.func_overload */
	function envCheckMbstring() {
		if ( wfIniGetBool( 'mbstring.func_overload' ) ) {
			$this->showMessage( 'config-mbstring' );
			return false;
		}
	}

	/** Environment check for zend.ze1_compatibility_mode */
	function envCheckZE1() {
		if ( wfIniGetBool( 'zend.ze1_compatibility_mode' ) ) {
			$this->showMessage( 'config-ze1' );
			return false;
		}
	}

	/** Environment check for safe_mode */
	function envCheckSafeMode() {
		if ( wfIniGetBool( 'safe_mode' ) ) {
			$this->setVar( '_SafeMode', true );
			$this->showMessage( 'config-safe-mode' );
		}
	}

	/** Environment check for the XML module */
	function envCheckXML() {
		if ( !function_exists( "utf8_encode" ) ) {
			$this->showMessage( 'config-xml-bad' );
			return false;
		}
		$this->showMessage( 'config-xml-good' );
	}

	/** Environment check for the PCRE module */
	function envCheckPCRE() {
		if ( !function_exists( 'preg_match' ) ) {
			$this->showMessage( 'config-pcre' );
			return false;
		}
	}

	/** Environment check for available memory */
	function envCheckMemory() {
		$limit = ini_get( 'memory_limit' );
		if ( !$limit || $limit == -1 ) {
			$this->showMessage( 'config-memory-none' );
			return true;
		}
		$n = intval( $limit );
		if( preg_match( '/^([0-9]+)[Mm]$/', trim( $limit ), $m ) ) {
			$n = intval( $m[1] * (1024*1024) );
		}
		if( $n < $this->minMemorySize*1024*1024 ) {
			$newLimit = "{$this->minMemorySize}M";
			if( false === ini_set( "memory_limit", $newLimit ) ) {
				$this->showMessage( 'config-memory-bad', $limit );
			} else {
				$this->showMessage( 'config-memory-raised', $limit, $newLimit );
				$this->setVar( '_RaiseMemory', true );
			}
		} else {
			$this->showMessage( 'config-memory-ok', $limit );
		}
	}

	/** Environment check for compiled object cache types */
	function envCheckCache() {
		$caches = array();
		foreach ( $this->objectCaches as $name => $function ) {
			if ( function_exists( $function ) ) {
				$caches[$name] = true;
				$this->showMessage( 'config-' . $name );
			}
		}
		if ( !$caches ) {
			$this->showMessage( 'config-no-cache' );
		}
		$this->setVar( '_Caches', $caches );
	}

	/** Search for GNU diff3 */
	function envCheckDiff3() {
		$paths = array_merge(
			array(
				"/usr/bin",
				"/usr/local/bin",
				"/opt/csw/bin",
				"/usr/gnu/bin",
				"/usr/sfw/bin" ),
			explode( PATH_SEPARATOR, getenv( "PATH" ) ) );
		$names = array( "gdiff3", "diff3", "diff3.exe" );

		$versionInfo = array( '$1 --version 2>&1', 'diff3 (GNU diffutils)' );
		$haveDiff3 = false;
		foreach ( $paths as $path ) {
			$exe = $this->locateExecutable( $path, $names, $versionInfo );
			if ($exe !== false) {
				$this->setVar( 'wgDiff3', $exe );
				$haveDiff3 = true;
				break;
			}
		}
		if ( $haveDiff3 ) {
			$this->showMessage( 'config-diff3-good', $exe );
		} else {
			$this->setVar( 'wgDiff3', false );
			$this->showMessage( 'config-diff3-bad' );
		}
	}

	/**
	 * Search a path for any of the given executable names. Returns the 
	 * executable name if found. Also checks the version string returned 
	 * by each executable
	 *
	 * @param string $path Path to search
	 * @param array $names Array of executable names
	 * @param string $versionInfo Array with two members:
	 *       0 => Command to run for version check, with $1 for the path
	 *       1 => String to compare the output with
	 *
	 * If $versionInfo is not false, only executables with a version 
	 * matching $versionInfo[1] will be returned.
	 */
	function locateExecutable( $path, $names, $versionInfo = false ) {
		if (!is_array($names))
			$names = array($names);

		foreach ($names as $name) {
			$command = "$path/$name";
			if ( @file_exists( $command ) ) {
				if ( !$versionInfo )
					return $command;

				$file = str_replace( '$1', $command, $versionInfo[0] );
				if ( strstr( wfShellExec( $file ), $versionInfo[1]) !== false )
					return $command;
			}
		}
		return false;
	}

	/** Environment check for ImageMagick and GD */
	function envCheckGraphics() {
		$imcheck = array( "/usr/bin", "/opt/csw/bin", "/usr/local/bin", "/sw/bin", "/opt/local/bin" );
		foreach( $imcheck as $dir ) {
			$im = "$dir/convert";
			if( @file_exists( $im ) ) {
				$this->showMessage( 'config-imagemagick', $im );
				$this->setVar( 'wgImageMagickConvertCommand', $im );
				return true;
			}
		}
		if ( function_exists( 'imagejpeg' ) ) {
			$this->showMessage( 'config-gd' );
			return true;
		}
		$this->showMessage( 'no-scaling' );
	}

	/** Environment check for setting $IP and $wgScriptPath */
	function envCheckPath() {
		$IP = dirname( dirname( dirname( __FILE__ ) ) );
		$this->setVar( 'IP', $IP );
		$this->showMessage( 'config-dir', $IP );

		// PHP_SELF isn't available sometimes, such as when PHP is CGI but
		// cgi.fix_pathinfo is disabled. In that case, fall back to SCRIPT_NAME
		// to get the path to the current script... hopefully it's reliable. SIGH
		if ( !empty( $_SERVER['PHP_SELF'] ) ) {
			$path = $_SERVER['PHP_SELF'];
		} elseif ( !empty( $_SERVER['SCRIPT_NAME'] ) ) {
			$path = $_SERVER['SCRIPT_NAME'];
		} elseif ( $this->getVar( 'wgScriptPath' ) ) {
			// Some kind soul has set it for us already (e.g. debconf)
			return true;
		} else {
			$this->showMessage( 'config-no-uri' );
			return false;
		}
		$uri = preg_replace( '{^(.*)/config.*$}', '$1', $path );
		$this->setVar( 'wgScriptPath', $uri );
		$this->showMessage( 'config-uri', $uri );
	}

	abstract function showStatusError( $status );

	/** Environment check for setting the preferred PHP file extension */
	function envCheckExtension() {
		// FIXME: detect this properly
		if ( defined( 'MW_INSTALL_PHP5_EXT' ) ) {
			$ext = 'php5';
		} else {
			$ext = 'php';
		}
		$this->setVar( 'wgScriptExtension', ".$ext" );
		$this->showMessage( 'config-extension', $ext );
	}

	/**
	 * Convert wikitext $text to HTML.
	 *
	 * This is potentially error prone since many parser features require a complete
	 * installed MW database. The solution is to just not use those features when you 
	 * write your messages. This appears to work well enough. Basic formatting and
	 * external links work just fine.
	 *
	 * But in case a translator decides to throw in a #ifexist or internal link or 
	 * whatever, this function is guarded to catch attempted DB access and to present 
	 * some fallback text.
	 *
	 * @param string $text
	 * @return string
	 */
	function parse( $text, $lineStart = false ) {
		global $wgParser;
		try {
			$out = $wgParser->parse( $text, $this->parserTitle, $this->parserOptions, $lineStart );
			$html = $out->getText();
		} catch ( InstallerDBAccessError $e ) {
			$html = '<!--DB access attempted during parse-->  ' . htmlspecialchars( $text );
			if ( !empty( $this->debug ) ) {
				$html .= "<!--\n" . $e->getTraceAsString() . "\n-->";
			}
		}
		return $html;
	}

	/**
	 * Extension tag hook for a documentation link
	 */
	function docLink( $linkText, $attribs, $parser ) {
		$url = $this->getDocUrl( $attribs['href'] );
		return '<a href="' . htmlspecialchars( $url ) . '">' . 
			htmlspecialchars( $linkText ) . 
			'</a>';
	}

	/**
	 * Overridden by WebInstaller to provide lastPage parameters
	 */
	protected function getDocUrl( $page ) {
		return "{$_SERVER['PHP_SELF']}?page=" . urlencode( $attribs['href'] );
	}
}

/**
 * Exception class for attempted DB access
 */
class InstallerDBAccessError extends MWException {
	function __construct() {
		parent::__construct( "The installer attempted to access the DB via wfGetDB(). This is not allowed." );
	}
}

/**
 * LBFactory class that throws an error on any attempt to use it. 
 * This will typically be done via wfGetDB().
 * Installer entry points should ensure that they set up $wgLBFactoryConf to 
 *  array( 'class' => 'LBFactory_InstallerFake' )
 */
class LBFactory_InstallerFake extends LBFactory {
	function __construct( $conf ) {}

	function newMainLB( $wiki = false) {
		throw new InstallerDBAccessError;
	}
	function getMainLB( $wiki = false ) {
		throw new InstallerDBAccessError;
	}
	function newExternalLB( $cluster, $wiki = false ) {
		throw new InstallerDBAccessError;
	}
	function &getExternalLB( $cluster, $wiki = false ) {
		throw new InstallerDBAccessError;
	}
	function forEachLB( $callback, $params = array() ) {}
}

