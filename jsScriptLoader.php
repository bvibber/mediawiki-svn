<?php
/**
 * This core jsScriptLoader class provides the script loader functionality
 * @file
 */


//Setup the script local script cache directory
// ( has to be hard coded rather than config based for fast non-mediawiki config hits )
$wgScriptCacheDirectory = realpath( dirname( __FILE__ ) ) . '/includes/cache';

// Check if we are being invoked in a MediaWiki context or stand alone usage:
if ( !defined( 'MEDIAWIKI' ) && !defined( 'MW_CACHE_SCRIPT_CHECK' ) ){
	// Load noMediaWiki helper for quick cache result
	$myScriptLoader = new jsScriptLoader();
	if( $myScriptLoader->outputFromCache() )
		exit();
	//Else load up all the config and do normal stand alone ScriptLoader process:
	require_once( realpath( dirname( __FILE__ ) ) . '/includes/noMediaWikiConfig.php' );
	$myScriptLoader->doScriptLoader();
}

class jsScriptLoader {
	var $jsFileList = array();
	var $langCode = '';
	var $jsout = '';
	var $requestKey = ''; // the request key
	var $error_msg = '';
	var $debug = false;

	// Whether we should include generated JS (special class '-')
	var $jsvarurl = false;
	var $doProcReqFlag = true;

	private static $rawClassList = '';

	/**
	 * Output the javascript from cache
	 *
	 * @return {Boolean} true on success, false on failure
	 */
	function outputFromCache(){
		// Process the request
		$this->requestKey = $this->preProcRequestVars();
		// Setup file cache object
		$this->sFileCache = new simpleFileCache( $this->requestKey );
		if ( $this->sFileCache->isFileCached() ) {
			// Just output headers so we can use PHP's @readfile::
			$this->outputJsHeaders();
			$this->sFileCache->loadFromFileCache();
			return true;
		}
		return false;
	}

