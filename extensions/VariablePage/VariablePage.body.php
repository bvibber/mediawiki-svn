<?php

class SpecialVariablePage extends UnlistedSpecialPage {

	public function __construct() {
		parent::__construct( 'VariablePage' );
	}

	public function execute() {
		global $wgOut, $wgRequest;
		global $wgVariablePagePossibilities, $wgVariablePageUtmMedium;
		
		$lang = ( preg_match( '/^[A-Za-z-]+$/', $wgRequest->getVal( 'lang' ) ) ) ? $wgRequest->getVal( 'lang' ) : 'en' ;
		$utm_source = $wgRequest->getVal( 'utm_source' );
		$utm_medium = ( strlen($wgVariablePageUtmMedium )) ? $wgVariablePageUtmMedium : $wgRequest->getVal( 'utm_medium' );
		$utm_campaign = $wgRequest->getVal( 'utm_campaign' );
		$referrer = $wgRequest->getHeader( 'referrer' );

		$tracking = '?' . wfArrayToCGI( array(
			'utm_source' => $utm_source,
			'utm_medium' => $utm_medium,
			'utm_campaign' => $utm_campaign,
			'referrer' => $referrer
		));

		$url = $this->determinePage( $wgVariablePagePossibilities );
		$wgOut->redirect( $url . '/' . $lang . $tracking );
	}

	/**
	 * Determine the URL to use based on its configured probability
	 *
	 * This is a basic weighted random selection algorithm borrowed from:
	 * http://20bits.com/articles/random-weighted-elements-in-php/
	 *
	 * @param array $page_possibilities
	 * @return string $url
	 */
	public function determinePage( $page_possibilities ) {
		/**
		 * Determine a random number to measure probability again
		 *
		 * We use a # larger than 100 to increase 'randomness'
		 */
		$random_number = mt_rand( 0, 100*100 );
		$offset = 0;  

		foreach ( $page_possibilities as $url => $probability ) {
			$offset += $probability * 100;
			if ( $random_number <= $offset ) {
				return $url;
			}
		}
	}
}
