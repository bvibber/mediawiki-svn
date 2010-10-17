<?php
/**
 * Special:WikiBhasha
 *
 * @file
 * @ingroup SpecialPage
 */

/**
 *
 */
class wikiBhasha extends SpecialPage {

	/**
	 * Constructor
	 */
	function __construct() {
		parent::SpecialPage( 'WikiBhasha', '', true );
	}

	/**
	 * Main execution function
	 * @param $par Parameters passed to the page
	 */
	function execute( $par ) {
		global $wgRequest, $wgOut;
		$this->setHeaders();
		$wgOut->addHTML( '<h2>message goes here</h2>' );
	}
}