	/**
	 * Core scriptLoader driver:
	 * 	get request key
	 *  builds javascript string
	 *  optionally gzips the output
	 *  checks for errors
	 *  sends the headers
	 *  outputs js
	 */
	function doScriptLoader() {
		global 	$wgJSAutoloadClasses, $IP,
		$wgEnableScriptMinify, $wgUseFileCache, $wgExtensionMessagesFiles;

		// Load the ExtensionMessagesFiles
		$wgExtensionMessagesFiles[ 'mwEmbed' ] = realpath( dirname( __FILE__ ) ) .
			'/includes/languages/mwEmbed.i18n.php';

		// Load the javascript class paths:
		require_once( realpath( dirname( __FILE__ ) ) . "/includes/jsClassLoader.php");
		jsClassLoader::loadClassPaths();

		// Reset the requestKey:
		$this->requestKey = '';
		// Do the post proc request with configuration vars:
		$this->postProcRequestVars();
		//update the filename (if gzip is on)
		$this->sFileCache->getCacheFileName();

		// Setup script loader header:
		$this->jsout .= 'var mwScriptLoaderDate = "' . date( 'c' ) . '";'  . "\n";
		$this->jsout .= 'var mwScriptLoaderRequestKey = "' . htmlspecialchars( $this->requestKey ) . '";'  . "\n";
		$this->jsout .= 'var mwLang = "' . htmlspecialchars( $this->langCode ) . '";' . "\n";

		// Build the output
		// Swap in the appropriate language per js_file
		foreach ( $this->jsFileList as $classKey => $file_name ) {
			// Get the script content
			$jstxt = $this->getScriptText( $classKey, $file_name );
			if( $jstxt ){
				$this->jsout .= $this->doProcessJs( $jstxt );
			}
			// If the core mwEmbed class entry point include loader js
			if( $classKey == 'mwEmbed' ){
				$this->jsout .= jsClassLoader::getCombinedLoaderJs();
			}
		}

		// Add a mw.loadDone callback so webkit browsers don't have to check if variables are "ready"
		$this->jsout .= self::getOnDoneCallback( );


		// Check if we should minify the whole thing:
		if ( !$this->debug ) {
			$this->jsout = self::getMinifiedJs( $this->jsout , $this->requestKey );
		}
		// Save to the file cache
		if ( $wgUseFileCache && !$this->debug ) {
			$status = $this->sFileCache->saveToFileCache( $this->jsout );
			if ( $status !== true )
			$this->error_msg .= $status;
		}
		// Check for an error msg
		if ( $this->error_msg != '' ) {
			//just set the content type (don't send cache header)
			header( 'Content-Type: text/javascript' );
			echo 'alert(\'Error With ScriptLoader ::' . str_replace( "\n", '\'+"\n"+' . "\n'", $this->error_msg ) . '\');';
			echo trim( $this->jsout );
		} else {
			// All good, let's output "cache" headers
			$this->outputJsWithHeaders();
		}
	}
	/**
	 * Get the onDone javascript callback for a given class list
	 *
	 * @return unknown
	 */
	static private function getOnDoneCallback( ){
		return 'if(mw && mw.loadDone){mw.loadDone(\'' .
							htmlspecialchars( self::$rawClassList ) . '\');};';
	}
	/**
	 * Get Minified js
	 *
	 * Takes the $js_string input
	 *  and
	 * @return  minified javascript value
	 */
	static function getMinifiedJs( & $js_string, $requestKey='' ){
		global $wgJavaPath, $wgClosureCompilerPath, $wgClosureCompilerLevel;


		// Check if google closure compiler is enabled and we can get its output
		if( $wgJavaPath && $wgClosureCompilerPath && wfShellExecEnabled() ){
			$jsMinVal = self::getClosureMinifiedJs( $js_string, $requestKey );
			if( $jsMinVal ){
				return $jsMinVal;
			}else{
				wfDebug( 'Closure compiler failed to produce code for:' . $requestKey);
			}
		}
		// Do the minification using php JSMin
		return JSMin::minify( $js_string );
	}
	static function getClosureMinifiedJs( & $js_string, $requestKey=''){
		if( !is_file( $wgJavaPath ) || ! is_file( $wgClosureCompilerPath ) ){
			return false;
		}
		// Update the requestKey with a random value if no provided
		// requestKey is used for the temporary file
		// ( There are problems with using standard output and Closure compile )
		if( $requestKey == '')
			$requestKey = rand() + microtime();

		// Write the grouped javascript to a temporary file:
		// ( closure compiler does not support reading from standard in )
		$td = wfTempDir();
		$jsFileName = $td . '/' . $requestKey  . '.tmp.js';
		file_put_contents( $jsFileName,  $js_string );
		$retval = '';
		$cmd = $wgJavaPath . ' -jar ' . $wgClosureCompilerPath;
		$cmd.= ' --js ' . $jsFileName;

		if( $wgClosureCompilerLevel )
			$cmd.= ' --compilation_level ' .  wfEscapeShellArg( $wgClosureCompilerLevel );

		// only output js ( no warnings )
		$cmd.= ' --warning_level QUIET';
		//print "run: $cmd";
		// Run the command:
		$jsMinVal = wfShellExec($cmd , $retval);

		// Clean up ( remove temporary file )
		unlink( $jsFileName );

		if( strlen( $jsMinVal ) != 0 && $retval === 0){
			//die( "used closure" );
			return $jsMinVal;
		}
		return false;
	}
	/**
	 * Gets Script Text
	 *
	 * @param {String} $classKey Class Key to grab text for
	 * @param {String} [$file_name] Optional file path to get js text
	 * @return unknown
	 */
	function getScriptText( $classKey, $file_name = '' ){
		$jsout = '';
		// Special case: title classes
		if ( substr( $classKey, 0, 3 ) == 'WT:' ) {
			global $wgUser;
			// Get just the title part
			$title_block = substr( $classKey, 3 );
			if ( $title_block[0] == '-' && strpos( $title_block, '|' ) !== false ) {
				// Special case of "-" title with skin
				$parts = explode( '|', $title_block );
				$title = array_shift( $parts );
				foreach ( $parts as $tparam ) {
					list( $key, $val ) = explode( '=', $tparam );
					if ( $key == 'useskin' ) {
						$skin = $val;
					}
				}
				$sk = $wgUser->getSkin();
				// Make sure the skin name is valid
				$skinNames = Skin::getSkinNames();
				$skinNames = array_keys( $skinNames );
				if ( in_array( strtolower( $skin ), $skinNames ) ) {
					// If in debug mode, add a comment with wiki title and rev:
					if ( $this->debug )
					$jsout .= "\n/**\n* GenerateUserJs: \n*/\n";
					return $jsout . $sk->generateUserJs( $skin ) . "\n";
				}
			} else {
				// Make sure the wiki title ends with .js
				if ( substr( $title_block, -3 ) != '.js' ) {
					$this->error_msg .= 'WikiTitle includes should end with .js';
					return false;
				}
				// It's a wiki title, append the output of the wikitext:
				$t = Title::newFromText( $title_block );
				$a = new Article( $t );
				// Only get the content if the page is not empty:
				if ( $a->getID() !== 0 ) {
					// If in debug mode, add a comment with wiki title and rev:
					if ( $this->debug )
					$jsout .= "\n/**\n* WikiJSPage: " . htmlspecialchars( $title_block ) . " rev: " . $a->getID() . " \n*/\n";

					return $jsout . $a->getContent() . "\n";
				}
			}
		}else{
			// Dealing with files

			// Check that the filename ends with .js and does not include ../ traversing
			if ( substr( $file_name, -3 ) != '.js' ) {
				$this->error_msg .= "\nError file name must end with .js: " . htmlspecialchars( $file_name ) . " \n ";
				return false;
			}
			if ( strpos( $file_name, '../' ) !== false ) {
				$this->error_msg .= "\nError file name must not traverse paths: " . htmlspecialchars( $file_name ) . " \n ";
				return false;
			}

			if ( trim( $file_name ) != '' ) {
				if ( $this->debug )
				$jsout .= "\n/**\n* File: " . htmlspecialchars( $file_name ) . "\n*/\n";

				$jsFileStr = $this->doGetJsFile( $file_name ) . "\n";
				if( $jsFileStr ){
					return $jsout . $jsFileStr;
				}else{
					$this->error_msg .= "\nError could not read file: ". htmlspecialchars( $file_name )  ."\n";
					return false;
				}
			}
		}
		// If we did not return some js
		$this->error_msg .= "\nUnknown error\n";
		return false;
	}
	/**
	 * Outputs the script headers
	 */
	function outputJsHeaders() {
		// Output JS MIME type:
		header( 'Content-Type: text/javascript' );
		header( 'Pragma: public' );
		if( $this->debug ){
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		}else{
			// Cache for 2 days ( we should always change the request URL so this could be higher in my opinion)
			$one_day = 60 * 60 * 24 * 2;
			header( "Expires: " . gmdate( "D, d M Y H:i:s", time() + $one_day ) . " GM" );
		}
	}

