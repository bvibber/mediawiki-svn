<?php

/**
 * File holding the DistributionRelease class.
 * 
 * @file DistributionRelease.php
 * @ingroup Distribution
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Static class for handling release data.
 * 
 * @since 0.1
 * 
 * @ingroup Distribution
 * 
 * @author Jeroen De Dauw
 */
class DistributionRelease {
	
	/**
	 * Mapping between the state names and internal representation.
	 * 
	 * @since 0.1
	 * 
	 * @var array
	 */
	private static $states = array(
		'dev' => 0,
		'alpha' => 1,
		'beta' => 2,
		'rc' => 3,
		'stable' => 4,
		'deprecated' => 5
	); 
	
	/**
	 * Returns a list of supported states. 
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	public static function getStates() {
		return array_keys( self::$states );
	}
	
	/**
	 * Returns the name of the default state.
	 */
	public static function getDefaultState() {
		return 'stable';
	}
	
	/**
	 * Returns the internal state representation of the provided state name.
	 * 
	 * @since 0.1
	 * 
	 * @param $stateName Integer
	 */
	public static function mapState( $stateName ) {
		if ( !array_key_exists( $stateName, self::$states ) ) {
			$oldName = $stateName;
			$stateName = self::getDefaultState();
			wfWarn( "State '$oldName' was not recognized, defaulted to '$stateName'" );
		}

		return self::$states[$stateName];
	}
	
}
