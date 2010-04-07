<?php
/**
 * Hooks for Usability Initiative PrefSwitch extension
 *
 * @file
 * @ingroup Extensions
 */

class PrefSwitchHooks {

	/* Static Functions */
	
	public static function schema() {
		global $wgExtNewTables;
		$wgExtNewTables[] = array( 'prefswitch', dirname( __FILE__ ) . '/PrefSwitch.sql' );
		return true;
	}
	public static function personalUrls( &$personal_urls, &$title ) {
		global $wgUser, $wgRequest;		
		// Loads opt-in messages
		wfLoadExtensionMessages( 'PrefSwitchLink' );
		// Figure out the orgin to include in the link
		$fromquery = array();
		if ( !( $wgRequest->wasPosted() ) ) {
			$fromquery = $wgRequest->getValues();
			unset( $fromquery['title'] );
		}
		// Make sure we don't create links that return to Special:UsabilityPrefSwitch itself
		if ( $title->equals( SpecialPage::getTitleFor( 'PrefSwitch' ) ) ) {
			$query = array( 'from' => $wgRequest->getVal( 'from' ), 'fromquery' => $wgRequest->getVal( 'fromquery' ) );
		} else {
			$query = array(	'from' => $title->getPrefixedDBKey(), 'fromquery' => wfArrayToCGI( $fromquery ) );
		}
		// Inserts a link into personal tools
		$personal_urls = array_merge(
			array(
				'prefswitch-link' => array(
					'text' => wfMsg( 'prefswitch-link' ),
					'href' => SpecialPage::getTitleFor( 'PrefSwitch' )->getFullURL( $query ),
					'class' => 'no-text-transform',
				),
			),
			$personal_urls
		); 
		return true;
	}
}
