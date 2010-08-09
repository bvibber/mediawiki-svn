<?php

/**
 * File holding the SpecialInstall class.
 *
 * @file SpecialInstall.php
 * @ingroup Deployment
 * @ingroup SpecialPage
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * A special page that allows browing and searching through extensions that are in the connected extension repository.
 * 
 * @author Jeroen De Dauw
 */
class SpecialInstall extends SpecialPage {

	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 */	
	public function __construct() {
		parent::__construct( 'Install', 'siteadmin' );
	}

	/**
	 * Main method.
	 * 
	 * @since 0.1 
	 * 
	 * @param $arg String
	 */	
	public function execute( $arg ) {
		global $wgOut, $wgUser;
		
		$wgOut->setPageTitle( wfMsg( 'install-title' ) );
		
		// If the user is authorized, display the page, if not, show an error.
		if ( $this->userCanExecute( $wgUser ) ) {
			
		} else {
			$this->displayRestrictionError();
		}			
	}
	
}