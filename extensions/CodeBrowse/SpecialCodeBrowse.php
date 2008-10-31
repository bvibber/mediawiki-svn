<?php

class SpecialCodeBrowse extends SpecialPage {
	function __construct() {
		parent::__construct( 'CodeBrowse', 'codebrowse' );
	}
	function execute( $par = '' ) {
		if ( !$par )
			$par = '/';
		$this->setHeaders();
			
		global $wgRequest;
		$view = CodeBrowseView::newFromPath( $par, $wgRequest );
		
		global $wgOut;
		$wgOut->addHTML( 
			$view->getHeader().
			$view->getContent().
			$view->getFooter()
		);
	}
}