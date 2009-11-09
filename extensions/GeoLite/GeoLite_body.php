<?php 
/**
 * Special Page for GeoLite
 *
 * @file
 * @ingroup Extensions
 */

// Special page GeoLite

class SpecialGeoLite extends UnlistedSpecialPage {

        /* Functions */

        public function __construct() {
                // Initialize special page
                parent::__construct( 'GeoLite' );
        }

	public function execute( $sub ) {
		global $wgOut, $wgRequest, $wgLandingPageBase, $wgChaptersPageBase, $wgChapterLandingPages;

		$lang = ( preg_match( '/^[A-Za-z-]+$/', $wgRequest->getVal( 'lang' ) ) ) ? $wgRequest->getVal( 'lang' ) : 'en' ;
		$utm_source = $wgRequest->getVal( 'utm_source' );
		$utm_medium = $wgRequest->getVal( 'utm_medium' );
		$utm_campaign = $wgRequest->getVal( 'utm_campaign' );
		$referrer = $wgRequest->getHeader( 'referer' );

		$tracking = '?' . wfArrayToCGI( array( 
			'utm_source' => "$utm_source",
			'utm_medium' => "$utm_medium",
		        'utm_campaign' => "$utm_campaign",
			'referrer' => "$referrer",
		) );
		
		$ip = ( $wgRequest->getVal( 'ip') ) ? $wgRequest->getVal( 'ip' ) : wfGetIP();
		
		if ( IP::isValid( $ip ) ) {
			$country = geoip_country_code_by_name( $ip );
			if ( is_string ( $country ) && array_key_exists( $country, $wgChapterLandingPages ) ) {
			    $wgOut->redirect( $wgChaptersPageBase . "/" . $wgChapterLandingPages[ $country ] . $tracking );
			} else { // Valid IP but no chapter page
				$wgOut->redirect( $wgLandingPageBase . "/" . $lang . $tracking );
			}
		} else { // No ip found so do the best we can 
			$wgOut->redirect( $wgLandingPageBase . "/" . $lang . $tracking );
		}
	}

}

