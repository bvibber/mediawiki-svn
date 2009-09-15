<?php
/**
 * Special:ClickTracking
 *
 * @file
 * @ingroup Extensions
 */

class SpecialClickTracking extends SpecialPage {
	function __construct() {
		parent::__construct( 'ClickTracking' );
		wfLoadExtensionMessages( 'ClickTracking' );
	}

	function execute( $par ) {
		global $wgOut;
		$this->setHeaders();
		$wgOut->setPageTitle( wfMsg( 'clicktracking-title' ) );
		
	}

	

}