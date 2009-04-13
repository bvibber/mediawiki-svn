<?php

class SpecialAccountManager extends SpecialPage {
	function __construct() {
		parent::__construct( 'AccountManager', 'accountmanager', false );
		$this->mErrors = array();
	}
	
	function processData() {
		global $wgRequest;
		
		
	}


}
