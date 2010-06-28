<?php

/**
 * File holding the SpecialExtensions class.
 *
 * @file SpecialExtensions.php
 * @ingroup Deployment
 * @ingroup SpecialPage
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * A special page that allows browing and searching through installed extensions.
 * 
 * @author Jeroen De Dauw
 */
class SpecialExtensions extends SpecialPage {

	public function __construct() {
		parent::__construct( 'Extensions' );
	}

	public function execute( $arg ) {
		
	}
	
}