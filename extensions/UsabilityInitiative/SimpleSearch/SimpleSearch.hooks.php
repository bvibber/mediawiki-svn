<?php
/**
 * Hooks for Usability Initiative SimpleSearch extension
 *
 * @file
 * @ingroup Extensions
 */

class SimpleSearchHooks {

	/* Static Functions */

	/**
	 * AjaxAddScript hook
	 * Initializes the component
	 */
	public static function initialize( $out ) {
		global $wgSimpleSearchStyleVersion;

		UsabilityInitiativeHooks::initialize();
		// Adds script to document
		UsabilityInitiativeHooks::addScript(
			'SimpleSearch/SimpleSearch.js', $wgSimpleSearchStyleVersion
		);
		// Internationalization
		wfLoadExtensionMessages( 'SimpleSearch' );
		// Adds messages to page
		UsabilityInitiativeHooks::addMessages(
			array( 'simplesearch-search', 'simplesearch-containing' )
		);

		// Continue
		return true;
	}
}
