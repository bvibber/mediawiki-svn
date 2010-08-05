<?php
/**
* This contains some static methods for
* validating XMP properties. See XMPInfo and XMPReader classes.
*
* Each of these functions take the stame parameters
* * an info array which is a subset of the XMPInfo::items array
* * A value (passed as reference) to validate. This can be either a
*	simple value or an array
* * A boolean to determine if this is validating a simple or complex values
*
* It should be noted that when an array is being validated, typically the validation
* function is called once for each value, and then once at the end for the entire array.
*
* These validation functions can also be used to modify the data. See the gps and flash one's
* for example.
*
* @see http://www.adobe.com/devnet/xmp/pdfs/XMPSpecificationPart1.pdf starting at pg 28
* @see http://www.adobe.com/devnet/xmp/pdfs/XMPSpecificationPart2.pdf starting at pg 11
*/
class XMPValidate {
	/**
	* function to validate boolean properties ( True or False )
	*
	* @param $info Array information about current property
	* @param &$val Mixed current value to validate
	* @param $standalone Boolean if this is a simple property or array
	*/
	public static function validateBoolean( $info, &$val, $standalone ) {
		if ( !$standalone ) {
			// this only validates standalone properties, not arrays, etc
			return;
		}
		if ( $val !== 'True' && $val !== 'False' ) {
			wfDebugLog( 'XMP', __METHOD__ . " Expected True or False but got $val" );
			$val = null;
		}

	}
	/**
	* function to validate rational properties ( 12/10 )
	*
	* @param $info Array information about current property
	* @param &$val Mixed current value to validate
	* @param $standalone Boolean if this is a simple property or array
	*/
	public static function validateRational( $info, &$val, $standalone ) {
		if ( !$standalone ) {
			// this only validates standalone properties, not arrays, etc
			return;
		}
		if ( !preg_match( '/^(-?\d+)\/(\d+[1-9]|[1-9]\d*)$/', $val ) ) {
			wfDebugLog( 'XMP', __METHOD__ . " Expected rational but got $val" );
			$val = null;
		}

	}
	/**
	* function to validate properties with a fixed number of allowed
	* choices. (closed choice)
	*
	* @param $info Array information about current property
	* @param &$val Mixed current value to validate
	* @param $standalone Boolean if this is a simple property or array
	*/
	public static function validateClosed( $info, &$val, $standalone ) {
		if ( !$standalone ) {
			// this only validates standalone properties, not arrays, etc
			return;
		}
		if ( !isset( $info['choices'][$val] ) ) {
			wfDebugLog( 'XMP', __METHOD__ . " Expected closed choice, but got $val" );
			$val = null;
		}
	}
	/**
	* function to validate and modify flash structure
	*
	* @param $info Array information about current property
	* @param &$val Mixed current value to validate
	* @param $standalone Boolean if this is a simple property or array
	*/
	public static function validateFlash( $info, &$val, $standalone ) {
		if ( $standalone ) {
			// this only validates flash structs, not individual properties
			return;
		}
		if ( !( isset( $val['Fired'] )
			&& isset( $val['Function'] )
			&& isset( $val['Mode'] )
			&& isset( $val['RedEyeMode'] )
			&& isset( $val['Return'] )
		) ) {
			wfDebugLog( 'XMP', __METHOD__ . " Flash structure did not have all the required compoenents" );
			$val = null;
		} else {
			$val = ( "\0" | ( $val['Fired'] === 'True' )
				| ( intval( $val['Return'] ) << 1 )
				| ( intval( $val['Mode'] ) << 3 )
				| ( ( $val['Function'] === 'True' ) << 5 )
				| ( ( $val['RedEyeMode'] === 'True' ) << 6 ) );
		}
	}

}
