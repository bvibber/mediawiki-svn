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
		if ( !preg_match( '/^(?:-?\d+)\/(?:\d+[1-9]|[1-9]\d*)$/D', $val ) ) {
			wfDebugLog( 'XMP', __METHOD__ . " Expected rational but got $val" );
			$val = null;
		}

	}
	/**
	* function to validate integers
	*
	* @param $info Array information about current property
	* @param &$val Mixed current value to validate
	* @param $standalone Boolean if this is a simple property or array
	*/
	public static function validateInteger( $info, &$val, $standalone ) {
		if ( !$standalone ) {
			// this only validates standalone properties, not arrays, etc
			return;
		}
		if ( !preg_match( '/^[-+]?\d+$/D', $val ) ) {
			wfDebugLog( 'XMP', __METHOD__ . " Expected integer but got $val" );
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
	/**
	* function to validate LangCode properties ( en-GB, etc )
	*
	* This is just a naive check to make sure it somewhat looks like a lang code.
	*
	* @see rfc 3066
	* @see http://www.adobe.com/devnet/xmp/pdfs/XMPSpecificationPart1.pdf page 30 (section 8.2.2.5)
	*
	* @param $info Array information about current property
	* @param &$val Mixed current value to validate
	* @param $standalone Boolean if this is a simple property or array
	*/
	public static function validateLangCode( $info, &$val, $standalone ) {
		if ( !$standalone ) {
			// this only validates standalone properties, not arrays, etc
			return;
		}
		if ( !preg_match( '/^[-A-Za-z0-9]{2,}$/D', $val) ) {
			//this is a rather naive check.
			wfDebugLog( 'XMP', __METHOD__ . " Expected Lang code but got $val" );
			$val = null;
		}

	}
	/**
	* function to validate date properties, and convert to Exif format.
	*
	* @param $info Array information about current property
	* @param &$val Mixed current value to validate. Converts to TS_EXIF as a side-effect.
	* @param $standalone Boolean if this is a simple property or array
	*/
	public static function validateDate( $info, &$val, $standalone ) {
		if ( !$standalone ) {
			// this only validates standalone properties, not arrays, etc
			return;
		}
		$res = array();
		if ( !preg_match(
			/* ahh! scary regex... */
			'/^([0-3]\d{3})(?:-([01]\d)(?:-([0-3]\d)(?:T([0-2]\d):([0-6]\d)(?::([0-6]\d)(?:\.\d+)?)?([-+]\d{2}:\d{2}|Z)?)?)?)?$/D'
			, $val, $res)
		) {
			wfDebugLog( 'XMP', __METHOD__ . " Expected date but got $val" );
			$val = null;
		} else {
			/*
			 * $res is formated as follows:
			 * 0 -> full date.
			 * 1 -> year, 2-> month, 3-> day, 4-> hour, 5-> minute, 6->second
			 * 7-> Timezone specifier (Z or something like +12:30 )
			 * many parts are optional, some aren't. For example if you specify
			 * minute, you must specify hour, day, month, and year but not second or TZ.
			 */
			//if month, etc unspecified, full out as 01.
			$res[2] = isset( $res[2] ) ? $res[2] : '01'; //month
			$res[3] = isset( $res[3] ) ? $res[3] : '01'; //day
			if ( !isset( $res[4] ) ) { //hour
				//just have the year month day
				$val = $res[1] . ':' . $res[2] . ':' . $res[3];
				return;
			}
			//if hour is set, so is minute or regex above will fail.
			//Extra check for empty string neccesary due to TZ but no second case.
			$res[6] = isset( $res[6] ) && $res[6] != '' ? $res[6] : '00';

			if ( !isset( $res[7] ) || $res[7] === 'Z' || $res[1] >= '2038' || $res[1] < '1902' ) {
				// timezone processing has y2k38 problem so pretend not set if
				// before 1902 or after 2037.
				$val = $res[1] . ':' . $res[2] . ':' . $res[3]
					. ' ' . $res[4] . ':' . $res[5] . ':' . $res[6];
				return;
			}

			//do timezone processing. We've already done the case that tz = Z.

			$unix = wfTimestamp( TS_UNIX, $res[1] . $res[2] . $res[3] . $res[4] . $res[5] . $res[6] );
			$offset = intval( substr( $res[7], 1, 2 ) ) * 60 * 60;
			$offset += intval( substr( $res[7], 4, 2 ) ) * 60;
			if ( substr( $res[7], 0, 1 ) === '-' ) {
				$offset = -$offset;
			}
			$val = wfTimestamp( TS_EXIF, $unix + $offset );
		}

	}

}
