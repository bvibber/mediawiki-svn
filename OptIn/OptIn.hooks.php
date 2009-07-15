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
		global $wgExtNewTables, $wgExtNewFields;
		
		$wgExtNewTables[] = array(
			'optin_survey',
			dirname( __FILE__ ) . '/OptIn.sql'
		);
		$wgExtNewFields[] = array(
			'optin_survey',
			'ois_type',
			dirname( __FILE__ ) . '/OptIn.patch.ois_type.sql'
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
		
		$fromquery = $wgRequest->getValues();
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
		$addLinks = array(
			'acaibeta' => array(
				'text' => SpecialOptIn::isOptedIn( $wgUser ) ?
					wfMsg( 'optin-leave' ) :
					wfMsg( 'optin-try' ),
				'href' => $link,
				'class' => 'no-text-transform'
			)
		);
		
		// For opted-in users, add a feedback link
		if ( SpecialOptIn::isOptedIn( $wgUser ) ) {
			$query['opt'] = 'feedback';
			$link = SpecialPage::getTitleFor( 'OptIn' )->getFullURL( $query );
			$addLinks['betafeedback'] = array(
				'text' => wfMsg( 'optin-feedback' ),
				'href' => $link,
				'class' => 'no-text-transform'
			);
		}
		
		// Add the links
		$personal_urls = array_merge( $addLinks, $personal_urls ); 
		return true;
	}
}