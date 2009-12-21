<?php

if ( !defined( 'MEDIAWIKI' ) ) die( 1 );

global $wgJSAutoloadLocalClasses, $wgMwEmbedDirectory;
global $wgJSModuleList;

// NOTE this is growing in complexity and globals sloppiness
// we should refactor as a class with some static methods

//Initialize $wgJSModuleList
$wgJSModuleList = array();

//Initialize $wgLoaderJs
$wgMwEmbedLoaderJs = '';

/**
 * Loads javascript class name paths from mwEmbed.js
 */
function wfLoadMwEmbedClassPaths ( ) {
	global $wgMwEmbedDirectory, $wgJSModuleList, $wgMwEmbedLoaderJs;
	//Set up the replace key:
	$ClassReplaceKey = '/mw\.addClassFilePaths\s*\(\s*{(.*)}\s*\)\s*\;/siU';
	// Load classes from  mwEmbed.js
	if ( is_file( $wgMwEmbedDirectory . 'mwEmbed.js' ) ) {
		// Read the file:
		$file_content = file_get_contents( $wgMwEmbedDirectory . 'mwEmbed.js' );
		// Call jsClassPathLoader() for each lcPaths() call in the JS source
		$replace_test = preg_replace_callback(
			$ClassReplaceKey,
			'wfClassPathLoader',
			$file_content
		);

		// Get the list of enabled modules into $wgJSModuleList
		$replace_test = preg_replace_callback(
			'/mwEnabledModuleList\s*\=\s*\[(.*)\]/siU',
			'wfBuildModuleList',
			$file_content
		);

		// Get all the classes from the loader files:
		foreach( $wgJSModuleList as  $na => $moduleName){
			$file_content = file_get_contents(
				$wgMwEmbedDirectory . 'modules/' . $moduleName . '/loader.js'
			);
			// Add the mwEmbed loader js to its global collector:
			$wgMwEmbedLoaderJs .=  $file_content;

			$replace_test.= preg_replace_callback(
				$ClassReplaceKey,
				'wfClassPathLoader',
				$file_content
			);
		}
	}
}
function wfBuildModuleList( $jsvar ){
	global $wgMwEmbedDirectory, $wgJSModuleList;
	if(! isset( $jsvar[1] )){
		return false;
	}
	$moduleSet = explode(',', $jsvar[1] );

	foreach( $moduleSet as $na => $module ){
		$moduleName = str_replace( array( '../', '\'', '"'), '', trim( $module ));
		// Check if there is there are module loader files
		if( is_file( $wgMwEmbedDirectory . 'modules/' . $moduleName . '/loader.js' )){
			array_push( $wgJSModuleList, $moduleName );
		}
	}
	return '';
}
function wfClassPathLoader( $jvar ) {
	global $wgJSAutoloadLocalClasses, $wgMwEmbedDirectory;
	if ( !isset( $jvar[1] ) )
		return false;

	$jClassSet = FormatJson::decode( '{' . $jvar[1] . '}', true );
	foreach ( $jClassSet as $jClass => $jPath ) {
		// Strip $ from jClass (as they are stripped on URL request parameter input)
		$jClass = str_replace( '$', '', $jClass );
		$wgJSAutoloadLocalClasses[ $jClass ] = $wgMwEmbedDirectory . $jPath;
	}

}
