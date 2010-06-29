<?php
class JSBreadCrumbsHooks {

	/**
	 * BeforePageDisplay hook
	 */
	public static function addResources( $out ) {
		global $wgExtensionAssetsPath;

		$out->addScriptFile( "$wgExtensionAssetsPath/JSBreadCrumbs/js/BreadCrumbs.js", 5 );
		$out->addExtensionStyle( "$wgExtensionAssetsPath/JSBreadCrumbs/css/BreadCrumbs.css?1" );

		return true;
	}

	/**
	 * MakeGlobalVariablesScript hook
	 */
	public static function addJSVars( $vars ) {
		global $wgJSBreadCrumbsMaxCrumbs, $wgJSBreadCrumbsSeparator, $wgJSBreadCrumbsCookiePath;

		wfLoadExtensionMessages( 'JSBreadCrumbs' );

		// Allow localized separator to be overriden
		if ( $wgJSBreadCrumbsSeparator !== '' ) {
			$separator = $wgJSBreadCrumbsSeparator;
		} else {
			$separator = wfMsg( "jsbreadcrumbs-separator" );
		}

		$variables = array();

		$variables['wgJSBreadCrumbsMaxCrumbs'] = $wgJSBreadCrumbsMaxCrumbs;
		$variables['wgJSBreadCrumbsSeparator'] = $separator;
		$variables['wgJSBreadCrumbsCookiePath'] = $wgJSBreadCrumbsCookiePath;
		$variables['wgJSBreadCrumbsLeadingDescription'] = wfMsg( "jsbreadcrumbs-leading-description" );

		$vars = array_merge( $vars, $variables );
		return true;
	}
}
