<?php
/**
 * Hooks for Usability Initiative OptIn extension
 *
 * @file
 * @ingroup Extensions
 */

class OptInHooks {

	/* Static Functions */
	public static function schema() {
		global $wgExtNewTables;
		$wgExtNewTables[] = array(
			'optin_survey',
			dirname( __FILE__ ) . '/OptIn.sql'
		);
		return true;
	}
	
	public static function personalUrls( &$personal_urls, &$title ) {
		global $wgUser;
		
		if ( SpecialOptIn::isOptedIn( $wgUser ) ) {
			wfLoadExtensionMessages( 'OptIn' );
			$personal_urls = array_merge(
				array(
					'optout' => array(
						'text' => wfMsg( 'optin-leave' ),
						'href' => SpecialPage::getTitleFor( 'UsabilityInitiativeOptIn' )->getFullURL()
					)
				),
				$personal_urls
			);
		}
		return true;
	}
}
