<?php

if ( !defined( 'MEDIAWIKI' ) ) die( 1 );

global $wgJSAutoloadLocalClasses, $wgMwEmbedDirectory;

/**
 * loads javascript class name paths from mwEmbed.js
 */
function wfLoadMwEmbedClassPaths ( ) {
	global $wgMwEmbedDirectory;
	// Load classes from  mwEmbed.js
	if ( is_file( $wgMwEmbedDirectory . 'mwEmbed.js' ) ) {

		// NOTE: ideally we could cache this json var and or update it php side per release

		// Read the file:
		$file_content = file_get_contents( $wgMwEmbedDirectory . 'mwEmbed.js' );
		// Call jsClassPathLoader() for each lcPaths() call in the JS source
		$replace_test = preg_replace_callback(
			'/mw\.addClassFilePaths\s*\(\s*{(.*)}\s*\)\s*/siU',
			'wfClassPathLoader',
			$file_content
		);
	}
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
