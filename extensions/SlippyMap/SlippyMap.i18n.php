<?php

$messages = array();

$messages['en'] = array( 
	'slippymap_latmissing' => "Missing lat value (for the lattitude). ",
	'slippymap_lonmissing' => "Missing lon value (for the longitude). ",
	'slippymap_zoommissing' => "Missing z value (for the zoom level). ",
	'slippymap_longdepreciated' => "Please use 'lon' instead of 'long' (parameter was renamed). ",
	'slippymap_widthnan' => "width (w) value '%1' is not a valid integer",
	'slippymap_heightnan' => "height (h) value '%1' is not a valid integer",
	'slippymap_zoomnan' => "zoom (z) value '%1' is not a valid integer",
	'slippymap_latnan' => "lattitude (lat) value '%1' is not a valid number",
	'slippymap_lonnan' => "logiditude (lon) value '%1' is not a valid number",
	'slippymap_widthbig' => "width (w) value cannot be greater than 1000",
	'slippymap_widthsmall' => "width (w) value cannot be less than 100",
	'slippymap_heightbig' => "height (h) value cannot be greater than 1000",
	'slippymap_heightsmall' => "height (h) value cannot be less than 100",
	'slippymap_latbig' => "lattitude (lat) value cannot be greater than 90",
	'slippymap_latsmall' => "lattitude (lat) value cannot be less than -90",
	'slippymap_lonbig' => "longitude (lon) value cannot be greater than 180",
	'slippymap_lonsmall' => "longitude (lon) value cannot be less than -180",
	'slippymap_zoomsmall' => "zoom (z) value cannot be less than zero",
	'slippymap_zoom18' => "zoom (z) value cannot be greater than 17. Note that this mediawiki extension hooks into the OpenStreetMap 'osmarender' layer which does not go beyond zoom level 17. The Mapnik layer available on openstreetmap.org, goes up to zoom level 18",
	'slippymap_zoombig' => "zoom (z) value cannot be greater than 17.",
	'slippymap_invalidlayer' => "Invalid 'layer' value '%1'",
	'slippymap_maperror' => "Map error:",
	'slippymap_osmlink' => 'http://www.openstreetmap.org/?lat=%1&lon=%2&zoom=%3',
	'slippymap_osmtext' => 'See this map on OpenStreetMap.org',
	'slippymap_license' => 'OpenStreetMap - CC-BY-SA-2.0',
);
