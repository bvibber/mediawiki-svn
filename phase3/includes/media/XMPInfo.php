<?php
/**
* This class is just a container for a big array
* used by XMPReader to determine which XMP items to
* extract.
*/
class XMPInfo {

	/** get the items array
	 * @return Array XMP item configuration array.
	*/
	public static function getItems ( ) {
		return self::$items;
	}

	/**
	* XMPInfo::$items keeps a list of all the items
	* we are interested to extract, as well as
	* information about the item like what type
	* it is.
	*
	* Format is an array of namespaces,
	* each containing an array of tags
	* each tag is an array of information about the
	* tag, including:
	*       * map_group - what group (used for precedence during conflicts)
	*       * mode - What type of item (self::MODE_SIMPLE usually, see above for all values)
	*       * validate - method to validate input. Could also post-process the input. (TODO: implement this)
	*       * choices  - array of potential values (format of 'value' => true )
	*       * children - for MODE_STRUCT items, allowed children.
	*
	* currently this just has a bunch of exif values as this class is only half-done
	*/

	static private $items = array(
		'http://ns.adobe.com/exif/1.0/' => array(
			'ApertureValue' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'BrightnessValue' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'CompressedBitsPerPixel' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'DigitalZoomRatio' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'ExposureBiasValue' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'ExposureIndex' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'ExposureTime' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'FlashEnergy' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'FNumber' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'FocalLength' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'FocalPlaneXResolution' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'FocalPlaneYResolution' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			/* FIXME GPSAltitude */
			'GPSDestBearing' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'GPSDestDistance' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'GPSDOP' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'GPSImgDirection' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'GPSSpeed' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'GPSTrack' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'MaxApertureValue'  => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'ShutterSpeedValue' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			'SubjectDistance'   => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational'
			),
			/* Flash */
			'Flash'             => array(
				'mode'      => XMPReader::MODE_STRUCT,
				'children'  => array(
					'Fired'      => true,
					'Function'   => true,
					'Mode'       => true,
					'RedEyeMode' => true,
					'Return'     => true,
				),
				'validate'  => 'validateFlash',
				'map_group' => 'exif',
			),
			'Fired'             => array(
				'map_group' => 'exif',
				'validate'  => 'validateBoolean',
				'mode'      => XMPReader::MODE_SIMPLE
			),
			'Function'          => array(
				'map_group' => 'exif',
				'validate'  => 'validateBoolean',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
			'Mode'              => array(
				'map_group' => 'exif',
				'validate'  => 'validateClosed',
				'mode'      => XMPReader::MODE_SIMPLE,
				'choices'   => array( '0' => true, '1' => true,
						'2' => true, '3' => true ),
			),
			'Return'            => array(
				'map_group' => 'exif',
				'validate'  => 'validateClosed',
				'mode'      => XMPReader::MODE_SIMPLE,
				'choices'   => array( '0' => true,
						'2' => true, '3' => true ),
			),
			'RedEyeMode'        => array(
				'map_group' => 'exif',
				'validate'  => 'validateBoolean',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
			/* End Flash */
			'ISOSpeedRatings'   => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SEQ,
			),
		),
		'http://purl.org/dc/elements/1.1/' => array(
			'title'             => array(
				'map_group' => 'general',
				'map_name'  => 'Headline',
				'mode'      => XMPReader::MODE_LANG
			),
			'description'             => array(
				'map_group' => 'general',
				'map_name'  => 'ImageDescription',
				'mode'      => XMPReader::MODE_LANG
			),

		),
	);
}
