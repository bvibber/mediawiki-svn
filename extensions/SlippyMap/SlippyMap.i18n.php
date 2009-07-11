<?php
/**
 * Internationalisation file for SlippyMap extension.
 *
 * @ingroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'slippymap_desc' => 'Adds a <tt>&lt;slippymap&gt;</tt> tag which allows for embedding of static & dynamic maps.Supports multiple map services including [http://openstreetmap.org OpenStreetMap] and NASA Worldwind',

	// The name of the extension, for use in error messages
	'slippymap_extname' => 'SlippyMap',
	'slippymap_tagname' => 'slippymap',

	/**
	 * User errors
	 */
	'slippymap_error' => "$1 error: $2",
	'slippymap_errors' => "$1 errors:",

	'slippymap_error_tag_content_given' => 'The <tt>&lt;$1&gt;</tt> tag only takes attribute arguments (&lt;$1 [...]/&gt;), not input text (&lt;$1&gt; ... &lt;/$1&gt;)',

	// Required parameters
	'slippymap_error_missing_arguments' => "You didn't supply any attributes to the &lt;$1&gt; tag, see [http://www.mediawiki.org/wiki/Extension:SlippyMap#Syntax usage information] for how to call it.",

	// Required parameters
	'slippymap_error_missing_attribute_lat' => "Missing <tt>lat</tt> attribute (for the latitude).",
	'slippymap_error_missing_attribute_lon' => "Missing <tt>lon</tt> attribute (for the longitude).",
	'slippymap_error_missing_attribute_zoom' => "Missing <tt>zoom</tt> attribute (for the zoom level).",

	// Invalid value
	'slippymap_error_invalid_attribute_lat_value_nan' => "The value <tt>$1</tt> is not valid for the <tt>lat</tt> (latitude) attribute, the given value must be a valid number.",
	'slippymap_error_invalid_attribute_lon_value_nan' => "The value <tt>$1</tt> is not valid for the <tt>lon</tt> (longitude) attribute, the given value must be a valid number.",
	'slippymap_error_invalid_attribute_zoom_value_nan' => "The value <tt>$1</tt> is not valid for the <tt>zoom</tt> attribute, the given value must be a valid number.",
	'slippymap_error_invalid_attribute_width_value_nan' => "The value <tt>$1</tt> is not valid for the <tt>width</tt> attribute, the given value must be a valid number.",
	'slippymap_error_invalid_attribute_height_value_nan' => "The value <tt>$1</tt> is not valid for the <tt>height</tt> attribute, the given value must be a valid number.",
	'slippymap_error_invalid_attribute_mode_value_not_a_mode' => "The value <tt>$1</tt> is not valid for the <tt>mode</tt> attribute, valid modes are $2.",
	'slippymap_error_invalid_attribute_layer_value_not_a_layer' => "The value <tt>$1</tt> is not valid for the <tt>layer</tt> attribute, valid layers are $2.",
	'slippymap_error_invalid_attribute_marker_value_not_a_marker' => "The value <tt>$1</tt> is not valid for the <tt>marker</tt> attribute, valid markers are $2.",
	'slippymap_error_unknown_attribute' => "The attribute <tt>$1</tt> is unknown.",

	// Value out of range
	'slippymap_error_invalid_attribute_lat_value_out_of_range' => "The value <tt>$1</tt> is not valid for the <tt>lat</tt> (latitude) attribute. Latitutes bust be between -90 and 90 degrees.",
	'slippymap_error_invalid_attribute_lon_value_out_of_range' => "The value <tt>$1</tt> is not valid for the <tt>lon</tt> (longitude) attribute. Longitudes must be between -180 and 180 degrees.",
	'slippymap_error_invalid_attribute_zoom_value_out_of_range' => "The value <tt>$1</tt> is not valid for the <tt>zoom</tt> attribute. Zoom levels must be between $2 and $3.",
	'slippymap_error_invalid_attribute_width_value_out_of_range' => "The value <tt>$1</tt> is not valid for the <tt>width</tt> attribute. Width levels must be between $2 and $3.",
	'slippymap_error_invalid_attribute_height_value_out_of_range' => "The value <tt>$1</tt> is not valid for the <tt>height</tt> attribute. Height levels must be between $2 and $3.",

	'slippymap_code'    => 'Wikicode for this map view:',
	'slippymap_button_code' => 'Get wikicode',
	'slippymap_resetview' => 'Reset view',
	'slippymap_clicktoactivate' => 'Click to activate map'
);


