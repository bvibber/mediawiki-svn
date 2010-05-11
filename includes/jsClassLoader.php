<?php
/**
 * The javascript class loader handles loading lists of available
 * javascript classes into php from their defined locations in javascript.
 */

if ( !defined( 'MEDIAWIKI' ) ) die( 1 );

class jsClassLoader {
	// The list of mwEmbed core components that make up the base mwEmbed class
	private static $coreComponentsList = array();

	// The list of mwEmbed modules that are enabled
	private static $moduleList = array();

	// Stores the contents of the combined loader.js files
	private static $combinedLoadersJs = '';

	// Reg Exp that supports extracting classes from loaders
	private static $classReplaceExp = '/mw\.addClassFilePaths\s*\(\s*{(.*)}\s*\)\s*\;/siU';

	// Flag to specify if the javascript class paths have been loaded.
	private static $classesLoaded = false;

	// The current directory context. Used in loading javascript modules outside of the mwEmbed folder
	private static $directoryContext = '';

	/**
	 * Get the javascript class paths from javascript files
	 */
	public static function loadClassPaths(){
		global $wgMwEmbedDirectory, $wgExtensionJavascriptLoader,
		$wgScriptLoaderNamedPaths, $wgExtensionMessagesFiles, $IP;

		// Only run once
		if( self::$classesLoaded ) {
			return false;
		}
		self::$classesLoaded = true;

		$mwEmbedAbsolutePath = ( $wgMwEmbedDirectory == '' ) ? $IP :  $IP .'/' .$wgMwEmbedDirectory;
		// Add the mwEmbed localizations
		$wgExtensionMessagesFiles[ 'mwEmbed' ] = $mwEmbedAbsolutePath . '/languages/mwEmbed.i18n.php';

		// Load javascript classes from mwEmbed.js
		if ( !is_file( $mwEmbedAbsolutePath . '/loader.js' ) ) {
			// throw error no mwEmbed found
			throw new MWException( "mwEmbed loader.js missing check \$wgMwEmbedDirectory path\n" );
			return false;
		}

		// Read the mwEmbed loader file:
		$fileContent = file_get_contents( $mwEmbedAbsolutePath . '/loader.js' );

		// Get class paths from mwEmbed.js
		self::$directoryContext = $wgMwEmbedDirectory;
		self::proccessLoaderContent( $fileContent );

		// Get the list of core component into self::$coreComponentsList
		preg_replace_callback(
			'/mwCoreComponentList\s*\=\s*\[(.*)\]/siU',
			'jsClassLoader::preg_buildComponentList',
			$fileContent
		);

		// Get the list of enabled modules into $moduleList
		preg_replace_callback(
			'/mwEnabledModuleList\s*\=\s*\[(.*)\]/siU',
			'jsClassLoader::preg_buildModuleList',
			$fileContent
		);

		// Get all the classes from the enabled mwEmbed modules folder
		foreach( self::$moduleList as  $na => $moduleName){
			$relativeSlash = ( $wgMwEmbedDirectory == '' )? '' : '/';
			self::proccessModulePath( $wgMwEmbedDirectory . $relativeSlash . 'modules/' . $moduleName );
		}

		// Get all the extension loader paths registered mwEmbed modules
		foreach( $wgExtensionJavascriptLoader as $na => $loaderPath){
			// Setup the directory context for extensions relative to loader.js file:
			$modulePath = str_replace('loader.js', '' , $loaderPath);
			self::proccessModulePath( $modulePath );
		}
	}
	/**
	 * Process a loader path, passes off to proccessLoaderContent
	 *
	 * @param String $path Path to module to be processed
	 */
	private static function proccessModulePath( $path ){
		global $wgExtensionMessagesFiles;
		// Get the module name
		$moduleName = end( explode('/', $path ) );

		// Set the directory context for relative js/css paths
		self::$directoryContext = $path;

		// Check for the loader.js
		if( !is_file( $path . '/loader.js' ) ){
			throw new MWException( "Module missing loader.js in root \n" );
			return false;
		}

		$fileContent = file_get_contents( $path . '/loader.js');
		self::proccessLoaderContent( $fileContent );

		$i18nPath = realpath( $path . '/' . $moduleName . '.i18n.php' );

		// Add the module localization file if present:
		if( is_file( $i18nPath ) ) {
			$wgExtensionMessagesFiles[ $moduleName ] = $i18nPath;
		}
	}