	/**
	 * Outputs the javascript text with script headers
	 */
	function outputJsWithHeaders() {
		global $wgUseGzip;
		$this->outputJsHeaders();
		if ( $wgUseGzip ) {
			if ( $this->clientAcceptsGzip() ) {
				header( 'Content-Encoding: gzip' );
				echo gzencode( $this->jsout );
			} else {
				echo $this->jsout;
			}
		} else {
			echo $this->jsout;
		}
	}

	/**
	 * Checks if client Accepts Gzip response
	 *
	 * @return boolean
	 * 	true if client accepts gzip encoded response
	 * 	false if client does not accept gzip encoded response
	 */
	static function clientAcceptsGzip() {
		$m = array();
		if( isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) ){
			if( preg_match(
				'/\bgzip(?:;(q)=([0-9]+(?:\.[0-9]+)))?\b/',
			$_SERVER['HTTP_ACCEPT_ENCODING'],
			$m ) ) {
				if( isset( $m[2] ) && ( $m[1] == 'q' ) && ( $m[2] == 0 ) )
				return false;
				//no gzip support found
				return true;
			}
		}
		return false;
	}

	/**
	 * Post process request uses globals, configuration and mediaWiki to test wiki-titles and files exist etc.
	 */
	function postProcRequestVars(){
		global $wgContLanguageCode, $wgEnableScriptMinify, $wgJSAutoloadClasses,
		$wgStyleVersion;

		// Set debug flag
		if ( ( isset( $_GET['debug'] ) && $_GET['debug'] == 'true' ) || ( isset( $wgEnableScriptDebug ) && $wgEnableScriptDebug == true ) ) {
			$this->debug = true;
		}

		// Set the urid. Be sure to escape it as it goes into our JS output.
		if ( isset( $_GET['urid'] ) && $_GET['urid'] != '' ) {
			$this->urid = htmlspecialchars( $_GET['urid'] );
		} else {
			// Just give it the current style sheet ID:
			// @@todo read the svn version number
			$this->urid = $wgStyleVersion;
		}

		//get the language code (if not provided use the "default" language
		if ( isset( $_GET['uselang'] ) && $_GET['uselang'] != '' ) {
			//make sure its a valid lang code:
			$this->langCode = preg_replace( "/[^A-Za-z]/", '', $_GET['uselang']);
		}else{
			//set English as default
			$this->langCode = 'en';
		}
		$this->langCode = self::checkForCommonsLanguageFormHack( $this->langCode );

		$reqClassList = false;
		if ( isset( $_GET['class'] ) && $_GET['class'] != '' ) {
			$reqClassList = explode( ',', $_GET['class'] );
			self::$rawClassList= $_GET['class'];
		}

		// Check for the requested classes
		if ( $reqClassList ) {
			// Clean the class list and populate jsFileList
			foreach ( $reqClassList as $reqClass ) {
				if ( trim( $reqClass ) != '' ) {
					if ( substr( $reqClass, 0, 3 ) == 'WT:' ) {
						$doAddWT = false;
						// Check for special case '-' class for user-generated JS
						if( substr( $reqClass, 3, 1) == '-'){
							$doAddWT = true;
						}else{
							if( strtolower( substr( $reqClass, -3) ) == '.js'){
								//make sure its a valid wikipage before doing processing
								$t = Title::newFromDBkey( substr( $reqClass, 3) );
								if( $t->exists()
								&& ( $t->getNamespace() == NS_MEDIAWIKI
								|| $t->getNamespace() == NS_USER ) ){
									$doAddWT = true;
								}
							}
						}
						if( $doAddWT ){
							$this->jsFileList[$reqClass] = true;
							$this->requestKey .= $reqClass;
							$this->jsvarurl = true;
						}
						continue;
					}

					$reqClass = preg_replace( "/[^A-Za-z0-9_\-\.]/", '', $reqClass );

					$jsFilePath = self::getJsPathFromClass( $reqClass );
					if(!$jsFilePath){
						$this->error_msg .= 'Requested class: ' . htmlspecialchars( $reqClass ) . ' not found' . "\n";
					}else{
						$this->jsFileList[ $reqClass ] = $jsFilePath;
						$this->requestKey .= $reqClass;
					}
				}
			}
		}

		// Add the language code to the requestKey:
		$this->requestKey .= '_' . $wgContLanguageCode;

		// Add the unique rid
		$this->requestKey .= $this->urid;

		// Add a minify flag
		if ( $wgEnableScriptMinify ) {
			$this->requestKey .= '_min';
		}
	}
	/**
	 * Pre-process request variables ~without configuration~ or any utility functions
	 *  This is to quickly get a requestKey that we can check against the cache
	 */
	function preProcRequestVars() {
		$requestKey = '';
		// Check for debug (won't use the cache)
		if ( ( isset( $_GET['debug'] ) && $_GET['debug'] == 'true' ) ) {
			// We are going to have to run postProcRequest
			return false;
		}

		// Check for the urid. Be sure to escape it as it goes into our JS output.
		if ( isset( $_GET['urid'] ) && $_GET['urid'] != '' ) {
			$urid = htmlspecialchars( $_GET['urid'] );
		}else{
			// If no urid is set use special "cache" version.
			// (this requires that the cache is removed for updates to take effect.)
			$urid = 'cache';
		}

		// Get the language code (if not provided use the "default" language
		if ( isset( $_GET['uselang'] ) && $_GET['uselang'] != '' ) {
			// Make sure its just a simple [A-Za-z] value
			$langCode = preg_replace( "/[^A-Za-z]/", '', $_GET['uselang']);
		}else{
			// Set English as default
			$langCode = 'en';
		}

		$langCode = self::checkForCommonsLanguageFormHack( $langCode );


		$reqClassList = false;
		if ( isset( $_GET['class'] ) && $_GET['class'] != '' ) {
			$reqClassList = explode( ',', $_GET['class'] );
		}

		// Check for the requested classes
		if ( $reqClassList && count( $reqClassList ) > 0 ) {
			// Clean the class list and populate jsFileList
			foreach (  $reqClassList as $reqClass ) {
				//do some simple checks:
				if ( trim( $reqClass ) != '' ){
					if( substr( $reqClass, 0, 3 ) == 'WT:'  && strtolower( substr( $reqClass, -3) ) == '.js' ){
						// Wiki page requests (must end with .js):
						$requestKey .= $reqClass;
					}else if( substr( $reqClass, 0, 3 ) != 'WT:' ){
						// Normal class requests:
						$reqClass = preg_replace( "/[^A-Za-z0-9_\-\.]/", '', $reqClass );
						$requestKey .= $reqClass;
					}else{
						// Not a valid class
					}
				}
			}
		}
		// Add the language code to the requestKey:
		$requestKey .= '_' . $langCode;

		// Add the unique rid
		$requestKey .= $urid;

		return $requestKey;
	}
	/**
	 * Check for the commons language hack.
 	 * ( someone had the bright idea to use language keys as message
	 *  name-spaces for separate upload forms )
	 *
	 * @param {String} $langKey The lang key for the form
	 */
	public static function checkForCommonsLanguageFormHack( $langKey){
		$formNames = array( 'ownwork', 'fromflickr', 'fromwikimedia', 'fromgov');
		foreach($formNames as $formName){
			// Directly reference a form Name then its "english"
			if( $formName == $langKey )
				return 'en';
			// If the langKey includes a form name (ie esownwork)
			// then strip the form name use that as the language key
			if( strpos($langKey, $formName)!==false){
				return str_replace($formName, '', $langKey);
			}
		}
		//else just return the key unchanged:
		return $langKey;
	}
	/**
	 * Get a file path for a given class
	 *
	 * @param {String} $reqClass Class key to get the path for
	 * @return path of the class or "false"
	 */
	public static function getJsPathFromClass( $reqClass ){
		global $wgJSAutoloadClasses;
		// Make sure the class is loaded:
		jsClassLoader::loadClassPaths();
		if ( isset( $wgJSAutoloadClasses[$reqClass] ) ) {
			return $wgJSAutoloadClasses[$reqClass];
		} else {
			return false;
		}
	}

	/**
	 * Retrieve the js file into a string, updates error_msg if not retrivable.
	 *
	 * @param {String} $filePath File to get
	 * @return {String} of the file contents
	 */
	function doGetJsFile( $filePath ) {
		global $IP;

		// Load the file
		wfSuppressWarnings();
		$str = file_get_contents( "{$IP}/{$filePath}" );
		wfRestoreWarnings();

		if ( $str === false ) {
			// @@todo check PHP error level. Don't want to expose paths if errors are hidden.
			$this->error_msg .= 'Requested File: ' . htmlspecialchars( $IP.'/'.$filePath ) . ' could not be read' . "\n";
			return false;
		}
		return $str;
	}

	/**
	 * Process the javascript string
	 *
	 * Strips debug statements:  mw.log( 'msg' );
	 * Localizes the javascript calling the languageMsgReplace function
	 *
	 * @param {String} $str Javascript string to be processed.
	 * @return processed javascript string
	 */
	function doProcessJs( $str ){
		global $wgEnableScriptLocalization;
		// Strip out js_log debug lines (if not in debug mode)
		if( !$this->debug )
			 $str = preg_replace('/\n\s*mw\.log\(([^\)]*\))*\s*[\;\n]/U', "\n", $str);

		// Do language swap by index:
		if ( $wgEnableScriptLocalization ){
			$inx = self::getAddMessagesIndex( $str );
			if($inx){
				$translated = $this->languageMsgReplace( substr($str, $inx['s'], ($inx['e']-$inx['s']) ));
				//return the final string (without double {})
				return substr($str, 0, $inx['s']-1) . $translated . substr($str, $inx['e']+1);
			}
		}
		//return the js str unmodified if we did not transform with the localisation.
		return $str;
	}

	/**
	 * Get the "addMesseges" function index ( for replacing msg text with localized json )
	 *
	 * @param {String} $str Javascript string to grab msg text from
	 * @return {Array} Array with start and end points character indexes
	 */
	static public function getAddMessagesIndex( $str ){
		$returnIndex = array();
		preg_match('/mw.addMessages\s*\(\s*\{/', $str, $matches, PREG_OFFSET_CAPTURE );
		if( count($matches) == 0){
			return false;
		}
		if( count( $matches ) > 0 ){
			//offset + match str length gives startIndex:
			$returnIndex['s'] = strlen( $matches[0][0] ) + $matches[0][1];
			$foundMatch = true;
		}
		$ignorenext = false;
		$inquote = false;

		// Look for closing } not inside quotes::
		for ( $i = $returnIndex['s']; $i < strlen( $str ); $i++ ) {
			$char = $str[$i];
			if ( $ignorenext ) {
				$ignorenext = false;
			} else {
				// Search for a close } that is not in quotes or escaped
				switch( $char ) {
					case '"':
						$inquote = !$inquote;
						break;
					case '}':
						if( ! $inquote){
							$returnIndex['e'] =$i;
							return $returnIndex;
						}
						break;
					case '\\':
						if ( $inquote ) $ignorenext = true;
						break;
				}
			}
		}
	}
	/**
	 * Generates an in-line addMessege call for page output.
	 * For use with OutputPage when the script-loader is disabled.
	 *
	 * @param {String} $class Name of class to get inin-lineline messages for.
	 * @return in-line msg javascript text or empty string if no msgs need to be localised.
	 */
	function getInlineMsgFromClass( $class ){
		$jsmsg = $this->getMsgKeysFromClass( $class );
		if( $jsmsg ){
			self::updateMsgKeys ( $jsmsg );
			return 'mw.addMessages(' . FormatJson::encode( $jsmsg ) . ');';
		}else{
			//if could not parse return empty string:
			return '';
		}
	}
	/**
	 * Get the set of message associated with a given javascript class
	 *
	 * @param {String} $class Class to restive msgs from
	 * @return {Array} decoded json array of message key value pairs
	 */
	function getMsgKeysFromClass( $class ){
		$filePath = self::getJsPathFromClass( $class );
		$str = $this->getScriptText($class,  $filePath);

		$inx = self::getAddMessagesIndex( $str );
		if(!$inx)
			return false;

		return FormatJson::decode( '{' . substr($str, $inx['s'], ($inx['e']-$inx['s'])) . '}', true);
	}

	/**
	 * Updates an array of messages with the wfMsgGetKey value
	 *
	 * @param {Array} $jmsg Associative array of message key -> message value pairs
	 * @param {String} $langCode Language code override
	 */
	static public function updateMsgKeys(& $jmsg, $langCode = false){
		global $wgLang;
		// Check the langCode
		if(!$langCode && $wgLang)
			$langCode = $wgLang->getCode();

		// Get the msg keys for the a json array
		foreach ( $jmsg as $msgKey => $na ) {
			$jmsg[ $msgKey ] = wfMsgGetKey( $msgKey, true, $langCode, false );
		}
	}

	/**
	 * Replace a string of json msgs with the translated json msgs.
	 *
	 * @param {String} $json_str Json string to be replaced
	 * @return {String} of msgs updated with the given language code
	 */
	function languageMsgReplace( $json_str ) {
		$jmsg = FormatJson::decode( '{' . $json_str . '}', true );
		// Do the language lookup
		if ( $jmsg ) {

			// See if any msgKey has the PLURAL template tag
			//package in PLURAL mapping
			self::updateMsgKeys( $jmsg, $this->langCode );

			// Return the updated JSON with Msgs:
			return FormatJson::encode( $jmsg );
		} else {
			// Could not parse JSON return error: (maybe a alert?)
			//we just make a note in the code, visitors will get the fallback language,
			//developers will read the js source when its not behaving as expected.
			return "\n/*
* Could not parse JSON language messages in this file,
* Please check that mw.addMessages call contains valid JSON (not javascript)
*/\n\n" . $json_str; //include the original fallback msg string
		}
	}
}

