<?php
/**
 * Internationalisation file for SlippyMap extension.
 *
 * @ingroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'slippymap_desc' => "Allows the use of the <tt><nowiki>&lt;slippymap&gt;</nowiki></tt> tag to display an OpenLayers slippy map. Maps are from [http://openstreetmap.org openstreetmap.org]",
	'slippymap_latmissing' => "Missing lat value (for the latitude).",
	'slippymap_lonmissing' => "Missing lon value (for the longitude).",
	'slippymap_zoommissing' => "Missing z value (for the zoom level).",
	'slippymap_longdepreciated' => "Please use 'lon' instead of 'long' (parameter was renamed).",
	'slippymap_widthnan' => "width (w) value '%1' is not a valid integer",
	'slippymap_heightnan' => "height (h) value '%1' is not a valid integer",
	'slippymap_zoomnan' => "zoom (z) value '%1' is not a valid integer",
	'slippymap_latnan' => "latitude (lat) value '%1' is not a valid number",
	'slippymap_lonnan' => "longitude (lon) value '%1' is not a valid number",
	'slippymap_widthbig' => "width (w) value cannot be greater than 1000",
	'slippymap_widthsmall' => "width (w) value cannot be less than 100",
	'slippymap_heightbig' => "height (h) value cannot be greater than 1000",
	'slippymap_heightsmall' => "height (h) value cannot be less than 100",
	'slippymap_latbig' => "latitude (lat) value cannot be greater than 90",
	'slippymap_latsmall' => "latitude (lat) value cannot be less than -90",
	'slippymap_lonbig' => "longitude (lon) value cannot be greater than 180",
	'slippymap_lonsmall' => "longitude (lon) value cannot be less than -180",
	'slippymap_zoomsmall' => "zoom (z) value cannot be less than zero",
	'slippymap_zoom18' => "zoom (z) value cannot be greater than 17. Note that this mediawiki extension hooks into the OpenStreetMap 'osmarender' layer which does not go beyond zoom level 17. The Mapnik layer available on openstreetmap.org, goes up to zoom level 18",
	'slippymap_zoombig' => "zoom (z) value cannot be greater than 17.",
	'slippymap_invalidlayer' => "Invalid 'layer' value '%1'",
	'slippymap_maperror' => "Map error:",
	'slippymap_osmlink' => 'http://www.openstreetmap.org/?lat=%1&lon=%2&zoom=%3', # do not translate or duplicate this message to other languages
	'slippymap_osmtext' => 'See this map on OpenStreetMap.org',
	'slippymap_license' => 'OpenStreetMap - CC-BY-SA-2.0', # do not translate or duplicate this message to other languages
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'slippymap_desc'            => "Ermöglicht die Nutzung des <tt><nowiki>&lt;slippymap&gt;</nowiki></tt>-Tags zur Anzeige einer OpenLayer-SlippyMap. Die Karten stammen von [http://openstreetmap.org openstreetmap.org]",
	'slippymap_latmissing'      => "Es wurde kein Wert für die geografische Breite (lat) angegeben.",
	'slippymap_lonmissing'      => "Es wurde kein Wert für die geografische Länge (lon) angegeben.",
	'slippymap_zoommissing'     => "Es wurde kein Zoom-Wert (z) angegeben.",
	'slippymap_longdepreciated' => "Bitte benutze 'lon' an Stelle von 'long' (Parameter wurde umbenannt). ",
	'slippymap_widthnan'        => "Der Wert für die Breite (w) '%1' ist keine gültige Zahl",
	'slippymap_heightnan'       => "Der Wert für die Höhe (h) '%1' ist keine gültige Zahl",
	'slippymap_zoomnan'         => "Der Wert für den Zoom (t) '%1' ist keine gültige Zahl",
	'slippymap_latnan'          => "Der Wert für die geografische Breite (lat) '%1' ist keine gültige Zahl",
	'slippymap_lonnan'          => "Der Wert für die geografische Länge (lon) '%1' ist keine gültige Zahl",
	'slippymap_widthbig'        => "Die Breite (w) darf 1000 nicht überschreiten",
	'slippymap_widthsmall'      => "Die Breite (w) darf 100 nicht unterschreiten",
	'slippymap_heightbig'       => "Die Höhe (h) darf 1000 nicht überschreiten",
	'slippymap_heightsmall'     => "Die Höhe (h) darf 100 nicht unterschreiten",
	'slippymap_latbig'          => "Die geografische Breite darf nicht größer als 90 sein",
	'slippymap_latsmall'        => "Die geografische Breite darf nicht kleiner als -90 sein",
	'slippymap_lonbig'          => "Die geografische Länge darf nicht größer als 180 sein",
	'slippymap_lonsmall'        => "Die geografische Länge darf nicht kleiner als -180 sein",
	'slippymap_zoomsmall'       => "Der Zoomwert darf nicht negativ sein",
	'slippymap_zoom18'          => "Der Zoomwert (z) kann nicht größer als 17 sein. Beachten, dass diese MediaWiki-Erweiterung die OpenStreetMap 'Osmarender'-Karte einbindet, sie nicht höher als Zoom 17 geht. Die Mapnik-Karte ist auf openstreetmap.org verfügbar und geht bis Zoom 18.",
	'slippymap_zoombig'         => "Der Zoomwert (z) kann nicht größer als 17 sein.",
	'slippymap_invalidlayer'    => "Ungültiger 'layer'-Wert „%1“",
	'slippymap_maperror'        => "Kartenfehler:",
	'slippymap_osmtext'         => 'Siehe diese Karte auf OpenStreetMap.org',
);
