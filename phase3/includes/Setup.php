<?php
/**
 * Include most things that's need to customize the site
 * @package MediaWiki
 */

/**
 * This file is not a valid entry point, perform no further processing unless
 * MEDIAWIKI is defined
 */
if( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is part of MediaWiki, it is not a valid entry point.\n";
	exit( 1 );
}	

# The main wiki script and things like database
# conversion and maintenance scripts all share a
# common setup of including lots of classes and
# setting up a few globals.
#

$fname = 'Setup.php';
wfProfileIn( $fname );

// Check to see if we are at the file scope
if ( !isset( $wgVersion ) ) {
	echo "Error, Setup.php must be included from the file scope, after DefaultSettings.php\n";
	die( 1 );
}

require_once( "$IP/includes/AutoLoader.php" );

wfProfileIn( $fname.'-exception' );
require_once( "$IP/includes/Exception.php" );
wfInstallExceptionHandler();
wfProfileOut( $fname.'-exception' );

wfProfileIn( $fname.'-includes' );
require_once( "$IP/includes/GlobalFunctions.php" );
require_once( "$IP/includes/Hooks.php" );
require_once( "$IP/includes/Namespace.php" );
require_once( "$IP/includes/ProxyTools.php" );
require_once( "$IP/includes/ObjectCache.php" );
require_once( "$IP/includes/ImageFunctions.php" );

wfProfileOut( $fname.'-includes' );
wfProfileIn( $fname.'-misc1' );

class StubObject {
	var $mGlobal, $mClass, $mParams;
	function __construct( $global = null, $class = null, $params = array() ) {
		$this->mGlobal = $global;
		$this->mClass = $class;
		$this->mParams = $params;
	}

	static function _getCaller( $level ) {
		$backtrace = debug_backtrace();
		if ( isset( $backtrace[$level] ) ) {
			if ( isset( $backtrace[$level]['class'] ) ) {
				$caller = $backtrace[$level]['class'] . '::' . $backtrace[$level]['function'];
			} else {
				$caller = $backtrace[$level]['function'];
			}
		} else {
			$caller = 'unknown';
		}
		return $caller;
	}

	function _call( $name, $args ) {
		$this->_unstub( $name, 5 );
		return call_user_func_array( array( $GLOBALS[$this->mGlobal], $name ), $args );
	}

	function _newObject() {
		return wfCreateObject( $this->mClass, $this->mParams );
	}
	
	function __call( $name, $args ) {
		return $this->_call( $name, $args );
	}

	/**
	 * This is public, for the convenience of external callers wishing to access 
	 * properties, e.g. eval.php
	 */
	function _unstub( $name = '_unstub', $level = 2 ) {
		if ( get_class( $GLOBALS[$this->mGlobal] ) != $this->mClass ) {
			$fname = __METHOD__.'-'.$this->mGlobal;
			wfProfileIn( $fname );
			$caller = self::_getCaller( $level );
			wfDebug( "Unstubbing \${$this->mGlobal} on call of {$this->mGlobal}->$name from $caller\n" );
			$GLOBALS[$this->mGlobal] = $this->_newObject();
			wfProfileOut( $fname );
		}
	}
}

class StubContLang extends StubObject {
	function __construct() {
		parent::__construct( 'wgContLang' );
	}

	function __call( $name, $args ) {
		return StubObject::_call( $name, $args );
	}

	function _newObject() {
		global $wgContLanguageCode;
		$obj = wfNewLangObj( $wgContLanguageCode );
		$obj->initEncoding();
		$obj->initContLang();
		return $obj;
	}
}
class StubUserLang extends StubObject {
	function __construct() {
		parent::__construct( 'wgLang' );
	}

	function __call( $name, $args ) {
		return $this->_call( $name, $args );
	}

	function _newObject() {
		global $wgLanguageCode, $wgContLanguageCode, $wgRequest, $wgUser, $wgContLang;
		// wgLanguageCode now specifically means the UI language
		$wgLanguageCode = $wgRequest->getText('uselang', '');
		if ($wgLanguageCode == '')
			$wgLanguageCode = $wgUser->getOption('language');
		# Validate $wgLanguageCode
		if( empty( $wgLanguageCode ) || !preg_match( '/^[a-z]+(-[a-z]+)?$/', $wgLanguageCode ) ) {
			$wgLanguageCode = $wgContLanguageCode;
		}

		if( $wgLanguageCode == $wgContLanguageCode ) {
			return $wgContLang;
		} else {
			$obj = wfNewLangObj( $wgLanguageCode );
			$obj->initEncoding();
			return $obj;
		}
	}
}
class StubUser extends StubObject {
	function __construct() {
		parent::__construct( 'wgUser' );
	}

	function __call( $name, $args ) {
		return $this->_call( $name, $args );
	}
	
	function _newObject() {
		global $wgCommandLineMode;
		if( $wgCommandLineMode ) {
			$user = new User;
			$user->setLoaded( true );
		} else {
			$user = User::loadFromSession();
		}
		return $user;
	}
}

$wgIP = false; # Load on demand
# Can't stub this one, it sets up $_GET and $_REQUEST in its constructor
$wgRequest = new WebRequest;
if ( function_exists( 'posix_uname' ) ) {
	$wguname = posix_uname();
	$wgNodeName = $wguname['nodename'];
} else {
	$wgNodeName = '';
}

