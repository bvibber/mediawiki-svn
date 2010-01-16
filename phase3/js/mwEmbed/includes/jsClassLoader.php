<?php

if ( !defined( 'MEDIAWIKI' ) ) die( 1 );

global $wgJSAutoloadLocalClasses, $wgMwEmbedDirectory;
// NOTE this is growing in complexity and globals sloppiness


class jsClassLoader {
	private static $moduleList = array();
	private static $combinedLoadersJs = '';
	private static $classReplaceExp = '/mw\.addClassFilePaths\s*\(\s*{(.*)}\s*\)\s*\;/siU';

	private static $loadClassFlag = false;
	/**
	 * Get the javascript class paths
	 */
	public static function loadClassPaths(){
		global $wgMwEmbedDirectory, $wgJSModuleLoaderPaths;

		// Only run "once"
		if( self::$loadClassFlag )
			return false;
		self::$loadClassFlag = true;

		// Load classes from  mwEmbed.js
		if ( !is_file( $wgMwEmbedDirectory . 'mwEmbed.js' ) ) {
			// throw error no mwEmbed found
			throw new MWException( "mwEmbed.js missing check \$wgMwEmbedDirectory path\n" );
			return false;
		}
		// Read the file:
		$file_content = file_get_contents( $wgMwEmbedDirectory . 'mwEmbed.js' );
		// Get class paths from mwEmbed.js
		$replace_test = preg_replace_callback(
			self::$classReplaceExp,
			'jsClassLoader::preg_classPathLoader',
			$file_content
		);

		// Get the list of enabled modules into $wgJSModuleList
		$replace_test = preg_replace_callback(
			'/mwEnabledModuleList\s*\=\s*\[(.*)\]/siU',
			'jsClassLoader::preg_buildModuleList',
			$file_content
		);

		// Get all the classes from the loader files:
		foreach( self::$moduleList as  $na => $moduleName){
			$file_content = file_get_contents(
				$wgMwEmbedDirectory . 'modules/' . $moduleName . '/loader.js'
			);
			// Add the mwEmbed loader js to its global collector:
			self::$combinedLoadersJs .=  $file_content;

			$replace_test.= preg_replace_callback(
				self::$classReplaceExp,
				'jsClassLoader::preg_classPathLoader',
				$file_content
			);
		}

		// Get all the classes from extensions registered mwEmbed modules

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
	 * to $wgJSAutoloadLocalClasses global
	 *
	 * Use $wgJSAutoloadLocalClasses to support manual adding of class name / paths
	 *
	 * @param string $jvar Json string with class name list
	 */
	private static function preg_classPathLoader( $jsvar ) {
		global $wgJSAutoloadLocalClasses, $wgMwEmbedDirectory;
		if ( !isset( $jsvar[1] ) )
			return false;

		$jClassSet = FormatJson::decode( '{' . $jsvar[1] . '}', true );
		foreach ( $jClassSet as $jClass => $jPath ) {
			// Strip $ from jClass (as they are stripped on URL request parameter input)
			$jClass = str_replace( '$', '', $jClass );
			$wgJSAutoloadLocalClasses[ $jClass ] = $wgMwEmbedDirectory . $jPath;
		}
	}
}

