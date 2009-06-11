<?php
/**
 * Abstract maintenance class for quickly writing and churning out
 * maintenance scripts with minimal effort. All that _must_ be defined
 * is the execute() method. See docs/maintenance.txt for more info
 * and a quick demo of how to use it.
 *
 * @author Chad Horohoe <chad@anyonecanedit.org>
 * @since 1.16
 * @ingroup Maintenance
 */
abstract class Maintenance {

	// This is the desired params
	protected $mParams = array();
	
	// Array of desired args
	protected $mArgList = array();

	// This is the list of options that were actually passed
	protected $mOptions = array();

	// This is the list of arguments that were actually passed
	protected $mArgs = array();
	
	// Name of the script currently running
	protected $mSelf;

	// Special vars for params that are always used
	private $mQuiet = false;
	private $mDbUser, $mDbPass;

	// A description of the script, children should change this
	protected $mDescription = '';

	/**
	 * Default constructor. Children should call this if implementing
	 * their own constructors
	 */
	public function __construct() {
		$this->addDefaultParams();
	}

	/**
	 * Do the actual work. All child classes will need to implement this
	 */
	abstract protected function execute();

	/**
	 * Add a parameter to the script. Will be displayed on --help
	 * with the associated description
	 *
	 * @param $name String The name of the param (help, version, etc)
	 * @param $description String The description of the param to show on --help
	 * @param $required boolean Is the param required?
	 * @param $withArg Boolean Is an argument required with this option?
	 */
	protected function addParam( $name, $description, $required = false, $withArg = false ) {
		$this->mParams[ $name ] = array( 'desc' => $description, 'require' => $required, 'withArg' => $withArg );
	}
	
	/**
	 * Add some args that are needed. Used in formatting help
	 */
	protected function addArgs( $args ) {
		foreach( $args as $arg ) {
			$this->mArgList[] = $arg;
		}
	}

	/**
	 * Return input from stdin.
	 * @param $length int The number of bytes to read
	 * @return mixed
	 */
	protected function getStdin( $len = 255 ) {
		$f = fopen( 'php://stdin', 'r' );
		$input = fgets( $fr, $len );
		fclose ( $fr );
		return rtrim( $input );
	}

	/**
	 * Throw some output to the user. Scripts can call this with no fears,
	 * as we handle all --quiet stuff here
	 * @param $out String The text to show to the user
	 */
	protected function output( $out ) {
		if( $this->mQuiet ) {
			return;
		}
		$f = fopen( 'php://stdout', 'w' );
		fwrite( $f, $out );
		fclose( $f );
	}

	/**
	 * Throw an error to the user. Doesn't respect --quiet, so don't use
	 * this for non-error output
	 * @param $err String The error to display
	 * @param $die boolean If true, go ahead and die out.
	 */
	protected function error( $err, $die = false ) {
		$f = fopen( 'php://stderr', 'w' ); 
		fwrite( $f, $err ); 
		fclose( $f ); 
		if( $die ) die();
	}

	/**
	 * Does the script need DB access? Specifically, we mean admin access to
	 * the DB. Override this and return true,
	 * if needed
	 * @return boolean
	 */
	protected function needsDB() {
		return false;
	}

	/**
	 * Add the default parameters to the scripts
	 */
	private function addDefaultParams() {
		$this->addParam( 'help', "Display this help message" );
		$this->addParam( 'quiet', "Whether to supress non-error output" );
		$this->addParam( 'conf', "Location of LocalSettings.php, if not default", false, true );
		$this->addParam( 'wiki', "For specifying the wiki ID", false, true );
		if( $this->needsDB() ) {
			$this->addParam( 'dbuser', "The DB user to use for this script", false, true );
			$this->addParam( 'dbpass', "The password to use for this script", false, true );
		}
	}

