<?php

class SpecialAccountManager extends SpecialPage {
	function __construct() {
		parent::__construct( 'AccountManager', 'accountmanager', false );
	}
	
	function processData() {
		global $wgRequest;
		
		
	}
	
	function execute() {
		global $wgRequest;
		
		$action = $wgRequest->getVal( 'action' );
		$username = $wgRequest->getVal( 'user' );
		
		$list = new AmUserListView();
		$list->execute();
		
		$userView = new AmUserView( $username );
		$userView->execute();
		
	}


}
