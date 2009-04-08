<?php
# OpenStreetMap SlippyMap - MediaWiki extension
#
# This defines what happens when <slippymap> tag is placed in the wikitext
#
# We show a map based on the lat/lon/zoom data passed in. This extension brings in
# the OpenLayers javascript, to show a slippy map.
#
# Usage example:
# <slippymap lat="51.485" lon="-0.15" z="11" w="300" h="200" layer="osmarender" marker="0" />
#
# Tile images are not cached local to the wiki.
# To acheive this (remove the OSM dependency) you might set up a squid proxy,
# and modify the requests URLs here accordingly.
#
# This file should be placed in the mediawiki 'extensions' directory
# ...and then it needs to be 'included' within LocalSettings.php
#
# #################################################################################
#
# Copyright 2008 Harry Wood, Jens Frank, Grant Slater, Raymond Spekking and others
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# @addtogroup Extensions
#

# Evil hack as documented at
# http://www.mediawiki.org/wiki/Manual:Tag_extensions#How_can_I_avoid_modification_of_my_extension.27s_HTML_output.3F
# This is here so that random <p> and <pre> tags aren't added to the inline JavaScript output
$SlippyMapMarkerList = array( );

# The number of Slippy Maps we have had thus far, to get unique JavaScript variables
$SlippyMapCounter = 1;