/*
*  A simple version of HTMLFileCache so that the scriptLoader can operate stand alone
*/
class simpleFileCache {
	var $mFileCache;
	var $filename = null;
	var $requestKey = null;

	/**
	 * Constructor
	 *
	 * @param {String} $requestKey Request key for unique identifying this cache file
	 */
	public function __construct( $requestKey ) {
		$this->requestKey = $requestKey;
		$this->getCacheFileName();
	}

	/**
	 * Get cache file file Name based on $requestKey and if gzip is enabled or not
	 * Updates the local filename var
	 *
	 * @return {String} file path
	 */
	public function getCacheFileName() {
		global $wgUseGzip, $wgScriptCacheDirectory;

		$hash = md5( $this->requestKey );
		# Avoid extension confusion
		$key = str_replace( '.', '%2E', urlencode( $this->requestKey ) );

		$hash1 = substr( $hash, 0, 1 );
		$hash2 = substr( $hash, 0, 2 );
		$this->filename = "{$wgScriptCacheDirectory}/{$hash1}/{$hash2}/{$this->requestKey}.js";

		// Check for defined files::
		if( is_file( $this->filename ) )
			return $this->filename;

		// Check for non-config based gzip version already there?
		if( is_file( $this->filename . '.gz') ){
			$this->filename .= '.gz';
			return $this->filename;
		}
		//Update the name based on the $wgUseGzip config var
		if ( isset($wgUseGzip) && $wgUseGzip )
			$this->filename.='.gz';

		return $this->filename;
	}
	/**
	 * Checks if file is cached
	 */
	public function isFileCached() {
		return file_exists( $this->filename );
	}

