<?php 
/**
 * Special Page for GeoLite
 *
 * @file
 * @ingroup Extensions
 */

// Special page GeoLite

class SpecialGeoLite extends SpecialPage {

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

		$tracking = '?' . wfArrayToCGI( array( 
			'utm_source' => "$utm_source",
			'utm_medium' => "$utm_medium",
		        'utm_campaign' => "$utm_campaign",
		) );
		
		$ip = ( $wgRequest->getVal( 'ip') ) ? $wgRequest->getVal( 'ip' ) : wfGetIP();
		
		if ( IP::isValid( $ip ) ) {
		   $country = geoip_country_code_by_name( $ip );
                   if ( is_string ( $country ) && array_key_exists( $country, $wgKnownLandingPages ) ) {
		          $wgOut->redirect( $wgChaptersPageBase . "/" . $wgChapterLandingPages[ $country ] . $tracking );
	  	   }
		} else {  
			// Either we couldn't get the ip from the client or the geo ip lookup failed. Redirect as best as we can 
			$wgOut->redirect( $wgLandingPageBase . "/" . $lang . $tracking );
		}
	}

}

