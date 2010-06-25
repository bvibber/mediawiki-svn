<?php
class JSBreadCrumbsHooks {

	function addResources( $out ) {
		global $wgExtensionAssetsPath;

		$out->addScriptFile( "$wgExtensionAssetsPath/JSBreadCrumbs/js/BreadCrumbs.js", 2 );
		$out->addExtensionStyle( "$wgExtensionAssetsPath/JSBreadCrumbs/css/BreadCrumbs.css?1" );

		return true;
	}

	/**
	 * MakeGlobalVariablesScript hook
	 */
	public static function addJSVars( $vars ) {
		global $wgJSBreadCrumbsMaxCrumbs, $wgJSBreadCrumbsSeparator;

		$variables = array();

		$variables['wgJSBreadCrumbsMaxCrumbs'] = $wgJSBreadCrumbsMaxCrumbs;
		$variables['wgJSBreadCrumbsSeparator'] = $wgJSBreadCrumbsSeparator;
		$variables['wgJSBreadCrumbsCookiePath'] = $wgJSBreadCrumbsCookiePath;

		$vars = array_merge( $vars, $variables );
		return true;
	}
}