	/**
	 * Do some sanity checking
	 */
	public function setup() {
		global $IP, $wgCommandLineMode, $wgUseNormalUser, $wgRequestTime;

		# Abort if called from a web server
		if ( isset( $_SERVER ) && array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
			$this->error( "This script must be run from the command line\n", true );
		}

		# Make sure we can handle script parameters
		if( !ini_get( 'register_argc_argv' ) ) {
			$this->error( "Cannot get command line arguments, register_argc_argv is set to false", true );
		}

		# Make sure we're on PHP5 or better
		if( version_compare( PHP_VERSION, '5.0.0' ) < 0 ) {
			$this->error( "Sorry! This version of MediaWiki requires PHP 5; you are running " .
					PHP_VERSION . ".\n\n" .
					"If you are sure you already have PHP 5 installed, it may be installed\n" .
					"in a different path from PHP 4. Check with your system administrator.\n", true );
		}

		if( version_compare( phpversion(), '5.2.4' ) >= 0 ) {
			// Send PHP warnings and errors to stderr instead of stdout.
			// This aids in diagnosing problems, while keeping messages
			// out of redirected output.
			if( ini_get( 'display_errors' ) ) {
				ini_set( 'display_errors', 'stderr' );
			}

			// Don't touch the setting on earlier versions of PHP,
			// as setting it would disable output if you'd wanted it.

			// Note that exceptions are also sent to stderr when
			// command-line mode is on, regardless of PHP version.
		}

		# Set the memory limit
		ini_set( 'memory_limit', -1 );

		$wgRequestTime = microtime(true);

		# Define us as being in Mediawiki
		define( 'MEDIAWIKI', true );

		# Setup $IP, using MW_INSTALL_PATH if it exists
		$IP = strval( getenv('MW_INSTALL_PATH') ) !== ''
			? getenv('MW_INSTALL_PATH')
			: realpath( dirname( __FILE__ ) . '/..' );
		
		$wgCommandLineMode = true;
		# Turn off output buffering if it's on
		@ob_end_flush();

		if (!isset( $wgUseNormalUser ) ) {
			$wgUseNormalUser = false;
		}
		
		$this->loadArgs();
		$this->maybeHelp();
	}

	/**
	 * Process command line arguments
	 * $mOptions becomes an array with keys set to the option names
	 * $mArgs becomes a zero-based array containing the non-option arguments
	 */
	private function loadArgs() {
		global $argv;
		$this->mSelf = array_shift( $argv );

		$options = array();
		$args = array();

		# Parse arguments
		for( $arg = reset( $argv ); $arg !== false; $arg = next( $argv ) ) {
			if ( $arg == '--' ) {
				# End of options, remainder should be considered arguments
				$arg = next( $argv );
				while( $arg !== false ) {
					$args[] = $arg;
					$arg = next( $argv );
				}
				break;
			} elseif ( substr( $arg, 0, 2 ) == '--' ) {
				# Long options
				$option = substr( $arg, 2 );
				if ( isset( $this->mParams[$option] ) && $this->mParams[$option]['withArg'] ) {
					$param = next( $argv );
					if ( $param === false ) {
						$this->error( "$arg needs a value after it\n", true );
					}
					$options[$option] = $param;
				} else {
					$bits = explode( '=', $option, 2 );
					if( count( $bits ) > 1 ) {
						$option = $bits[0];
						$param = $bits[1];
					} else {
						$param = 1;
					}
					$options[$option] = $param;
				}
			} elseif ( substr( $arg, 0, 1 ) == '-' ) {
				# Short options
				for ( $p=1; $p<strlen( $arg ); $p++ ) {
					$option = $arg{$p};
					if ( isset( $this->mParams[$option]['withArg'] ) ) {
						$param = next( $argv );
						if ( $param === false ) {
							$this->error( "$arg needs a value after it\n", true );
						}
						$options[$option] = $param;
					} else {
						$options[$option] = 1;
					}
				}
			} else {
				$args[] = $arg;
			}
		}

		# These vars get special treatment
		if( isset( $options['dbuser'] ) )
			$this->mDbUser = $options['dbuser'];
		if( isset( $options['dbpass'] ) )
			$this->mDbPass = $options['dbpass'];
		if( isset( $options['quiet'] ) )
			$this->mQuiet = true;

		# Check to make sure we've got all the required ones
		foreach( $this->mParams as $opt => $info ) {
			if( $info['require'] && !isset( $this->mOptions[$opt] ) ) {
				$this->error( "Param $opt required.\n", true );
			}
		}

		$this->mOptions = $options;
		$this->mArgs = $args;
	}
	
	/**
	 * Maybe show the help.
	 */
	private function maybeHelp() {
		if( isset( $this->mOptions['help'] ) || in_array( 'help', $this->mArgs ) ) {
			$this->mQuiet = false;
			if( $this->mDescription ) {
				$this->output( $this->mDescription . "\n" );
			}
			$this->output( "\nUsage: php " . $this->mSelf . " [--" . 
							implode( array_keys( $this->mParams ), "|--" ) . "] <" . 
							implode( $this->mArgList, "> <" ) . ">\n" );
			foreach( $this->mParams as $par => $info ) {
				$this->output( "\t$par : " . $info['desc'] . "\n" );
			}
			die( 1 );
		}
	}
	