	/**
	 * Process loader content
	 *
	 * parses the loader files and adds
	 *
	 * @param String $fileContent content of loader.js file
	 */
	private static function proccessLoaderContent( & $fileContent ){
		// Add the mwEmbed loader js to its global collector:
		self::$combinedLoadersJs .=  $fileContent;

		preg_replace_callback(
			self::$classReplaceExp,
			'jsClassLoader::preg_classPathLoader',
			$fileContent
		);
	}
	/**
	 * Get the language file javascript
	 * @param String $languageJs The language file javascript
	 */
	public static function getLanguageJs( $langKey = 'en' ){
		global $wgMwEmbedDirectory;
		$path =  $wgMwEmbedDirectory . '/languages/classes/Language' . ucfirst( $langKey ) . '.js';
		if( is_file( $path ) ){
			$languageJs = file_get_contents( $path );
			return $languageJs;
		}
		return '';
	}

	/**
	 * Get combined core component javascript
	 *
	 * NOTE: Component JS is javascript that is part of the
	 * core mwEmbed javascript lib but in a separate file
	 * for core library maintainability
	 *
	 * @return String combined component javascript
	 */
	public static function getCombinedComponentJs( $scriptLoader ){
		self::loadClassPaths();
		$jsOut = '';
		foreach(  self::$coreComponentsList as $componentClassName ) {
			// Output the core component via the script loader:
			$jsOut .= $scriptLoader->getLocalizedScriptText( $componentClassName );
		}
		return $jsOut;
	}

	/**
	 * Get the combined loader javascript
	 *
	 * @return the combined loader jss
	 */
	public static function getCombinedLoaderJs(){
		self::loadClassPaths();
		return self::$combinedLoadersJs;
	}

	/**
	 * Get the list of enabled modules
	 */
	public static function getModuleList(){
		self::loadClassPaths();
		return self::$moduleList;
	}
	/**
	 * Build a list of components to be included with mwEmbed
	 */
	private static function preg_buildComponentList( $jsvar ){
		if(! isset( $jsvar[1] )){
			return false;
		}
		$componentSet = explode(',', $jsvar[1] );
		foreach( $componentSet as $na => $componentName ) {
			$componentName = str_replace( array( '../', '\'', '"'), '', trim( $componentName ));
			// Add the component to the $coreComponentsList
			if( trim( $componentName ) != '' ) {
				array_push( self::$coreComponentsList, trim( $componentName ) );
			}
		}
	}

	/**
	 * Build the list of modules from the mwEnabledModuleList replace callback
	 * @param String $jsvar Coma delimited list of modules
	 */
	private static function preg_buildModuleList( $jsvar ){
		global $IP, $wgMwEmbedDirectory;
		if(! isset( $jsvar[1] )){
			return false;
		}
		$moduleSet = explode(',', $jsvar[1] );

		$mwEmbedAbsolutePath = ( $wgMwEmbedDirectory == '' )? $IP:  $IP .'/' .$wgMwEmbedDirectory;

		foreach( $moduleSet as $na => $moduleName ){
			// Skip empty module names
			if(trim( $moduleName ) == '' ){
				continue;
			}
			$moduleName = str_replace( array( '../', '\'', '"'), '', trim( $moduleName ));
			// Check if there is there are module loader files
			if( is_file( $mwEmbedAbsolutePath . '/modules/' . $moduleName . '/loader.js' )){
				array_push( self::$moduleList, $moduleName );
			} else {
				// Not valid module ( missing loader.js )
				throw new MWException( "Missing module: $moduleName \n" );
			}
		}
	}
	/**
	 * Adds javascript autoloader class names and paths
	 * to $wgScriptLoaderNamedPaths global
	 *
	 * @param string $jvar Json string with class name list
	 */
	private static function preg_classPathLoader( $jsvar ) {
		global $wgScriptLoaderNamedPaths;
		if ( !isset( $jsvar[1] ) ) {
			return false;
		}

		$jClassSet = FormatJson::decode( '{' . $jsvar[1] . '}', true );
		// Check for null json decode:
		if( $jClassSet == NULL ){
			return false;
		}

		foreach ( $jClassSet as $className => $classPath ) {
			// Strip $ from class (as they are stripped on URL request parameter input)
			$className = str_replace( '$', '', $className );
			$classPath =  ( self::$directoryContext == '' )? $classPath :  self::$directoryContext . '/' . $classPath;
			$wgScriptLoaderNamedPaths[ $className ] = $classPath;
		}
	}
}

