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

	public function __construct() {
		parent::__construct( 'Update' );
	}

	public function execute( $arg ) {
		
	}
	
}