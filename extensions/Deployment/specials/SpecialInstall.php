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
		global $wgOut, $wgUser, $wgRequest;
		
		$wgOut->setPageTitle( wfMsg( 'install-title' ) );
		
		// If the user is authorized, display the page, if not, show an error.
		if ( $this->userCanExecute( $wgUser ) ) {
			if ( $wgRequest->wasPosted() ) {
				$this->showCompactSearchOptions();
				// TODO
				$this->findExtenions();
			}
			else {
				$this->showFullSearchOptions();
			}
		} else {
			$this->displayRestrictionError();
		}			
	}
	
	protected function showFullSearchOptions() {
		// TODO
	}
	
	protected function showCompactSearchOptions() {
		// TODO
	}
	
	protected function findExtenions( $filterType, $filterValue ) {
		$repository = wfGetRepository();
		
		$repository->findExtenions( $filterType, $filterValue );
	}
	
	/**
	 * Show the extensions that where found in a list.
	 * 
	 * @param $extensions Array
	 */
	protected function showExtensionList( array $extensions ) {
		global $wgOut;
		
		// TODO: this is just a debug mockup
		
		$list = array();
		
		foreach ( $extensions as $extension ) {
			$list[] = $extension['name'];
		}
		
		$wgOut->addHTML( implode( ',', $list ) );
	}
	
}