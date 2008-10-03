<?php

if (!defined('MEDIAWIKI')) die();

/**
 * An extension to that allows users to add Google Maps to wiki pages based
 * on structured data
 *
 * @addtogroup Extensions
 *
 * @author Robert Buzink
 * @author Yaron Koren
 */

# Define a setup function
$wgExtensionFunctions[] = 'sgmSetup';

# Add a hook to initialise the magic word
$wgHooks['LanguageGetMagic'][] = 'sgmFunctionMagic';

$wgExtensionMessagesFiles['SemanticGoogleMaps'] = dirname(__FILE__) . '/SemanticGoogleMaps.i18n.php';

function sgmSetup() {
	global $wgParser, $wgExtensionCredits;

	// credits
	$wgExtensionCredits['parserhook'][] = array(
		'name'            => 'Semantic Google Maps',
		'version'         => '0.4',
		'author'          => array( 'Robert Buzink', 'Yaron Koren' ),
		'url'             => 'http://www.mediawiki.org/wiki/Extension:Semantic_Google_Maps',
		'description'     => 'Allows users to add Google Maps to wiki pages based on structured data',
		'descriptionmsg'  => 'semanticgooglemaps-desc',
	);

	// A hook to enable the '#semantic_google_map' parser function
	$wgParser->setFunctionHook( 'semantic_google_map', 'sgmFunctionRender' );
	global $sfgFormPrinter;
	if ($sfgFormPrinter) {
		$sfgFormPrinter->setInputTypeHook('googlemap', 'sgmInputHTML', array());
		// for backwards compatibility
		$sfgFormPrinter->setInputTypeHook('coordinatesmap', 'sgmInputHTML', array());
	}

	include_once('SGM_QueryPrinter.php');
	// global variable introduced in SMW 1.2.2
	global $smwgResultFormats;
	if (isset($smwgResultFormats))
		$smwgResultFormats['googlemap'] = 'SGMResultPrinter';
	else
		SMWQueryProcessor::$formats['googlemap'] = 'SGMResultPrinter';

}

function sgmFunctionMagic( &$magicWords, $langCode ) {
	# Add the magic word
	# The first array element is case sensitive, in this case it is not case sensitive
	# All remaining elements are synonyms for our parser function
	$magicWords['semantic_google_map'] = array( 0, 'semantic_google_map' );
	// for backwards compatibility
	$magicWords['insert_map'] = array( 0, 'semantic_google_map' );
	# unless we return true, other parser functions extensions won't get loaded.
	return true;
}

function sgmFunctionRender( &$parser, $coordinates = '1,1', $zoom = '14', $type = 'G_NORMAL_MAP', $control_class = 'GSmallMapControl', $class = 'pmap', $width = '200', $height = '200', $style = '' ) {
	# The parser function itself
	# The input parameters are wikitext with templates expanded
	# The output is not parsed as wikitext
	global $wgJsMimeType, $wgGoogleMapsKey, $wgGoogleMapsOnThisPage;

	if (!$wgGoogleMapsOnThisPage) {$wgGoogleMapsOnThisPage = 0;}
	$wgGoogleMapsOnThisPage++;

	list($lat, $lon) = sgmGetLatLon($coordinates);

	$output =<<<END
<script src="http://maps.google.com/maps?file=api&v=2&key=$wgGoogleMapsKey" type="$wgJsMimeType"></script>
<script type="text/javascript"> function createMarker(point, label) {  var marker = new GMarker(point);  GEvent.addListener(marker, 'click', function() { marker.openInfoWindowHtml(label, GInfoWindoOptions.maxWidth=100); });  return marker;  }  function addLoadEvent(func) {  var oldonload = window.onload;  if (typeof oldonload == 'function') {  window.onload = function() {  oldonload();  func();  };  } else {  window.onload = func;  }  }  window.unload = GUnload;</script>
<div id="map$wgGoogleMapsOnThisPage" class="$class" style="$style" ></div>
<script type="text/javascript"> function makeMap{$wgGoogleMapsOnThisPage}() { if (GBrowserIsCompatible()) {var map = new GMap2(document.getElementById("map{$wgGoogleMapsOnThisPage}"), {size: new GSize('$width', '$height')}); map.addControl(new {$control_class}()); map.addControl(new GMapTypeControl()); map.setCenter(new GLatLng({$lat}, {$lon}), {$zoom}, {$type}); var point = new GLatLng({$lat}, {$lon}); var marker = new GMarker(point); map.addOverlay(marker); } else { document.write('should show map'); } } addLoadEvent(makeMap{$wgGoogleMapsOnThisPage});</script>

END;

	return array( $output, 'noparse' => true, 'isHTML' => true );

}