class SlippyMap {
	# The callback function for converting the input text to HTML output
	static function parse( $input, $argv ) {
		global $SlippyMapMarkerList, $SlippyMapCounter;
		wfLoadExtensionMessages( 'SlippyMap' );

		// Support old style parameters from $input
		// Parse the pipe separated name value pairs (e.g. 'aaa=bbb|ccc=ddd')
		// With the new syntax we expect nothing in the $input, so this will result in '' values
		$oldStyleParamStrings = explode( '|', $input );
		foreach ( $oldStyleParamStrings as $oldStyleParamString ) {
			$oldStyleParamString = trim( $oldStyleParamString );
			$eqPos = strpos( $oldStyleParamString, "=" );
			if ( $eqPos === false ) {
				$oldStyleParams[$oldStyleParamString] = "true";
			} else {
				$oldStyleParams[substr( $oldStyleParamString, 0, $eqPos )] = trim( htmlspecialchars( substr( $oldStyleParamString, $eqPos + 1 ) ) );
			}
		}

		// Receive new style args: <slippymap aaa=bbb ccc=ddd></slippymap>
		if ( isset( $argv['lat'] ) ) {
			$lat = $argv['lat'];
		} else if ( isset( $oldStyleParams['lat'] ) ) {
			$lat = $oldStyleParams['lat'];
		} else {
			$lat = '';
		}
		if ( isset( $argv['lon'] ) ) {
			$lon = $argv['lon'];
		} else if ( isset( $oldStyleParams['lon'] ) ) {
			$lon = $oldStyleParams['lon'];
		} else {
			$lon = '';
		}
		if ( isset( $argv['z'] ) ) {
			$zoom = $argv['z'];
		} else if ( isset( $oldStyleParams['z'] ) ) {
			$zoom = $oldStyleParams['z'];
		} else {
			$zoom = '';
		}
		if ( isset( $argv['w'] ) ) {
			$width = $argv['w'];
		} else if ( isset( $oldStyleParams['w'] ) ) {
			$width = $oldStyleParams['w'];
		} else {
			$width = '';
		}
		if ( isset( $argv['h'] ) ) {
			$height = $argv['h'];
		} else if ( isset( $oldStyleParams['h'] ) ) {
			$height = $oldStyleParams['h'];
		} else {
			$height = '';
		}
		if ( isset( $argv['layer'] ) ) {
			$layer = $argv['layer'];
		} else if ( isset( $oldStyleParams['layer'] ) ) {
			$layer = $oldStyleParams['layer'];
		} else {
			$layer = '';
		}
		if ( isset( $argv['marker'] ) ) {
			$marker = $argv['marker'];
		} else {
			$marker = '';
		}

		$error = '';

		// default values (meaning these parameters can be missed out)
		if ( $width == '' ) $width = '450';
		if ( $height == '' ) $height = '320';
		if ( $layer == '' ) $layer = 'mapnik';

		if ( $zoom == '' && isset( $argv['zoom'] ) ) {
			$zoom = $argv['zoom']; // see if they used 'zoom' rather than 'z' (and allow it)
		}

		$marker = ( $marker != '' && $marker != '0' );

		// trim off the 'px' on the end of pixel measurement numbers (ignore if present)
		if ( substr( $width, -2 ) == 'px' )
			$width = (int) substr( $width, 0, -2 );

		if ( substr( $height, - 2 ) == 'px' )
			$height = (int) substr( $height, 0, -2 );

		if ( trim( $input ) != '' && sizeof( $oldStyleParamStrings ) < 3 ) {
			$error = 'slippymap tag contents. Were you trying to input KML? KML support ' .
				'is disabled pending discussions about wiki syntax';
			$showkml = false;
// KML code:
//		if ( $showkml ) {
//			$input = str_replace( array( '%',   "\n" , "'"  , '"'  , '<'  , '>'  , ' '   ),
//			array( '%25', '%0A', '%27', '%22', '%3C', '%3E', '%20' ), $input );
//			$output .= 'var vector = new OpenLayers.Layer.Vector("Vector Layer"); ' .
//			'   map.addLayer(vector); ' .
//			'   kml = new OpenLayers.Format.KML( { "internalProjection": map.baseLayer.projection, ' .
//			'                                      "externalProjection": epsg4326, ' .
//			'                                      "extractStyles": true, ' .
//			'                                      "extractAttributes": true } ); ' .
//			"   features = kml.read(unescape('$input')); " .
//			'   vector.addFeatures( features ); ';
//		}
		} else {
			$showkml = false;
		}

		// Check required parameters values are provided
		if ( $lat == ''  ) $error .= wfMsg( 'slippymap_latmissing' ) . ' ';
		if ( $lon == ''  ) $error .= wfMsg( 'slippymap_lonmissing' ) . ' ';
		if ( $zoom == '' ) $error .= wfMsg( 'slippymap_zoommissing' ) . ' ';

		if ( $error == '' ) {
			// no errors so far. Now check the values
			if ( !is_numeric( $width ) ) {
				$error = wfMsg( 'slippymap_widthnan', $width );
			} else if ( !is_numeric( $height ) ) {
				$error = wfMsg( 'slippymap_heightnan', $height );
			} else if ( !is_numeric( $zoom ) ) {
				$error = wfMsg( 'slippymap_zoomnan', $zoom );
			} else if ( !is_numeric( $lat ) ) {
				$error = wfMsg( 'slippymap_latnan', $lat );
			} else if ( !is_numeric( $lon ) ) {
				$error = wfMsg( 'slippymap_lonnan', $lon );
			} else if ( $width > 1000 ) {
				$error = wfMsg( 'slippymap_widthbig' );
			} else if ( $width < 100 ) {
				$error = wfMsg( 'slippymap_widthsmall' );
			} else if ( $height > 1000 ) {
				$error = wfMsg( 'slippymap_heightbig' );
			} else if ( $height < 100 ) {
				$error = wfMsg( 'slippymap_heightsmall' );
			} else if ( $lat > 90 ) {
				$error = wfMsg( 'slippymap_latbig' );
			} else if ( $lat < -90 ) {
				$error = wfMsg( 'slippymap_latsmall' );
			} else if ( $lon > 180 ) {
				$error = wfMsg( 'slippymap_lonbig' );
			} else if ( $lon < -180 ) {
				$error = wfMsg( 'slippymap_lonsmall' );
			} else if ( $zoom < 0 ) {
				$error = wfMsg( 'slippymap_zoomsmall' );
			} else if ( $zoom == 18 ) {
				$error = wfMsg( 'slippymap_zoom18' );
			} else if ( $zoom > 17 ) {
				$error = wfMsg( 'slippymap_zoombig' );
			}
		}

		// Find the tile server URL to use.  Note that we could allow the user to override that with
		// *any* tile server URL for more flexibility, but that might be a security concern.

		$layer = strtolower( $layer );
		$layerObjectDef = '';
		if ( $layer == 'osmarender' ) {
			$layerObjectDef = 'OpenLayers.Layer.OSM.Osmarender("Osmarender");';
		} elseif ( $layer == 'mapnik' ) {
			$layerObjectDef = 'OpenLayers.Layer.OSM.Mapnik("Mapnik");';
		} elseif ( $layer == 'maplint' ) {
			$layerObjectDef = 'OpenLayers.Layer.OSM.Maplint("Maplint");';
		} else {
			$error = wfMsg( 'slippymap_invalidlayer',  htmlspecialchars( $layer ) );
		}

		if ( $error != "" ) {
			// Something was wrong. Spew the error message and input text.
			$output  = '';
			$output .= "<span class=\"error\">" . wfMsg( 'slippymap_maperror' ) . ' ' . $error . "</span><br />";
			$output .= htmlspecialchars( $input );
			return $output;
		} else {
			$output = self::makeSlippyMapHTMLandJS($lon, $lat, $zoom, $height, $width, $layerObjectDef, $marker, $SlippyMapCounter++);

			$markercount = count($SlippyMapMarkerList);
			$pMarker = "SlippyMap-marker".$markercount."-SlippyMap";
			$SlippyMapMarkerList[$markercount] = $output;
			return $pMarker;
		}
	}

