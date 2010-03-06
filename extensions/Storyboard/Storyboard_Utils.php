<?php

/**
 * File holding the utility functions for Storyboard.
 *
 * @file Storyboard_Utils.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
 * @author Roan Kattouw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class StoryboardUtils {

	/**
	 * Get the width or height from an arguments array, or use the default value if not specified or not valid
	 * @param $arr Array of arguments
	 * @param $name Key in $array
	 * @param $default Default value to use if $arr[$name] is not set or not valid
	 */
	public static function getDimension( $arr, $name, $default ) {
		$value = $default;
		if ( isset( $arr[$name] ) && preg_match( '/\d+(\.\d+)?%?/', $arr[$name] ) ) {
			$value = $arr[$name];
		}
		if ( !preg_match( '/(px|ex|em|%)$/', $value ) ) {
			$value .= 'px';
		}
		return $value;
	}
	
}