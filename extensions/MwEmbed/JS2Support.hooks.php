<?php

/**
* Setup JS2 hooks
*/
class JS2SupportHooks {

	public static function setup(){
		global $wgOut, $js2Dir, $wgAutoloadClasses, $wgResourceLoaderNamedPaths,
		$wgExtensionJavascriptModules, $wgEnableTestJavascriptModules;

		// Remap output page as part of the extension setup
		$wgOut = new StubObject( 'wgOut', 'ResourceLoaderOutputPage' );
		$wgAutoloadClasses[ 'ResourceLoaderOutputPage' ] = $js2Dir . 'ResourceLoaderOutputPage.php';

		// Include all the mediaWiki autoload classes:
		require( $js2Dir . 'JS2AutoLoader.php');

		// Add the core test module loaders (extensions can add their own test modules referencing this global )
		if( $wgEnableTestJavascriptModules ) {
			$wgExtensionJavascriptModules['JS2Tests'] = 'extensions/JS2Support/tests';
		}

		// Update all the javascript modules classNames and localization by reading respective loader.js files
		// @dependent on all extensions defining $wgExtensionJavascriptModules paths in config file ( not after setup )
		//
		// @NOTE runtime for loadResourcePaths with 8 or so loaders with 100 or so named paths is
		// is around .002 seconds on my laptop. If this is a concern we can switch resource defines to php
		NamedResourceLoader::loadResourcePaths();
	}

	public static function addJSVars( &$vars ) {
		global $wgExtensionAssetsPath;
		$vars = array_merge( $vars,
			array(
				'wgScriptLoaderLocation' => $wgExtensionAssetsPath . '/JS2Support/mwResourceLoader.php'
			)
		);
		return true;
	}

	public static function addPreferences( $user, &$preferences ){
		// Add the preference to enable / disable debugging:
		$preferences['scriptdebug'] = array(
			'type' => 'toggle',
			'label-message' => 'js2support-debug-preference',
			'section' => 'misc/script-debug'
		);
		return true;
	}
}