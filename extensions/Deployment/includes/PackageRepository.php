<?php

/**
 * File holding the PackageRepository class.
 *
 * @file PackageRepository.php
 * @ingroup Deployment
 *
 * @author Jeroen De Dauw
 */

/**
 * Base repository class. Deriving classes handle interaction with
 * package repositories of the type they support.
 * 
 * @author Jeroen De Dauw
 */
abstract class PackageRepository {
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		// TODO
	}		
	
}

/**
 * Class for interaction with the Ontoprise repository.
 * @see PackageRepository
 * 
 * @author Jeroen De Dauw
 * 
 * TODO: move to it's own file
 */
class OntopriseRepository extends PackageRepository {
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		
		// TODO
	}		
	
}

// TODO: if the ontoprise repository structure is acceptable for general use, rename the class,
// if it's not, design a more general repository structure and create a new PackageRepository class to handle.