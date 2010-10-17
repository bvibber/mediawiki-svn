<?php
class wikibhasha extends SpecialPage {
	function __construct() {
		parent::__construct( 'Wikibhasha' );
		wfLoadExtensionMessages('Wikibhasha');
	}
 
	function execute( $par ) {
		global $wgRequest, $wgOut;
 
		$this->setHeaders();
 
		# Get request data from, e.g.
		$param = $wgRequest->getText('param');
 
		# Do stuff
		# ...
		$output="WikiBhasha";
		$wgOut->addWikiText( $output );
	}
}
?>