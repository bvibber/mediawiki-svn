<?php

class SpecialCheckUser extends SpecialPage {
	
	function __construct() {
		global $wgUser;
		
		if ( $wgUser->isAllowed( 'checkuser' ) || !$wgUser->isAllowed( 'checkuser-log' ) ) {
			parent::__construct( 'CheckUser', 'checkuser' );
		} else {
			parent::__construct( 'CheckUser', 'checkuser-log' );
		}
	} 
	
	function excecute( $subpage ) {
	
		wfLoadExtensionMessages( 'CheckUser' ); 
		
	}
	
	function preCacheMessages() {
	}
	
	function getLogSubpageTitle() {
	}
	
	function doForm() {
	}
	
	function addStyles() {
	}
	
	function getPeriodMenu() {
	}
	
	function addJSCIDRForm() {
	}
	
	function doMassUserBlock() {
	}
	
	function noMatchesMessage() {
	}
	
	function checkReason() {
	}
	
	function doUser2IP() {
	}
	
	function doUser2Edits() {
	}
	
	function doIP2User() {
	}
	
	function doIP2Edits() {
	}
	
	
	
	
}

class CUTablePager extends TablePager { 
}