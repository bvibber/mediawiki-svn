<?php

/**
 * File holding the SpecialUpdate class.
 *
 * @file SpecialUpdate.php
 * @ingroup Deployment
 * @ingroup SpecialPage
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * A special page that allows checking for updates for both MediaWiki itself and extensions. 
 * 
 * @author Jeroen De Dauw
 */
class SpecialUpdate extends SpecialPage {

	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 */	
	public function __construct() {
		parent::__construct( 'Update', 'siteadmin' );
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
		
		// If the user is authorized, display the page, if not, show an error.
		if ( $this->userCanExecute( $wgUser ) ) {
			
		} else {
			$this->displayRestrictionError();
		}			
	}
	
}