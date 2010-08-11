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
 * @since 0.1
 * 
 * @ingroup Deployment
 * 
 * @author Jeroen De Dauw
 */
abstract class PackageRepository {
	
	/**
	 * Base location of the repository.
	 * 
	 * @since 0.1
	 * 
	 * @var string
	 */
	protected $location;	
	
	/**
	 * Returns a list of extensions matching the search criteria.
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	public abstract function findExtenions();
	
	/**
	 * Constructor.
	 * 
	 * @param $location String
	 * 
	 * @since 0.1
	 */
	public function __construct( $location ) {
		$this->location = $location;
	}		
	
}

/**
 * Repository class for interaction with repositories provided by
 * the Distirbution extension and the MediaWiki API.
 * 
 * @since 0.1
 * 
 * @ingroup Deployment
 * 
 * @author Jeroen De Dauw
 */
class DistributionRepository extends PackageRepository {
	
	/**
	 * Constructor.
	 * 
	 * @param $location String: path to the api of the MediaWiki install providing the repository.
	 * 
	 * @since 0.1
	 */
	public function __construct( $location ) {
		parent::__construct( $location );
	}
	
	/**
	 * @see PackageRepository::findExtenions
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */	
	public function findExtenions() {
		global $wgRepositoryPackageStates;
		
		// TODO
	}
	
}