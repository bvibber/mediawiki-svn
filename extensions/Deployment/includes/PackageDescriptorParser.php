<?php

/**
 * File holding the PackageDescriptorParser class.
 * Partly based on DeployDescriptor from Ontoprises Deployment Framework.
 *
 * @file PackageDescriptor.php
 * @ingroup Deployment
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Base package description parsing class. Deriving classes
 * can parse a package description in a certain format to a
 * PackageDescriptor object.
 * 
 * @author Jeroen De Dauw
 * @author Kai Kühn
 */
abstract class PackageDescriptorParser {
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		// TODO
	}
	
}

/**
 * Parsing class for package desciprtions in XML.
 * @see PackageDescriptorParser
 * 
 * @author Jeroen De Dauw
 * @author Kai Kühn
 * 
 * TODO: move to it's own file
 */
class XMLDescriptorParser extends PackageDescriptorParser {
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		
		// TODO
	}		
	
}

/**
 * Parsing class for package desciprtions in CPAN.
 * @see PackageDescriptorParser
 * 
 * @author Jeroen De Dauw
 * 
 * TODO: move to it's own file
 */
class CPANDescriptorParser extends PackageDescriptorParser {
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		
		// TODO
	}		
	
}