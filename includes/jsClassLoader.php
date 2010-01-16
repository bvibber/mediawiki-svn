<?php

if ( !defined( 'MEDIAWIKI' ) ) die( 1 );

class jsClassLoader {
	private static $moduleList = array();
	private static $combinedLoadersJs = '';
	private static $classReplaceExp = '/mw\.addClassFilePaths\s*\(\s*{(.*)}\s*\)\s*\;/siU';

	private static $loadClassFlag = false;
	private static $directoryContext = '';
	/**
	 * Get the javascript class paths from javascript files
	 *
	 * Note:: if it is ~too costly~ to parse js we could cache in DB per file modified time
	 */
	public static function loadClassPaths(){
		global $wgMwEmbedDirectory, $wgExtensionJavascriptLoader,
			$wgJSAutoloadClasses, $wgJSAutoloadLocalClasses, $IP;

		// Only run "once"
		if( self::$loadClassFlag )
			return false;
		self::$loadClassFlag = true;

		// Load classes from mediaWiki $wgJSAutoloadLocalClasses var:
		$wgJSAutoloadClasses = array_merge( $wgJSAutoloadClasses, $wgJSAutoloadLocalClasses );

		// Load classes from  mwEmbed.js
		if ( !is_file( $wgMwEmbedDirectory . 'mwEmbed.js' ) ) {
			// throw error no mwEmbed found
			throw new MWException( "mwEmbed.js missing check \$wgMwEmbedDirectory path\n" );
			return false;
		}

		// Read the file:
		$fileContent = file_get_contents( $wgMwEmbedDirectory . 'mwEmbed.js' );
		// Get class paths from mwEmbed.js
		self::$directoryContext = $wgMwEmbedDirectory;
		preg_replace_callback(
			self::$classReplaceExp,
			'jsClassLoader::preg_classPathLoader',
			$fileContent
		);

		// Get the list of enabled modules into $wgJSModuleList
		preg_replace_callback(
			'/mwEnabledModuleList\s*\=\s*\[(.*)\]/siU',
			'jsClassLoader::preg_buildModuleList',
			$fileContent
		);

		// Get all the classes from the loader files:
		foreach( self::$moduleList as  $na => $moduleName){
			// Setup the directory context:
			self::$directoryContext = $wgMwEmbedDirectory;
			self::proccessLoaderPath( $wgMwEmbedDirectory .
				'modules/' . $moduleName . '/loader.js' );

		}

		// Get all the extension loader paths registered mwEmbed modules
		foreach( $wgExtensionJavascriptLoader as $na => $loaderPath){
			// Setup the directory context:
			self::$directoryContext = 'extensions/' .str_replace('loader.js', '' , $loaderPath);
			self::proccessLoaderPath( $IP . '/extensions/' .  $loaderPath );
		}
	}
	/**
	 * Process a loader path
	 *
	 * @param String $path
	 */
	private static function proccessLoaderPath( $path ){
		$fileContent = file_get_contents( $path );

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

