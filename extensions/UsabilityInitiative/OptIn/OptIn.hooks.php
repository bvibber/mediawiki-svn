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
		global $wgOptInNeverShowPersonalLink, $wgRequest;
		
		if ( $wgOptInNeverShowPersonalLink ||
				( !SpecialOptIn::isOptedIn( $wgUser ) &&
				!$wgOptInAlwaysShowPersonalLink ) )
			// Don't show the link
			return true;
		
		// Loads opt-in messages
		wfLoadExtensionMessages( 'OptIn' );
		
		$fromquery = $wgRequest->data;
		unset( $fromquery['title'] );
		$query = array(	'from' => $title->getPrefixedDBKey(),
				'fromquery' => wfArrayToCGI( $fromquery )
		);
		// Make sure we don't create links that return to
		// Special:UsabilityOptIn itself
		if ( $title->equals( SpecialPage::getTitleFor( 'OptIn' ) ) ) {
			$query['from'] = $wgRequest->getVal( 'from' );
			$query['fromquery'] = $wgRequest->getVal( 'fromquery' );
		}
		$link = SpecialPage::getTitleFor( 'OptIn' )->getFullURL( $query );
		
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
