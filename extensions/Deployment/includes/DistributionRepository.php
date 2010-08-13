<?php

/**
 * File holding the DistributionRepository class.
 *
 * @file DistributionRepository.php
 * @ingroup Deployment
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
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
	 * @param $filterType String
	 * @param $filterValue String
	 * 
	 * @return array
	 */	
	public function findExtenions( $filterType, $filterValue ) {
		global $wgRepositoryPackageStates;
		
		// TODO: use $wgRepositoryPackageStates
		
		$filterType = urlencode( $filterType );
		$filterValue = urlencode( $filterValue );
		
		$response = Http::get(
			"$this->location?format=json&action=query&list=extensions&dstfilter=$filterType&dstvalue=$filterValue",
			'default',
			array( 'sslVerifyHost' => true, 'sslVerifyCert' => true )
		);
		
		$extensions = array();
		
		if ( $response !== false ) {
			$extensions = FormatJson::decode( $response )->query->extensions;
		}

		return $extensions;
	}
	
	/**
	 * @see PackageRepository::extensionHasUpdate
	 * 
	 * @since 0.1
	 */		
	public function extensionHasUpdate( $extensionName, $currentVersion ) {
		global $wgRepositoryPackageStates;
		
		// TODO: use $wgRepositoryPackageStates
		
		$extensionName = urlencode( $extensionName );
		$currentVersion = urlencode( $currentVersion );		
		
		$response = Http::get(
			"$this->location?format=json&action=updates&extensions=$extensionName;$currentVersion",
			'default',
			array( 'sslVerifyHost' => true, 'sslVerifyCert' => true )
		);
		
		if ( $response === false ) {
			return false;
		}
		
		$response = FormatJson::decode( $response );
		
		if ( property_exists( $response, 'extensions' ) && property_exists( $response->extensions, $extensionName ) ) {
			return $response->extensions->$extensionName;
		}
		
		return false;
	}
	
	/**
	 * @see PackageRepository::coreHasUpdate
	 * 
	 * @since 0.1
	 */			
	public function coreHasUpdate( $currentVersion ) {
		global $wgRepositoryPackageStates;
		
		// TODO: use $wgRepositoryPackageStates
		
		$currentVersion = urlencode( $currentVersion );		
		
		$response = Http::get(
			"$this->location?format=json&action=updates&mediawiki=$currentVersion",
			'default',
			array( 'sslVerifyHost' => true, 'sslVerifyCert' => true )
		);
		
		if ( $response === false ) {
			return false;
		}
		
		$response = FormatJson::decode( $response );
		
		if ( property_exists( $response, 'mediawiki' ) ) {
			return $response->mediawiki;
		}
		
		return false;
	}
	
	/**
	 * @see PackageRepository::installationHasUpdates
	 * 
	 * @since 0.1
	 */			
	public function installationHasUpdates( $coreVersion, array $extensions ) {
		global $wgRepositoryPackageStates;
		
		// TODO: use $wgRepositoryPackageStates
		
		$coreVersion = urlencode( $coreVersion );
		
		$extensionParams = array();
		
		foreach ( $extensions as $extensionName => $extensionVersion ) {
			$extensionParams[] = urlencode( $extensionName ) . ';' . urlencode( $extensionVersion );
		}
		
		$extensionParams = implode( '|', $extensionParams );
		
		$response = Http::get(
			"$this->location?format=json&action=updates&mediawiki=$coreVersion&extensions=",
			'default',
			array( 'sslVerifyHost' => true, 'sslVerifyCert' => true )
		);
		
		if ( $response === false ) {
			return false;
		}
		
		$response = FormatJson::decode( $response );
		
		$updates = array();
		
		if ( property_exists( $response, 'mediawiki' ) ) {
			$updates['MediaWiki'] = $response->mediawiki;
		}		
		
		if ( property_exists( $response, 'extensions' ) ) {
			foreach ( $extensions as $extensionName => $extensionVersion ) {
				if ( property_exists( $response->extensions, $extensionName ) ) {
					$updates[$extensionName] = $response->extensions->$extensionName;
				}				
			}
		}
		
		return count( $updates ) > 0 ? $updates : false;
	}
	
}