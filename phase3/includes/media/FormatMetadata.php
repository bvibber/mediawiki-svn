<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @ingroup Media
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason, 2009 Brent Garber
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @see http://exif.org/Exif2-2.PDF The Exif 2.2 specification
 * @file
 */


/**
 * Format Image metadata values into a human readable form.
 *
 * Note lots of these messages use the prefix 'exif' even though
 * they may not be exif properties. For example 'exif-ImageDescription'
 * can be the Exif ImageDescription, or it could be the iptc-iim caption
 * property, or it could be the xmp dc:description property. This
 * is because these messages should be independent of how the data is
 * stored, sine the user doesn't care if the description is stored in xmp,
 * exif, etc only that its a description. (Additionally many of these properties
 * are merged together following the MWG standard, such that for example,
 * exif properties override XMP properties that mean the same thing if
 * there is a conflict).
 *
 * It should perhaps use a prefix like 'metadata' instead, but there
 * is already a large number of messages using the 'exif' prefix.
 *
 * @ingroup Media
 */
class FormatMetadata {

	/**
	 * Numbers given by Exif user agents are often magical, that is they
	 * should be replaced by a detailed explanation depending on their
	 * value which most of the time are plain integers. This function
	 * formats Exif (and other metadata) values into human readable form.
	 *
	 * @param $tags Array: the Exif data to format ( as returned by
	 *                    Exif::getFilteredData() or BitmapMetadataHandler )
	 * @return array
	 */
	public static function getFormattedData( $tags ) {
		global $wgLang;

		$resolutionunit = !isset( $tags['ResolutionUnit'] ) || $tags['ResolutionUnit'] == 2 ? 2 : 3;
		unset( $tags['ResolutionUnit'] );

		foreach ( $tags as $tag => &$vals ) {

			// This seems ugly to wrap non-array's in an array just to unwrap again,
			// especially when most of the time it is not an array
			if ( !is_array( $tags[$tag] ) ) {
				$vals = Array( $vals );
			}

			// _type is a special value to say what array type
			if ( isset( $tags[$tag]['_type'] ) ) {
				$type = $tags[$tag]['_type'];
				unset( $vals['_type'] );
			} else {
				$type = 'ul'; // default unorcdered list.
			}

			//This is done differently as the tag is an array.
			if ($tag == 'GPSTimeStamp' && count($vals) === 3) {
				//hour min sec array

				$h = explode('/', $vals[0]);
				$m = explode('/', $vals[1]);
				$s = explode('/', $vals[2]);

				// this should already be validated
				// when loaded from file, but it could
				// come from a foreign repo, so be
				// paranoid.
				if ( !isset($h[1])
					|| !isset($m[1])
					|| !isset($s[1])
					|| $h[1] == 0
					|| $m[1] == 0
					|| $s[1] == 0
				) {
					continue;
				}
				$tags[$tag] = intval( $h[0] / $h[1] )
					. ':' . intval( $m[0] / $m[1] )
					. ':' . str_pad( intval( $s[0] / $s[1] ), 2, '0', STR_PAD_LEFT );
				continue;
			}

		
			foreach ( $vals as &$val ) {

				switch( $tag ) {
				case 'Compression':
					switch( $val ) {
					case 1: case 6:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'PhotometricInterpretation':
					switch( $val ) {
					case 2: case 6:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'Orientation':
					switch( $val ) {
					case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'PlanarConfiguration':
					switch( $val ) {
					case 1: case 2:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				// TODO: YCbCrSubSampling
				case 'YCbCrPositioning':
					switch ( $val ) {
					case 1:
					case 2:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'XResolution':
				case 'YResolution':
					switch( $resolutionunit ) {
						case 2:
							$val = self::msg( 'XYResolution', 'i', self::formatNum( $val ) );
							break;
						case 3:
							$val = self::msg( 'XYResolution', 'c', self::formatNum( $val ) );
							break;
						default:
							$val = $val;
							break;
					}
					break;

				// TODO: YCbCrCoefficients  #p27 (see annex E)
				case 'ExifVersion': case 'FlashpixVersion':
					$val = "$val" / 100;
					break;

				case 'ColorSpace':
					switch( $val ) {
					case 1: case 'FFFF.H':
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'ComponentsConfiguration':
					switch( $val ) {
					case 0: case 1: case 2: case 3: case 4: case 5: case 6:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'DateTime':
				case 'DateTimeOriginal':
				case 'DateTimeDigitized':
				case 'DateTimeReleased':
				case 'DateTimeExpires':
				case 'GPSDateStamp':
				case 'dc-date':
				case 'DateTimeMetadata':
					if ( $val == '0000:00:00 00:00:00' || $val == '    :  :     :  :  ' ) {
						$val = wfMsg( 'exif-unknowndate' );
					} elseif ( preg_match( '/^(?:\d{4}):(?:\d\d):(?:\d\d) (?:\d\d):(?:\d\d):(?:\d\d)$/', $val ) ) {
						$val = $wgLang->timeanddate( wfTimestamp( TS_MW, $val ) );
					} elseif ( preg_match( '/^(?:\d{4}):(?:\d\d):(?:\d\d)$/', $val ) ) {
						// avoid using wfTimestamp here for the pre-1902 photos
						// due to reverse y2k38 bug. $wgLang->timeanddate() is also
						// broken on dates from before 1902 so don't worry about it
						// in the above case (not to mention that most photos from the
						// 1800's don't have a time recorded anyways).
						$val = $wgLang->date( substr( $val, 0, 4 )
							. substr( $val, 5, 2 )
							. substr( $val, 8, 2 )
							. '000000' );
					}
					// else it will just output $val without formatting it.
					break;

				case 'ExposureProgram':
					switch( $val ) {
					case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'SubjectDistance':
					$val = self::msg( $tag, '', self::formatNum( $val ) );
					break;

				case 'MeteringMode':
					switch( $val ) {
					case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 255:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'LightSource':
					switch( $val ) {
					case 0: case 1: case 2: case 3: case 4: case 9: case 10: case 11:
					case 12: case 13: case 14: case 15: case 17: case 18: case 19: case 20:
					case 21: case 22: case 23: case 24: case 255:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'Flash':
					$flashDecode = array(
						'fired'    => $val & bindec( '00000001' ),
						'return'   => ( $val & bindec( '00000110' ) ) >> 1,
						'mode'     => ( $val & bindec( '00011000' ) ) >> 3,
						'function' => ( $val & bindec( '00100000' ) ) >> 5,
						'redeye'   => ( $val & bindec( '01000000' ) ) >> 6,
//						'reserved' => ($val & bindec( '10000000' )) >> 7,
					);
	
					# We do not need to handle unknown values since all are used.
					foreach ( $flashDecode as $subTag => $subValue ) {
						# We do not need any message for zeroed values.
						if ( $subTag != 'fired' && $subValue == 0 ) {
							continue;
						}
						$fullTag = $tag . '-' . $subTag ;
						$flashMsgs[] = self::msg( $fullTag, $subValue );
					}
					$val = $wgLang->commaList( $flashMsgs );
					break;

				case 'FocalPlaneResolutionUnit':
					switch( $val ) {
					case 2:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'SensingMethod':
					switch( $val ) {
					case 1: case 2: case 3: case 4: case 5: case 7: case 8:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'FileSource':
					switch( $val ) {
					case 3:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'SceneType':
					switch( $val ) {
					case 1:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'CustomRendered':
					switch( $val ) {
					case 0: case 1:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'ExposureMode':
					switch( $val ) {
					case 0: case 1: case 2:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'WhiteBalance':
					switch( $val ) {
					case 0: case 1:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'SceneCaptureType':
					switch( $val ) {
					case 0: case 1: case 2: case 3:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'GainControl':
					switch( $val ) {
					case 0: case 1: case 2: case 3: case 4:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'Contrast':
					switch( $val ) {
					case 0: case 1: case 2:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'Saturation':
					switch( $val ) {
					case 0: case 1: case 2:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'Sharpness':
					switch( $val ) {
					case 0: case 1: case 2:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'SubjectDistanceRange':
					switch( $val ) {
					case 0: case 1: case 2: case 3:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				//The GPS...Ref values are kept for compatability, probably won't be reached.
				case 'GPSLatitudeRef':
				case 'GPSDestLatitudeRef':
					switch( $val ) {
					case 'N': case 'S':
						$val = self::msg( 'GPSLatitude', $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'GPSLongitudeRef':
				case 'GPSDestLongitudeRef':
					switch( $val ) {
					case 'E': case 'W':
						$val = self::msg( 'GPSLongitude', $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'GPSAltitude':
					if ( $val < 0 ) {
						$val = self::msg( 'GPSAltitude', 'below-sealevel', self::formatNum( -$val ) );
					} else {
						$val = self::msg( 'GPSAltitude', 'above-sealevel', self::formatNum( $val ) );
					}
					break;

				case 'GPSStatus':
					switch( $val ) {
					case 'A': case 'V':
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'GPSMeasureMode':
					switch( $val ) {
					case 2: case 3:
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;


				case 'GPSTrackRef':
				case 'GPSImgDirectionRef':
				case 'GPSDestBearingRef':
					switch( $val ) {
					case 'T': case 'M':
						$val = self::msg( 'GPSDirection', $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'GPSLatitude':
				case 'GPSDestLatitude':
					$val = self::formatCoords( $val, 'latitude' );
					break;
				case 'GPSLongitude':
				case 'GPSDestLongitude':
					$val = self::formatCoords( $val, 'longitude' );
					break;

				case 'GPSSpeedRef':
					switch( $val ) {
					case 'K': case 'M': case 'N':
						$val = self::msg( 'GPSSpeed', $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'GPSDestDistanceRef':
					switch( $val ) {
					case 'K': case 'M': case 'N':
						$val = self::msg( 'GPSDestDistance', $val );
						break;
					default:
						$val = $val;
						break;
					}
					break;

				case 'GPSDOP':
					// See http://en.wikipedia.org/wiki/Dilution_of_precision_(GPS)
					if ( $val <= 2 ) {
						$val = self::msg( $tag, 'excellent', self::formatNum( $val ) );
					} elseif ( $val <= 5 ) {
						$val = self::msg( $tag, 'good', self::formatNum( $val ) );
					} elseif ( $val <= 10 ) {
						$val = self::msg( $tag, 'moderate', self::formatNum( $val ) );
					} elseif ( $val <= 20 ) {
						$val = self::msg( $tag, 'fair', self::formatNum( $val ) );
					} else {
						$val = self::msg( $tag, 'poor', self::formatNum( $val ) );
					}
					break;

	

				// This is not in the Exif standard, just a special
				// case for our purposes which enables wikis to wikify
				// the make, model and software name to link to their articles.
				case 'Make':
				case 'Model':
					$val = self::msg( $tag, '', $val );
					break;

				case 'Software':
					if ( is_array( $val ) ) {
						//if its a software, version array.
						$val = wfMsg( 'exif-software-version-value', $val[0], $val[1] );
					} else {
						$val = self::msg( $tag, '', $val );
					}
					break;

				case 'ExposureTime':
					// Show the pretty fraction as well as decimal version
					$val = wfMsg( 'exif-exposuretime-format',
						self::formatFraction( $val ), self::formatNum( $val ) );
					break;
				case 'ISOSpeedRatings':
					// If its = 65535 that means its at the
					// limit of the size of Exif::short and
					// is really higher.
					if ( $val == '65535' ) {
						$val = self::msg( $tag, 'overflow' );
					} else {
						$val = self::formatNum( $val );
					}
					break;
				case 'FNumber':
					$val = wfMsg( 'exif-fnumber-format',
						self::formatNum( $val ) );
					break;

				case 'FocalLength':
					$val = wfMsg( 'exif-focallength-format',
						self::formatNum( $val ) );
					break;

				// Do not transform fields with pure text.
				// For some languages the formatNum() conversion results to wrong output like
				// foo,bar@example,com or foo٫bar@example٫com
				case 'ImageDescription':
				case 'Artist':
				case 'Copyright':
				case 'RelatedSoundFile':
				case 'ImageUniqueID':
				case 'SpectralSensitivity':
				case 'GPSSatellites':
				case 'GPSVersionID':
				case 'GPSMapDatum':
				case 'Keywords':
				case 'CountryDest':
				case 'CountryDestCode':
				case 'ProvinceOrStateDest':
				case 'CityDest':
				case 'SublocationDest':
				case 'ObjectName':
				case 'SpecialInstructions':
				case 'Headline':
				case 'Credit':
				case 'Source':
				case 'EditStatus':
				case 'Urgency':
				case 'FixtureIdentifier':
				case 'LocationDest':
				case 'LocationDestCode':
				case 'Contact':
				case 'Writer':
				case 'JPEGFileComment':
				case 'iimCategory':
				case 'iimSupplementalCategory':
				case 'OriginalTransmissionRef':
				case 'Identifier':
				case 'dc-contributor':
				case 'dc-coverage':
				case 'dc-publisher':
				case 'dc-relation':
				case 'dc-rights':
				case 'dc-source':
				case 'dc-type':
				case 'Lens':
				case 'SerialNumber':
				case 'CameraOwnerName':
				case 'Label':
				case 'Nickname':
				case 'RightsCertificate':
				case 'CopyrightOwner':
				case 'UsageTerms':
				case 'WebStatement':
				case 'OriginalDocumentID':
				case 'LicenseUrl':
				case 'MorePermissionsUrl':
				case 'AttributionUrl':
				case 'PreferredAttributionName':
	
					$val = htmlspecialchars( $val );
					break;

				case 'ObjectCycle':
					switch ( $val ) {
					case 'a': case 'p': case 'b':
						$val = self::msg( $tag, $val );
						break;
					default:
						$val = htmlspecialchars( $val );
						break;
					}
					break;
				case 'Copyrighted':
					switch( $val ) {
					case 'True': case 'False':
						$val = self::msg( $tag, $val );
						break;
					}
					break;
				case 'Rating':
					if ( $val == '-1' ) {
						$val = self::msg( $tag, 'rejected' );
					} else {
						$val = self::formatNum( $val );
					}
					break;

				case 'LanguageCode':
					$lang = $wgLang->getLanguageName( strtolower( $val ) );
					if ($lang) {
						$val = htmlspecialchars( $lang );
					} else {
						$val = htmlspecialchars( $val );
					}
					break;

				default:
					$val = self::formatNum( $val );
					break;
				}
			}
			// End formatting values, start flattening arrays.
			$vals = self::flattenArray( $vals, $type );

		}
		return $tags;
	}

	/**
	* A function to collapse multivalued tags into a single value.
	* This turns an array of (for example) authors into a bulleted list.
	*
	* This is public on the basis it might be useful outside of this class.
	* 
	* @param $vals Array array of values
	* @param $type Type of array (either lang, ul, ol).
	* lang = language assoc array with keys being the lang code
	* ul = unorded list, ol = ordered list
	* type can also come from the '_type' member of $vals.
	* @return String single value (in wiki-syntax).
	*/
	public static function flattenArray( $vals, $type = 'ul' ) {

		if ( isset( $vals['_type'] ) ) {
			$type = $vals['_type'];
		}

		if ( !is_array( $vals ) ) {
			 return $vals; // do nothing if not an array;
		}
		elseif ( count( $vals ) === 1 && $type !== 'lang' ) {
			return $vals[0];
		}
		elseif ( count( $vals ) === 0 ) {
			return ""; // paranoia. This should never happen
			wfDebug( __METHOD__ . ' metadata array with 0 elements!' );
		}
		/* Fixme: This should hide some of the list entries if there are
		* say more than four. Especially if a field is translated into 20
		* languages, we don't want to show them all by default
		*/
		else {
			switch( $type ) {
			case 'lang':
				global $wgContLang;
				// Display default, followed by ContLang,
				// followed by the rest in no paticular
				// order.

				// Todo: hide some items if really long list.

				$content = '';

				$cLang = $wgContLang->getCode();
				$default = false;
				$defaultLang = false;

				// If default is set, save it for later,
				// as we don't know if it's equal to
				// one of the lang codes. (In xmp
				// you specify the language for a 
				// default property by having both
				// a default prop, and one in the language
				// that are identical)
				if ( isset( $vals['x-default'] ) ) {
					$defaultItem = $vals['x-default'];
					unset( $vals['x-default'] );
				}
				// Do contentLanguage.
				if ( isset( $vals[$cLang] ) ) {
					$isDefault = false;
					if ( $vals[$cLang] === $defaultItem ) {
						$defaultItem = false;
						$isDefault = true;
					}
					$content .= self::langItem(
						$vals[$cLang], $cLang,
						 $isDefault );

					unset( $vals[$cLang] );
				}

				// Now do the rest.
				foreach ( $vals as $lang => $item ) {
					if ( $item === $defaultItem ) {
						$defaultLang = $lang;
						continue;
					}
					$content .= self::langItem( $item,
						$lang );
				}
				if ( $defaultItem !== false ) {
					$content = self::langItem( $defaultItem,
						$defaultLang, true )
						 . $content;
				}
				return '<ul class="metadata-langlist">' .
					$content .
					'</ul>';
			case 'ol':
				return "<ol><li>" . implode( "</li>\n<li>", $vals ) . '</li></ol>';
			case 'ul':
			default:
				return "<ul><li>" . implode( "</li>\n<li>", $vals ) . '</li></ul>';
			}
		}
	}
	/** Helper function for creating lists of translations.
	 *
	 * @param $value String value (this is not escaped)
	 * @param $lang String lang code of item or false
	 * @param $default if it is default value.
	 * @return language item (Note: despite how this looks,
	 * 	this is treated as wikitext not html).
	 */
	private static function langItem( $value, $lang, $default = false ) {
		global $wgContLang;
		if ( $lang === false && $default === false) {
			throw new MWException('$lang and $default cannot both '
				. 'be false.');
		}

		$wrappedValue = '<span class="mw-metadata-lang-value">'
			. $value . '</span>';

		if ( $lang === false ) {
			return '<li class="mw-metadata-lang-default">'
				. wfMsg( 'metadata-langitem-default',
					$wrappedValue )
				. "</li>\n";
		}
		$langName = $wgContLang->getLanguageName( strtolower( $lang ) );
		if ( $langName === '' ) {
			//try just the base language name. (aka en-US -> en ).
			list( $langPrefix ) = explode( '-', strtolower( $lang ),
				2 );
			$langName = $wgContLang->getLanguageName( $langPrefix );
			if ( $langName === '' ) {
				// give up.
				$langName = $lang;
			}
		}
		// else we have a language specified
		$item = '<li class="mw-metadata-lang-code-'
			. $lang;
		if ( $default ) {
			$item .= ' mw-metadata-lang-default';
		}
		$item .= '" lang="' . $lang . '">';
		$item .= wfMsg( 'metadata-langitem',
			$wrappedValue, $langName, $lang );
		$item .= "</li>\n";
		return $item;
	}
	/**
	 * Convenience function for getFormattedData()
	 *
	 * @private
	 *
	 * @param $tag String: the tag name to pass on
	 * @param $val String: the value of the tag
	 * @param $arg String: an argument to pass ($1)
	 * @return string A wfMsg of "exif-$tag-$val" in lower case
	 */
	static function msg( $tag, $val, $arg = null ) {
		global $wgContLang;

		if ($val === '')
			$val = 'value';
		return wfMsg( $wgContLang->lc( "exif-$tag-$val" ), $arg );
	}

	/**
	 * Format a number, convert numbers from fractions into floating point
	 * numbers, joins arrays of numbers with commas.
	 *
	 * @private
	 *
	 * @param $num Mixed: the value to format
	 * @return mixed A floating point number or whatever we were fed
	 */
	static function formatNum( $num ) {
		global $wgLang;
		$m = array();
		if( is_array($num) ) {
			$out = array();
			foreach( $num as $number ) {
				$out[] = self::formatNum($number);
			}
			return $wgLang->commaList( $out );
		}
		if ( preg_match( '/^(-?\d+)\/(\d+)$/', $num, $m ) )
			return $wgLang->formatNum( $m[2] != 0 ? $m[1] / $m[2] : $num );
		else
			return $wgLang->formatNum( $num );
	}

	/**
	 * Format a rational number, reducing fractions
	 *
	 * @private
	 *
	 * @param $num Mixed: the value to format
	 * @return mixed A floating point number or whatever we were fed
	 */
	static function formatFraction( $num ) {
		$m = array();
		if ( preg_match( '/^(-?\d+)\/(\d+)$/', $num, $m ) ) {
			$numerator = intval( $m[1] );
			$denominator = intval( $m[2] );
			$gcd = self::gcd( abs( $numerator ), $denominator );
			if( $gcd != 0 ) {
				// 0 shouldn't happen! ;)
				return self::formatNum( $numerator / $gcd ) . '/' . self::formatNum( $denominator / $gcd );
			}
		}
		return self::formatNum( $num );
	}

	/**
	 * Calculate the greatest common divisor of two integers.
	 *
	 * @param $a Integer: Numerator
	 * @param $b Integer: Denominator
	 * @return int
	 * @private
	 */
	static function gcd( $a, $b ) {
		/*
			// http://en.wikipedia.org/wiki/Euclidean_algorithm
			// Recursive form would be:
			if( $b == 0 )
				return $a;
			else
				return gcd( $b, $a % $b );
		*/
		while( $b != 0 ) {
			$remainder = $a % $b;

			// tail recursion...
			$a = $b;
			$b = $remainder;
		}
		return $a;
	}

	/**
	 * Format a coordinate value, convert numbers from floating point
	 * into degree minute second representation.
	 *
	 * @private
	 *
	 * @param $coords Array: degrees, minutes and seconds
	 * @param $type String: latitude or longitude (for if its a NWS or E)
	 * @return mixed A floating point number or whatever we were fed
	 */
	static function formatCoords( $coord, $type ) {
		$ref = '';
		if ( $coord < 0 ) {
			$nCoord = -$coord;
			if ( $type === 'latitude' ) {
				$ref = 'S';
			}
			elseif ( $type === 'longitude' ) {
				$ref = 'W';
			}
		}
		else {
			$nCoord = $coord;
			if ( $type === 'latitude' ) {
				$ref = 'N';
			}
			elseif ( $type === 'longitude' ) {
				$ref = 'E';
			}
		}

		$deg = floor( $nCoord );
		$min = floor( ( $nCoord - $deg ) * 60.0 );
		$sec = round( ( ( $nCoord - $deg ) - $min / 60 ) * 3600, 2 );

		$deg = self::formatNum( $deg );
		$min = self::formatNum( $min );
		$sec = self::formatNum( $sec );

		return wfMsg( 'exif-coordinate-format', $deg, $min, $sec, $ref, $coord );
	}

}

/** For compatability with old FormatExif class
 * which some extensions use.
 *
 *@deprected
 *
**/
class FormatExif {
	var $meta;
	function FormatExif ( $meta ) {
		wfDeprecated(__METHOD__);
		$this->meta = $meta;
	}
	function getFormattedData ( ) {
		return FormatMetadata::getFormattedData( $this->meta );
	}

}
