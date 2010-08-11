<?php

/**
 * File holding the DistributionRepository class.
 *
 * @file DistributionRepository.php
 * @ingroup Deployment
 *
 * @author Jeroen De Dauw
 */

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
	 * @param $filterType String
	 * @param $filterValue String
	 * 
	 * @return array
	 */	
	public function findExtenions( $filterType, $filterValue ) {
		global $wgRepositoryPackageStates;
		
		// TODO: use $wgRepositoryPackageStates
		
		$response = Http::get(
			"$this->location?format=json&action=query&list=extensions&dstfilter=$filterType&dstvalue=$filterValue",
			'default',
			array( 'sslVerifyHost' => true, 'sslVerifyCert' => true )
		);
		
		// TODO: parse and return result 
	}
	
}