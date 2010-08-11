<?php

/**
 * File holding the PackageRepository class.
 *
 * @file PackageRepository.php
 * @ingroup Deployment
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

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
	 * @param $filterType String
	 * @param $filterValue String
	 * 
	 * @return array
	 */
	public abstract function findExtenions( $filterType, $filterValue );
	
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