	/**
	 * Loads and outputs the file from file cache
	 */
	public function loadFromFileCache() {
		if ( jsScriptLoader::clientAcceptsGzip() && substr( $this->filename, -3 ) == '.gz'  ) {
			header( 'Content-Encoding: gzip' );
			readfile( $this->filename );
			return true;
		}
		// Output without gzip:
		if ( substr( $this->filename, -3 ) == '.gz' ) {
			readgzfile( $this->filename );
		} else {
			readfile( $this->filename );
		}
		return true;
	}
	/**
	 * Saves text string to file
	 * @param unknown_type $text
	 */
	public function saveToFileCache( &$text ) {
		global $wgUseFileCache, $wgUseGzip;
		if ( !$wgUseFileCache ) {
			return 'Error: Called saveToFileCache with $wgUseFileCache off';
		}
		if ( strcmp( $text, '' ) == 0 )
		return 'saveToFileCache: empty output file';

		if ( $wgUseGzip ) {
			$outputText = gzencode( trim( $text ) );
		} else {
			$outputText = trim( $text );
		}

		// Check the directories. If we could not create them, error out.
		$status = $this->checkCacheDirs();

		if ( $status !== true )
		return $status;
		$f = fopen( $this->filename, 'w' );
		if ( $f ) {
			fwrite( $f, $outputText );
			fclose( $f );
		} else {
			return 'Could not open file for writing. Check your cache directory permissions?';
		}
		return true;
	}
	/**
	 * Checks cache directories and makes the dirs if not present
	 */
	protected function checkCacheDirs() {
		$mydir2 = substr( $this->filename, 0, strrpos( $this->filename, '/' ) ); # subdirectory level 2
		$mydir1 = substr( $mydir2, 0, strrpos( $mydir2, '/' ) ); # subdirectory level 1

		// Suppress error so javascript can format it
		if ( @wfMkdirParents( $mydir1 ) === false || @wfMkdirParents( $mydir2 ) === false ) {
			return 'Could not create cache directory. Check your cache directory permissions?';
		} else {
			return true;
		}
	}
}
