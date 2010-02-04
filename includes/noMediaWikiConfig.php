<?php
/**
 * No mediaWikiConfig sets variables for using the script-loader and mwEmbed modules
 * without a complete mediaWiki install.
 */

// Optional set the path for google Closure Compiler ( for improved minification )
$wgClosureCompilerPath = false;
$wgJavaPath = false;

// Give us true for MediaWiki
define( 'MEDIAWIKI', true );

define( 'MWEMBED_STANDALONE', true );

// Setup the globals: 	(for documentation see: DefaultSettings.php )

$IP = realpath( dirname( __FILE__ ) . '/../' );

// $wgMwEmbedDirectory becomes the root $IP
$wgMwEmbedDirectory = '';

$wgUseFileCache = true;

// Init our wg Globals
$wgJSAutoloadLocalClasses = array();
$wgJSAutoloadClasses = array();
$wgExtensionJavascriptLoader = array();
$wgJSModuleLoaderPaths = array();

/*Localization:*/
$wgEnableScriptLocalization = true;

$mwLanguageCode = 'en';
$wgLang = false;

$wgStyleVersion = '218';

$wgEnableScriptMinify = true;

$wgUseGzip = true;


/**
 * Default value for chmoding of new directories.
 */
$wgDirectoryMode = 0777;

$wgJsMimeType = 'text/javascript';

// Get the autoload classes
require_once( realpath( dirname( __FILE__ ) ) . '/jsClassLoader.php' );
// Load the javascript Classes
jsClassLoader::loadClassPaths();

// Get the JSmin class:
require_once( realpath( dirname( __FILE__ ) ) . '/library/JSMin.php' );

// Get the messages file:
require_once( realpath( dirname( __FILE__ ) ) . '/languages/mwEmbed.i18n.php' );

function wfDebug() {
    return false;
}

/**
 * Make directory, and make all parent directories if they don't exist
 *
 * @param string $dir Full path to directory to create
 * @param int $mode Chmod value to use, default is $wgDirectoryMode
 * @param string $caller Optional caller param for debugging.
 * @return bool
 */
function wfMkdirParents( $dir, $mode = null, $caller = null ) {
	global $wgDirectoryMode;

	if ( !is_null( $caller ) ) {
		wfDebug( "$caller: called wfMkdirParents($dir)" );
	}

	if ( strval( $dir ) === '' || file_exists( $dir ) )
		return true;

	if ( is_null( $mode ) )
		$mode = $wgDirectoryMode;

	return @mkdir( $dir, $mode, true );  // PHP5 <3
}

/**
 * Copied from mediaWIki GlobalFunctions.php wfMsgGetKey
 *
 * Fetch a message string value, but don't replace any keys yet.
 * @param $key String
 * @param $useDB Bool
 * @param $langCode String: Code of the language to get the message for, or
 *                  behaves as a content language switch if it is a boolean.
 * @param $transform Boolean: whether to parse magic words, etc.
 * @return string
 * @private
 */
function wfMsgGetKey( $msgKey, $na, $langKey=false ) {
    global $messages, $mwLanguageCode;
    if(!$langKey){
    	$langKey = $mwLanguageCode;
    }
    if ( isset( $messages[$mwLanguageCode] ) && isset( $messages[$langKey][$msgKey] ) ) {
        return $messages[$langKey][$msgKey];
    } else {
        return '&lt;' . $msgKey . '&gt;';
    }
}

/**
 * mediaWiki abstracts the json functions with fallbacks
 * here we just map directly to the call
 */
class FormatJson{
	public static function encode($value, $isHtml=false){
		return json_encode($value);
	}
	public static function decode( $value, $assoc=false ){
		return json_decode( $value, $assoc );
	}
}
// MWException extends Exception (for noWiki we don't do anything fancy )
class MWException extends Exception {
}
function wfSuppressWarnings(){
};
function wfRestoreWarnings(){
};