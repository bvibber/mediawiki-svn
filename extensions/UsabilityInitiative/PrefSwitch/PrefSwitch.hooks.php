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
		global $wgExtNewTables, $wgExtModifiedFields;

		$dir = dirname( __FILE__ );

		$wgExtNewTables[] = array( 'prefswitch_survey', $dir  . '/PrefSwitch.sql' );
		$wgExtModifiedFields[] = array( '', '', $dir  . '/PrefSwitch-addusertext.sql' );
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
		$state = SpecialPrefSwitch::userState( $wgUser );
		if ( $state == 'on' ) {
			// Inserts a link into personal tools - this just gets people to the generic new features page
			$personal_urls = array_merge(
				array(
					"prefswitch-link-anon" => array(
						'text' => wfMsg( 'prefswitch-link-anon' ),
						'href' => SpecialPage::getTitleFor( 'PrefSwitch' )->getFullURL( $query ),
						'class' => 'no-text-transform',
					),
				),
				$personal_urls
			);
			// Make the next link go to the opt-out page
			$query['mode'] = 'off';
		}
		// Inserts a link into personal tools - Uses prefswitch-link-anon, prefswitch-link-on and prefswitch-link-off
		$personal_urls = array_merge(
			array(
				"prefswitch-link-{$state}" => array(
					'text' => wfMsg( 'prefswitch-link-' . $state ),
					'href' => SpecialPage::getTitleFor( 'PrefSwitch' )->getFullURL( $query ),
					'class' => 'no-text-transform',
				),
			),
			$personal_urls
		);
		return true;
	}
}
