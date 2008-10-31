<?php

class SpecialCodeBrowse extends SpecialPage {
	function __construct() {
		parent::__construct( 'CodeBrowse', 'codebrowse' );
	}
	function execute( $par = '' ) {
		$this->setHeaders();		
		
		global $wgRequest;
		$path = $wgRequest->getText( 'path', $par );

		$view = CodeBrowseView::newFromPath( $path, $wgRequest );
		
		global $wgOut;
		$wgOut->addHTML( 
			$view->getHeader().
			$view->getContent().
			$view->getFooter()
		);
	}
}