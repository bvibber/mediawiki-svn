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

	public function __construct() {
		parent::__construct( 'Install' );
	}

	public function execute( $arg ) {
		
	}
	
}