function sgmGetLatLon($param2) {
	$coordinates = preg_split("/,/", $param2);
	if (count($coordinates) == 2) {
		$lat = sm_convert_coord($coordinates[0]);
		$lon = sm_convert_coord($coordinates[1]);
		return array($lat, $lon);
	}
	return array();
}

function sm_degree2decimal($deg_coord="") {
	$dpos=strpos($deg_coord,'°');
	$mpos=strpos($deg_coord,'.');
	$spos=strpos($deg_coord,'"');
	$mlen=(($mpos-$dpos)-1);
	$slen=(($spos-$mpos)-1);
	$direction=substr(strrev($deg_coord),0,1);
	$degrees=substr($deg_coord,0,$dpos);
	$minutes=substr($deg_coord,$dpos+1,$mlen);
	$seconds=substr($deg_coord,$mpos+1,$slen);
	$seconds=($seconds/60);
	$minutes=($minutes+$seconds);
	$minutes=($minutes/60);
	$decimal=($degrees+$minutes);
	//South latitudes and West longitudes need to return a negative result
	if (($direction=="S") or ($direction=="W")) {
		$decimal=$decimal*(-1);
	}
	return $decimal;
}

function sm_decdegree2decimal($deg_coord="") {
	$direction=substr(strrev($deg_coord),0,1);
	$decimal=floatval($deg_coord);
	if (($direction=="S") or ($direction=="W")) {
		$decimal=$decimal*(-1);
	}
	return $decimal;
}

function sm_convert_coord ($deg_coord="") {
	if (preg_match('/°/', $deg_coord)) {
		if (preg_match('/"/', $deg_coord)) {
			$deg_coord = sm_degree2decimal($deg_coord);
		} else {
			$deg_coord = sm_decdegree2decimal($deg_coord);
		}
	}
	return $deg_coord;
}

function sgmLatDecimal2Degree($decimal) {
	if ($decimal < 0) {
		return abs($decimal) . "° S";
		} else {
		return $decimal . "° N";
		}
	}

function sgmLonDecimal2Degree($decimal) {
	if ($decimal < 0) {
    return abs($decimal) . "° W";
		} else {
    return $decimal . "° E";
		}
	}