	/**
	 * Handle some last-minute setup here.
	 */
	private function finalSetup() {
		global $wgCommandLineMode, $wgUseNormalUser, $wgShowSQLErrors;
		global $wgTitle, $wgProfiling, $IP, $wgDBadminuser, $wgDBadminpassword;
		global $wgDBuser, $wgDBpassword, $wgDBservers, $wgLBFactoryConf;
		
		# Turn off output buffering again, it might have been turned on in the settings files
		if( ob_get_level() ) {
			ob_end_flush();
		}
		# Same with these
		$wgCommandLineMode = true;

		# If these were passed, use them
		if( $this->mDbUser )
			$wgDBadminuser = $this->mDbUser;
		if( $this->mDbPass )
			$wgDBadminpass = $this->mDbPass;

		if ( empty( $wgUseNormalUser ) && isset( $wgDBadminuser ) ) {
			$wgDBuser = $wgDBadminuser;
			$wgDBpassword = $wgDBadminpassword;
	
			if( $wgDBservers ) {
				foreach ( $wgDBservers as $i => $server ) {
					$wgDBservers[$i]['user'] = $wgDBuser;
					$wgDBservers[$i]['password'] = $wgDBpassword;
				}
			}
			if( isset( $wgLBFactoryConf['serverTemplate'] ) ) {
				$wgLBFactoryConf['serverTemplate']['user'] = $wgDBuser;
				$wgLBFactoryConf['serverTemplate']['password'] = $wgDBpassword;
			}
		}
	
		if ( defined( 'MW_CMDLINE_CALLBACK' ) ) {
			$fn = MW_CMDLINE_CALLBACK;
			$fn();
		}
	
		$wgShowSQLErrors = true;
		@set_time_limit( 0 );
	
		$wgProfiling = false; // only for Profiler.php mode; avoids OOM errors
	}
	
	/**
	 * Do setup specific to WMF
	 */
	public function loadWikimediaSettings() {
		global $IP, $wgNoDBParam, $wgUseNormalUser, $wgConf;

		if ( empty( $wgNoDBParam ) ) {
			# Check if we were passed a db name
			if ( isset( $this->mOptions['wiki'] ) ) {
				$db = $this->mOptions['wiki'];
			} else {
				$db = array_shift( $this->mArgs );
			}
			list( $site, $lang ) = $wgConf->siteFromDB( $db );
	
			# If not, work out the language and site the old way
			if ( is_null( $site ) || is_null( $lang ) ) {
				if ( !$db ) {
					$lang = 'aa';
				} else {
					$lang = $db;
				}
				if ( isset( $this->mArgs[0] ) ) {
					$site = array_shift( $this->mArgs );
				} else {
					$site = 'wikipedia';
				}
			}
		} else {
			$lang = 'aa';
			$site = 'wikipedia';
		}
	
		# This is for the IRC scripts, which now run as the apache user
		# The apache user doesn't have access to the wikiadmin_pass command
		if ( $_ENV['USER'] == 'apache' ) {
		#if ( posix_geteuid() == 48 ) {
			$wgUseNormalUser = true;
		}
	
		putenv( 'wikilang=' . $lang );
	
		$DP = $IP;
		ini_set( 'include_path', ".:$IP:$IP/includes:$IP/languages:$IP/maintenance" );
	
		if ( $lang == 'test' && $site == 'wikipedia' ) {
			define( 'TESTWIKI', 1 );
		}
	}

	/**
	 * Generic setup for most installs. Returns the location of LocalSettings
	 * @return String
	 */
	public function loadSettings() {
		global $wgWikiFarm, $wgCommandLineMode, $IP, $DP;

		$wgWikiFarm = false;
		if ( isset( $this->mOptions['conf'] ) ) {
			$settingsFile = $this->mOptions['conf'];
		} else {
			$settingsFile = "$IP/LocalSettings.php";
		}
		if ( isset( $this->mOptions['wiki'] ) ) {
			$bits = explode( '-', $this->mOptions['wiki'] );
			if ( count( $bits ) == 1 ) {
				$bits[] = '';
			}
			define( 'MW_DB', $bits[0] );
			define( 'MW_PREFIX', $bits[1] );
		}
	
		if ( ! is_readable( $settingsFile ) ) {
			$this->error( "A copy of your installation's LocalSettings.php\n" .
			  			"must exist and be readable in the source directory.\n", true );
		}
		$wgCommandLineMode = true;
		$DP = $IP;
		$this->finalSetup();
		return $settingsFile;
	}
}