# Useful debug output
if ( $wgCommandLineMode ) {
	wfDebug( "\n\nStart command line script\n" );
} elseif ( function_exists( 'getallheaders' ) ) {
	wfDebug( "\n\nStart request\n" );
	wfDebug( $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . "\n" );
	$headers = getallheaders();
	foreach ($headers as $name => $value) {
		wfDebug( "$name: $value\n" );
	}
	wfDebug( "\n" );
} elseif( isset( $_SERVER['REQUEST_URI'] ) ) {
	wfDebug( $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . "\n" );
}

if ( $wgSkipSkin ) {
	$wgSkipSkins[] = $wgSkipSkin;
}

$wgUseEnotif = $wgEnotifUserTalk || $wgEnotifWatchlist;

if($wgMetaNamespace === FALSE) {
	$wgMetaNamespace = str_replace( ' ', '_', $wgSitename );
}

wfProfileOut( $fname.'-misc1' );
wfProfileIn( $fname.'-memcached' );

$wgMemc =& wfGetMainCache();
$messageMemc =& wfGetMessageCacheStorage();
$parserMemc =& wfGetParserCacheStorage();

wfDebug( 'Main cache: ' . get_class( $wgMemc ) .
       "\nMessage cache: " . get_class( $messageMemc ) .
	   "\nParser cache: " . get_class( $parserMemc ) . "\n" );

wfProfileOut( $fname.'-memcached' );
wfProfileIn( $fname.'-SetupSession' );

if ( $wgDBprefix ) {
	$wgCookiePrefix = $wgDBname . '_' . $wgDBprefix;
} elseif ( $wgSharedDB ) {
	$wgCookiePrefix = $wgSharedDB;
} else {
	$wgCookiePrefix = $wgDBname;
}

# If session.auto_start is there, we can't touch session name
#
if( !ini_get( 'session.auto_start' ) )
	session_name( $wgSessionName ? $wgSessionName : $wgCookiePrefix . '_session' );

if( !$wgCommandLineMode && ( isset( $_COOKIE[session_name()] ) || isset( $_COOKIE[$wgCookiePrefix.'Token'] ) ) ) {
	wfIncrStats( 'request_with_session' );
	wfSetupSession();
	$wgSessionStarted = true;
} else {
	wfIncrStats( 'request_without_session' );
	$wgSessionStarted = false;
}

wfProfileOut( $fname.'-SetupSession' );
wfProfileIn( $fname.'-globals' );

if ( !$wgDBservers ) {
	$wgDBservers = array(array(
		'host' => $wgDBserver,
		'user' => $wgDBuser,
		'password' => $wgDBpassword,
		'dbname' => $wgDBname,
		'type' => $wgDBtype,
		'load' => 1,
		'flags' => ($wgDebugDumpSql ? DBO_DEBUG : 0) | DBO_DEFAULT
	));
}

# $wgLanguageCode may be changed later to fit with user preference.
# The content language will remain fixed as per the configuration,
# so let's keep it.
$wgContLanguageCode = $wgLanguageCode;

$wgLoadBalancer = new StubObject( 'wgLoadBalancer', 'LoadBalancer', 
	array( $wgDBservers, false, $wgMasterWaitTimeout, true ) );
$wgContLang = new StubContLang;
$wgUser = new StubUser;
$wgLang = new StubUserLang;
$wgOut = new StubObject( 'wgOut', 'OutputPage' );
$wgParser = new StubObject( 'wgParser', 'Parser' );
$wgMessageCache = new StubObject( 'wgMessageCache', 'MessageCache', 
	array( $parserMemc, $wgUseDatabaseMessages, $wgMsgCacheExpiry, $wgDBname) );

wfProfileOut( $fname.'-globals' );
wfProfileIn( $fname.'-User' );

# Skin setup functions
# Entries can be added to this variable during the inclusion
# of the extension file. Skins can then perform any necessary initialisation.
# 
foreach ( $wgSkinExtensionFunctions as $func ) {
	call_user_func( $func );
}

if( !is_object( $wgAuth ) ) {
	$wgAuth = new StubObject( 'wgAuth', 'AuthPlugin' );
}
wfProfileOut( $fname.'-User' );

wfProfileIn( $fname.'-misc2' );

$wgDeferredUpdateList = array();
$wgPostCommitUpdateList = array();

wfSeedRandom();

# Placeholders in case of DB error
$wgTitle = null;
$wgArticle = null;

wfProfileOut( $fname.'-misc2' );
wfProfileIn( $fname.'-extensions' );

# Extension setup functions for extensions other than skins
# Entries should be added to this variable during the inclusion
# of the extension file. This allows the extension to perform
# any necessary initialisation in the fully initialised environment
foreach ( $wgExtensionFunctions as $func ) {
	call_user_func( $func );
}

// For compatibility
wfRunHooks( 'LogPageValidTypes', array( &$wgLogTypes ) );
wfRunHooks( 'LogPageLogName', array( &$wgLogNames ) );
wfRunHooks( 'LogPageLogHeader', array( &$wgLogHeaders ) );
wfRunHooks( 'LogPageActionText', array( &$wgLogActions ) );


wfDebug( "Fully initialised\n" );
$wgFullyInitialised = true;
wfProfileOut( $fname.'-extensions' );
wfProfileOut( $fname );

?>
