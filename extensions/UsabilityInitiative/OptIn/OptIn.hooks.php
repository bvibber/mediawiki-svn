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
		global $wgOptInAlwaysShowPersonalLink, $wgOptInNeverShowPersonalLink;
		
		// Checks if...
		if (
			// This should be shown because...
			(
				// The user is opted in
				SpecialOptIn::isOptedIn( $wgUser ) ||
				// Or the link should always be shown
				$wgOptInAlwaysShowPersonalLink
			) &&
			// And the link is allowed to be shown
			!$wgOptInNeverShowPersonalLink
		) {
			// Loads opt-in messages
			wfLoadExtensionMessages( 'OptIn' );
			// Inserts a link into personal tools
			$personal_urls = array_merge(
				array(
					'acaibeta' => array(
						'text' => SpecialOptIn::isOptedIn( $wgUser ) ?
									wfMsg( 'optin-leave' ) :
									wfMsg( 'optin-try' ),
						'href' => SpecialPage::getTitleFor(
							'UsabilityInitiativeOptIn', $title->getFullText()
						)->getLocalUrl(),
						'class' => 'no-text-transform'
					)
				),
				$personal_urls
			);
		}
		return true;
	}
}
