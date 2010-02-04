<?php
/**
 * The javascript class loader handles loading lists of available
 * javascript classes into php from their defined locations in javascript.
 */

if ( !defined( 'MEDIAWIKI' ) ) die( 1 );

class jsClassLoader {
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
			$wgJSAutoloadClasses, $wgJSAutoloadLocalClasses, $IP;

		// Only run once
		if( self::$classesLoaded )
			return false;
		self::$classesLoaded = true;

		// Load classes from mediaWiki $wgJSAutoloadLocalClasses var:
		$wgJSAutoloadClasses = array_merge( $wgJSAutoloadClasses, $wgJSAutoloadLocalClasses );

		// Load javascript classes from mwEmbed.js
		if ( !is_file( $wgMwEmbedDirectory . 'mwEmbed.js' ) ) {
			// throw error no mwEmbed found
			throw new MWException( "mwEmbed.js missing check \$wgMwEmbedDirectory path\n" );
			return false;
		}

		// Read the mwEmbed loader file:
		$fileContent = file_get_contents( $wgMwEmbedDirectory . 'loader.js' );

		// Get class paths from mwEmbed.js
		self::$directoryContext = $wgMwEmbedDirectory;
		self::proccessLoaderContent( $fileContent );

		// Get the list of enabled modules into $wgJSModuleList
		preg_replace_callback(
			'/mwEnabledModuleList\s*\=\s*\[(.*)\]/siU',
			'jsClassLoader::preg_buildModuleList',
			$fileContent
		);

		// Get all the classes from the loader files:
		foreach( self::$moduleList as  $na => $moduleName){
			// Setup the directory context for mwEmbed modules:
			self::$directoryContext = $wgMwEmbedDirectory;

			self::proccessLoaderPath( $wgMwEmbedDirectory .
				'modules/' . $moduleName . '/loader.js' );
		}

		// Get all the extension loader paths registered mwEmbed modules
		foreach( $wgExtensionJavascriptLoader as $na => $loaderPath){
			// Setup the directory context for extensions
			self::$directoryContext = 'extensions/' .str_replace('loader.js', '' , $loaderPath);
			self::proccessLoaderPath( $IP . '/extensions/' .  $loaderPath );
		}
	}

	/**
	 * Process a loader path, passes off to proccessLoaderContent
	 *
	 * @param String $path
	 */
	private static function proccessLoaderPath( $path ){
		// Get the loader content
		$fileContent = file_get_contents( $path );
		self::proccessLoaderContent( $fileContent );
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
	 * Get the combined loader javascript
	 *
	 * @return the combined loader jss
	 */
	public static function getCombinedLoaderJs(){
		self::loadClassPaths();
		return self::$combinedLoadersJs;
	}

	/**
	 * Build the list of modules from the mwEnabledModuleList replace callback
	 * @param String $jsvar Coma delimited list of modules
	 */
	private static function preg_buildModuleList( $jsvar ){
		global $wgMwEmbedDirectory;
		if(! isset( $jsvar[1] )){
			return false;
		}
		$moduleSet = explode(',', $jsvar[1] );

		foreach( $moduleSet as $na => $module ){
			$moduleName = str_replace( array( '../', '\'', '"'), '', trim( $module ));
			// Check if there is there are module loader files
			if( is_file( $wgMwEmbedDirectory . 'modules/' . $moduleName . '/loader.js' )){
				array_push( self::$moduleList, $moduleName );
			}
		}
		// Enabled modules is not reused.
		return '';
	}

	/**
	 * Adds javascript autoloader class names and paths
	 * to $wgJSAutoloadClasses global
	 *
	 * @param string $jvar Json string with class name list
	 */
	private static function preg_classPathLoader( $jsvar ) {
		global $wgJSAutoloadClasses;
		if ( !isset( $jsvar[1] ) )
			return false;

		$jClassSet = FormatJson::decode( '{' . $jsvar[1] . '}', true );
		foreach ( $jClassSet as $jClass => $jPath ) {
			// Strip $ from class (as they are stripped on URL request parameter input)
			$jClass = str_replace( '$', '', $jClass );
			$wgJSAutoloadClasses[ $jClass ] =  self::$directoryContext . $jPath;
		}
	}
}

