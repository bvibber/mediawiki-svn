<?php

class Poll extends SpecialPage {
	function __construct() {
		parent::__construct( 'Poll' );
		wfLoadExtensionMessages( 'Poll' );
	}

	function execute( $par ) {
		global $wgRequest, $wgOut;

		$this->setHeaders();

		# Get request data from, e.g.
		$action = htmlentities( $wgRequest->getText( 'action' ) );
		$id = htmlentities( $wgRequest->getText( 'id' ) );

	}
}
