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
		global $wgUser, $wgOptInAlwaysShowPersonalLink;
		global $wgOptInNeverShowPersonalLink;
		
		if ( $wgOptInNeverShowPersonalLink ||
				( !SpecialOptIn::isOptedIn( $wgUser ) &&
				!$wgOptInAlwaysShowPersonalLink ) )
			// Don't show the link
			return true;
		
		// Loads opt-in messages
		wfLoadExtensionMessages( 'OptIn' );
		
		// Make sure we don't create links that return to
		// Special:UsabilityOptIn itself
		$titleParts = explode( '/', $title->getText() );
		if ( $titleParts[0] == SpecialPage::getLocalNameFor( 'OptIn' ) )
			$link = $title->getLocalUrl();
		else
			$link = SpecialPage::getTitleFor(
					'OptIn', $title->getFullText()
				)->getLocalUrl();
		
		// Inserts a link into personal tools
		$personal_urls = array_merge(
			array(
				'acaibeta' => array(
					'text' => SpecialOptIn::isOptedIn( $wgUser ) ?
						wfMsg( 'optin-leave' ) :
						wfMsg( 'optin-try' ),
					'href' => $link,
					'class' => 'no-text-transform'
				)
			),
			$personal_urls
		);
		return true;
	}
}
