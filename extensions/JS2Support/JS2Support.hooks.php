<?php

/**
* Setup JS2 hooks
*/
class JS2SupportHooks {

	public static function setup(){
		global $wgOut, $js2Dir, $wgAutoloadClasses, $wgScriptLoaderNamedPaths,
		$wgExtensionJavascriptModules, $wgEnableTestJavascriptModules;

		// Remap output page as part of the extension setup
		$wgOut = new StubObject( 'wgOut', 'ScriptLoaderOutputPage' );
		$wgAutoloadClasses[ 'ScriptLoaderOutputPage' ] = $js2Dir . 'ScriptLoaderOutputPage.php';

		// Include all the mediaWiki autoload classes:
		require( $js2Dir . 'JS2AutoLoader.php');

		// Add the core test module loaders (extensions can add their own test modules referencing this global )
		if( $wgEnableTestJavascriptModules ) {
			$wgExtensionJavascriptModules['JS2Tests'] = 'extensions/JS2Support/tests';
		}

		// Update all the javascript modules classNames and localization by reading respective loader.js files
		// @dependent on all extensions defining $wgExtensionJavascriptModules paths in config file ( not in setup )
		//
		// @NOTE runtime for loadClassPaths with 8 or so loaders with 100 or so named paths is
		// is around .002 seconds on my laptop. Could probably be further optimized and of course it only runs
		// on non-cached pages.
		jsClassLoader::loadClassPaths();
	}

	public static function addJSVars( &$vars ) {
		global $wgExtensionAssetsPath;
		$vars = array_merge( $vars,
			array(
				'wgScriptLoaderLocation' => $wgExtensionAssetsPath . '/JS2Support/mwScriptLoader.php'
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