// the function that outputs the custom form html
function sgmInputHTML($coordinates, $input_name, $is_mandatory, $is_disabled, $field_args) {
	global $gTabIndex, $gDisabledText, $wgJsMimeType, $wgGoogleMapsKey, $wgGoogleMapsOnThisPage;

	// default values
	$flat = 0;
	$flon = 0;

	if ($coordinates) {
		// can show up here either as an array or a string, depending on
		// whether it came from user input or a wiki page
		if (is_array($coordinates)) {
			// todo if relevant
		} else {
			list($flat, $flon) = sgmGetLatLon($coordinates);
		}
		$zoom = '14';
	} else {
		// if there's no starting value, get the value for the map
		// center and zoom from the form input, if they exist
		if (array_key_exists('center', $field_args)) {
			list($flat, $flon) = sgmGetLatLon($field_args['center']);
			$zoom = '14';
		} else {
			$zoom = '0';
		}
		if (array_key_exists('zoom', $field_args)) {
			$zoom = $field_args['zoom'];
		}
	}
	if (!$wgGoogleMapsOnThisPage) {$wgGoogleMapsOnThisPage = 0;}
	$wgGoogleMapsOnThisPage++;
	if (array_key_exists('width', $field_args)) {
		$width = $field_args['width'];
	} else {
		$width = '200';
	}
	if (array_key_exists('height', $field_args)) {
		$height = $field_args['height'];
	} else {
		$height = '200';
	}
	$class = 'sm_map';
	if (array_key_exists('map type', $field_args)) {
		$type = $field_args['map type'];
	} else {
		$type = 'G_NORMAL_MAP';
	}
	if (array_key_exists('map control', $field_args)) {
		$control_class = $field_args['map control'];
	} else {
		$control_class = 'GSmallMapControl';
	}
	if ($flat == 0) { $lat = '50';} else {$lat = $flat;}
	if ($flon == 0) { $lon = '5';} else {$lon = $flon;}

	// input field
	$starting_coords = "";
	if ($coordinates != null && $flat != 0 && $flon != 0) {
		$deg_lat = sgmLatDecimal2Degree($flat);
		$deg_lon = sgmLonDecimal2Degree($flon);
		$starting_coords = "$deg_lat, $deg_lon";
	}
	$info_id = "info_$gTabIndex";
	$text =<<<END
	<input tabindex="$gTabIndex" id="input_$gTabIndex" name="$input_name" type="text" value="$starting_coords" size="40" $gDisabledText>
	<span id="$info_id" class="error_message"></span>

END;

	// map div
	$text .= '<div id="sm_map'.$wgGoogleMapsOnThisPage.'" class="'.$class.'"></div>';

	//geocoder html
	wfLoadExtensionMessages( 'SemanticGoogleMaps' );
	$lookup_coordinates_text = wfMsg('semanticgooglemaps_lookupcoordinates');
	$text .= <<<END
	<p>
		<input size="60" id= "geocode" name="geocode" value="" type="text">
		<a href="#" onClick="showAddress(document.forms['createbox'].geocode.value); return false">$lookup_coordinates_text</a>
	</p>
	<br />

END;

	// map javascript

	$text .= <<<END
<script src="http://maps.google.com/maps?file=api&v=2&key=$wgGoogleMapsKey" type="$wgJsMimeType"></script>
<script type="text/javascript">
function showAddress(address) {
	makeMap{$wgGoogleMapsOnThisPage}();
	if (geocoder) {
		geocoder.getLatLng(address,
			function(point) {
				if (!point) {
					alert(address + " not found");
				} else {
					map.clearOverlays()
					map.setCenter(point, 14);
					var marker = new GMarker(point);
					map.addOverlay(marker);
					document.getElementById("input_$gTabIndex").value = convertLatToDMS(point.y)+', '+convertLngToDMS(point.x);
				}
			}
		);
	}
}

function convertLatToDMS (val) {
	if (val < 0) {
		return Math.abs(val) + "° " + "S";
	} else {
		return Math.abs(val) + "° " + "N";
	}
}

function convertLngToDMS (val) {
	if (val < 0) {
		return Math.abs(val) + "° " + "W";
	} else {
		return Math.abs(val) + "° " + "E";
	}
}

function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof oldonload == 'function') {
		window.onload = function() {
			oldonload();
			func();
		};
	} else {
		window.onload = func;
	}
}

window.unload = GUnload;
</script>

END;
	$javascript_text = <<<END
<script type="text/javascript">
function makeMap{$wgGoogleMapsOnThisPage}() {
	if (GBrowserIsCompatible()) {
		window.map = new GMap2(document.getElementById("sm_map{$wgGoogleMapsOnThisPage}"), {size: new GSize('$width', '$height')});
		geocoder = new GClientGeocoder();
		map.addControl(new {$control_class}());
		map.addControl(new GMapTypeControl());
		map.setCenter(new GLatLng({$lat}, {$lon}), {$zoom}, {$type});

END;
		// show a starting marker only if a value already exists
		if ($coordinates != null) {
			$javascript_text .= <<<END
		var point = new GLatLng({$lat}, {$lon});
		var marker = new GMarker(point);
		map.addOverlay(marker);

END;
		}
		$javascript_text .= <<<END
		GEvent.addListener(map, "click",
			function(overlay, point) {
				place = null;
				if (overlay) {
					map.removeOverlay (overlay);
				} else {
					var marker = new GMarker (point);
					map.clearOverlays();
					document.getElementById("input_$gTabIndex").value = convertLatToDMS(point.y)+', '+convertLngToDMS(point.x);
					map.addOverlay(marker);
					map.panTo(point);
				}
			}
		);
	}
}
addLoadEvent(makeMap{$wgGoogleMapsOnThisPage});
</script>

END;
	// remove all newlines, to avoid wiki parsing inserting unwanted
	// <p> tags within the Javascript
	$javascript_text = preg_replace('/\s+/m', ' ', $javascript_text);
	$text .= $javascript_text;

	$output = array($text,'');
	return $output;
}
