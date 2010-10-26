<?php
/*
*
*	The body file will contain a subclass of SpecialPage. It will be loaded automatically when the special page is requested
*	this file contains the functions to populate the content to the special page
*
*/
class wikibhasha extends SpecialPage {
	function __construct() {
		parent::__construct( 'Wikibhasha' );
	}

	function execute( $par ) {
		global $wgRequest, $wgOut;

		$this->setHeaders();

		# Get request data from, e.g.
		$param = $wgRequest->getText( 'param' );

		# Do stuff
		# ...
		$output = "WikiBhasha";
		$wgOut->addWikiText( $output );
	}
}
