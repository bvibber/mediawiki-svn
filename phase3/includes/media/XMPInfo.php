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
		'http://ns.adobe.com/tiff/1.0/' => array(
			'Artist'            => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
			'BitsPerSample'     => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SEQ,
				'validate'  => 'validateInteger',
			),
			'Compression'       => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateClosed',
				'choices'   => array( '1' => true, '6' => true ),
			),
			/* this prop should not be used in XMP. dc:rights is the correct prop */
			'Copyright'         => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_LANG,
			),
			'DateTime'          => array(  /* proper prop is xmp:ModifyDate */
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateDate',
			),
			'ImageDescription'  => array(  /* proper one is dc:description */
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_LANG,
			),
			'ImageLength'       => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateInteger',
			),
			'ImageWidth'        => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateInteger',
			),
			'Make'              => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
			'Model'             => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
			'Orientation'       => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateClosed',
				'choices'   => array( '1' => true, '2' => true, '3' => true, '4' => true, 5 => true,
						'6' => true, '7' => true, '8' => true ),
			),
			'PhotometricInterpretation' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateClosed',
				'choices'   => array( '2' => true, '6' => true ),
			),
			'PlanerConfiguration' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateClosed',
				'choices'   => array( '1' => true, '2' => true ),
			),
			'PrimaryChromaticities' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SEQ,
				'validate'  => 'validateRational',
			),
			'ReferenceBlackWhite' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SEQ,
				'validate'  => 'validateRational',
			),
			'ResolutionUnit'    => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateClosed',
				'choices'   => array( '2' => true, '3' => true ),
			),
			'SamplesPerPixel'   => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateInteger',
			),
			'Software'          => array(  /* see xmp:CreatorTool */
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
			/* ignore TransferFunction */
			'WhitePoint'        => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SEQ,
				'validate'  => 'validateRational',
			),
			'XResolution'       => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational',
			),
			'YResolution'       => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateRational',
			),
			'YCbCrCoefficients' => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SEQ,
				'validate'  => 'validateRational',
			),
			'YCbCrPositioning'  => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
				'validate'  => 'validateClosed',
				'choices'   => array( '1' => true, '2' => true ),
			),
			'YCbCrSubSampling'  => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SEQ,
				'validate'  => 'validateClosed',
				'choices'   => array( '1' => true, '2' => true ),
			),
		),
		'http://ns.adobe.com/exif/1.0/aux/' => array(
			'Lens'              => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
			'SerialNumber'      => array(
				'map_group' => 'exif',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
		),
		'http://purl.org/dc/elements/1.1/' => array(
			'title'             => array(
				'map_group' => 'general',
				'map_name'  => 'Headline',
				'mode'      => XMPReader::MODE_LANG
			),
			'description'       => array(
				'map_group' => 'general',
				'map_name'  => 'ImageDescription',
				'mode'      => XMPReader::MODE_LANG
			),
			'contributor'       => array(
				'map_group' => 'general',
				'map_name'  => 'dc-contributor',
				'mode'      => XMPReader::MODE_BAG
			),
			'coverage'          => array(
				'map_group' => 'general',
				'map_name'  => 'dc-coverage',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
			'creator'           => array(
				'map_group' => 'general',
				'map_name'  => 'Artist', //map with exif Artist, iptc bylin (2:80)
				'mode'      => XMPReader::MODE_SEQ,
			),
			'date'              => array(
				'map_group' => 'general',
				// Note, not mapped with other date properties, as this type of date is
				// non-specific: "A point or period of time associated with an event in
				//  the lifecycle of the resource"
				'map_name'  => 'dc-date',
				'mode'      => XMPReader::MODE_SEQ,
				'validate'  => 'validateDate',
			),
			/* Do not extract dc:format, as we've got better ways to determine mimetype */
			'identifier'         => array(
				'map_group' => 'deprected',
				'map_name'  => 'Identifier',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
			'language'          => array(
				'map_group' => 'general',
				'map_name'  => 'LanguageCode', /* mapped with iptc 2:135 */
				'mode'      => XMPReader::MODE_BAG,
				'validate'  => 'validateLangCode',
			),
			'publisher'         => array(
				'map_group' => 'general',
				'map_name'  => 'dc-publisher',
				'mode'      => XMPReader::MODE_BAG,
			),
			// for related images/resources
			'relation'          => array(
				'map_group' => 'general',
				'map_name'  => 'dc-relation',
				'mode'      => XMPReader::MODE_BAG,
			),
			'rights'            => array(
				'map_group' => 'general',
				'map_name'  => 'dc-rights',
				'mode'      => XMPReader::MODE_LANG,
			),
			// Note: source is not mapped with iptc source, since iptc
			// source describes the source of the image in terms of a person
			// who provided the image, where this is to describe an image that the
			// current one is based on.
			'source'            => array(
				'map_group' => 'general',
				'map_name'  => 'dc-source',
				'mode'      => XMPReader::MODE_SIMPLE,
			),
			'subject'           => array(
				'map_group' => 'general',
				'map_name'  => 'Keywords', /* maps to iptc 2:25 */
				'mode'      => XMPReader::MODE_BAG,
			),
			'type'              => array(
				'map_group' => 'general',
				'map_name'  => 'dc-type',
				'mode'      => XMPReader::MODE_BAG,
			),
		),
		//Note, this property affects how jpeg metadata is extracted.
		'http://ns.adobe.com/xmp/note/' => array(
			'HasExtendedXMP' => array(
				'map_group' => 'special',
				'mode'    => XMPReader::MODE_SIMPLE,
			),
		),
	);
}