	static function makeSlippyMapHTMLandJS($lon, $lat, $zoom, $height, $width, $layerObjectDef, $marker, $unique) {
		global $wgScriptPath, $wgMapOfServiceUrl;
		// HTML output for the slippy map.
		// Note that this must all be output on one line (no linefeeds)
		// otherwise MediaWiki adds <BR> tags, which is bad in the middle of a block of javascript.
		// There are other ways of fixing this, but not for MediaWiki v4
		// (See http://www.mediawiki.org/wiki/Manual:Tag_extensions#How_can_I_avoid_modification_of_my_extension.27s_HTML_output.3F)

		$slippymap_code_marker = '';
		if ( $marker ) {
		$slippymap_code_marker = <<<EOT
var markers${unique} = new OpenLayers.Layer.Markers( "Markers" );
map${unique}.addLayer(markers${unique});
var size${unique} = new OpenLayers.Size(20,34);
var offset${unique} = new OpenLayers.Pixel(-(size${unique}.w/2), -size${unique}.h);
var icon${unique} = new OpenLayers.Icon('http://boston.openguides.org/markers/YELLOW.png', size${unique},offset${unique});
markers${unique}.addMarker(new OpenLayers.Marker( lonLat${unique}, icon${unique} ));
EOT;
		}

		$slippymap_code_escaped = Xml::escapeJsString( wfMsg( 'slippymap_code' ) );
		$slippymap_button_code = Xml::escapeJsString( wfMsg( 'slippymap_button_code' ) );
		$slippymap_resetview = Xml::escapeJsString( wfMsg( 'slippymap_resetview' ) );

		$slippymap_zoom_snippet = '';
		if ( $height > 320 ) {
			// Add the zoom bar control, except if the map is only little
			$slippymap_zoom_snippet = 'new OpenLayers.Control.PanZoomBar(),';
		} else if ( $height > 140 ) {
			$slippymap_zoom_snippet = 'new OpenLayers.Control.PanZoom(),';
		}

		$output = <<<EOT
<!-- Slippy Map -->

<!-- This inline stylesheet defines how the two extra buttons look, and where they are positioned. -->
<style> .buttonsPanel div { float:left; display:block; position:relative; left:50px; margin-left:3px; margin-top:7px; width:36px;  height:19px; }</style>
<style> .buttonsPanel .getWikiCodeButtonItemInactive { width:36px; height:19px; background-image:url('$wgScriptPath/extensions/SlippyMap/wikicode-button.png'); }</style>
<style> .buttonsPanel .resetButtonItemInactive       { width:36px; height:19px; background-image:url('$wgScriptPath/extensions/SlippyMap/reset-button.png'); }</style>

<!-- bring in the OpenLayers javascript library -->
<script src="http://openlayers.org/api/OpenLayers.js"></script>

<!-- bring in the OpenStreetMap OpenLayers layers.
     Using this hosted file will make sure we are kept up
     to date with any necessary changes -->
<script src="http://openstreetmap.org/openlayers/OpenStreetMap.js"></script>

<script type="text/javascript">
	var lon${unique} = ${lon};
	var lat${unique} = ${lat};
	var zoom${unique} = ${zoom};

	var lonLat${unique};
	var map${unique};

	addOnloadHook( slippymap_init );

	function slippymap_resetPosition${unique}() {
		map${unique}.setCenter(lonLat${unique}, zoom${unique});
	}

	function slippymap_getWikicode${unique}() {
		LL = map${unique}.getCenter().transform(map${unique}.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
		Z = map${unique}.getZoom();
		size = map${unique}.getSize();
		prompt(
		    "${slippymap_code_escaped}",
		    "<slippymap h=" + size.h + " w=" + size.w + " z=" + Z + " lat=" + LL.lat + " lon=" + LL.lon + " layer=mapnik marker=1 />"
		);
	}

	function slippymap_init() {
		// FIXME: Replace with OpenLayers.Layer.OSM?: http://trac.openlayers.org/changeset/9240
		map${unique} = new OpenLayers.Map("map${unique}", {
			controls:[
				new OpenLayers.Control.Navigation(),
				$slippymap_zoom_snippet
				new OpenLayers.Control.Attribution()
			],
			maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
			maxResolution:156543.0399,
			units: 'meters',
			projection: "EPSG:900913"
		});

		layer = new ${layerObjectDef};
		map${unique}.addLayer(layer);
		epsg4326 = new OpenLayers.Projection("EPSG:4326");
		lonLat${unique} = new OpenLayers.LonLat(lon${unique}, lat${unique}).transform( epsg4326, map${unique}.getProjectionObject() );

		$slippymap_code_marker

		map${unique}.setCenter(lonLat${unique}, zoom${unique});

		var getWikiCodeButton${unique} = new OpenLayers.Control.Button({
			title: "${slippymap_button_code}",
			displayClass: "getWikiCodeButton",
			trigger: slippymap_getWikicode${unique}
		});

		var resetButton${unique} = new OpenLayers.Control.Button({
			title: "${slippymap_resetview}",
			displayClass: "resetButton",
			trigger: slippymap_resetPosition${unique}
		});

		var panel${unique} = new OpenLayers.Control.Panel( { displayClass: "buttonsPanel" } );
		panel${unique}.addControls([getWikiCodeButton${unique}, resetButton${unique}]);
		map${unique}.addControl(panel${unique});
	}
</script>

<div style="width: {$width}px; height:{$height}px; border-style:solid; border-width:1px; border-color:lightgrey;" id="map${unique}">
<noscript>
	<a href="http://www.openstreetmap.org/?lat=${lat}&lon=${lon}&zoom=${zoom}" title="See this map on OpenStreetMap.org" style="text-decoration:none">
		<img
		src="${wgMapOfServiceUrl}lat=${lat}&long=${lon}&z=${zoom}&w=${width}&h=${height}&format=jpeg"
		width="${width}"
		height="${height}"
		border="0"
		alt="Slippy Map" />
		<br />
	</a>
</noscript>
</div>
EOT;

		return $output;
	}

	// Hack, see beginning of file for why
	static function ParserAfterTidy(&$parser, &$text) {
		global $SlippyMapMarkerList;
		$keys = array();
		$marker_count = count($SlippyMapMarkerList);
	
		for ($i = 0; $i < $marker_count; $i++) {
			$keys[] = 'SlippyMap-marker' . $i . '-SlippyMap';
		}
 
		$text = str_replace($keys, $SlippyMapMarkerList, $text);
		return true;
	}
}
