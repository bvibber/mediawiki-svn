<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Print query results in a Google Map. Based on Google Maps API code
 * written by Robert Buzink and query results printing code written by
 * Markus Krötzsch.
 */

class SGMResultPrinter extends SMWResultPrinter {

	public function getResult($results, $params, $outputmode) {
		// skip checks, results with 0 entries are normal
		$this->readParameters($params, $outputmode);
		return $this->getResultText($results, $params, SMW_OUTPUT_HTML);
	}

	protected function getResultText($res, $outputmode) {
		global $smwgIQRunningNumber, $wgUser;
		$skin = $wgUser->getSkin();
		$result = "";

		$locations = array();
		// print all result rows
		while ( $row = $res->getNext() ) {
			$lat = $lon = $title = $text = "";
			foreach ($row as $field) {
				$pr = $field->getPrintRequest();
				$first = true;
				while ( ($object = $field->getNextObject()) !== false ) {
					if ($object->getTypeID() == '_geo') { // use shorter "LongText" for wikipage
						// don't add geographical coordinates to the display
					} elseif ($object->getTypeID() == '_wpg') { // use shorter "LongText" for wikipage
						$text .= $pr->getHTMLText($skin) . " " . $object->getLongText($outputmode, $skin) . "<br />";
						if ($first) {
							$title = $object->getShortWikiText(false);
						}
					} else {
						$text .= $pr->getHTMLText($skin) . " " . $object->getShortText($outputmode, $skin) . "<br />";
					}
					if ($pr->getMode() == SMWPrintRequest::PRINT_PROP && $pr->getTypeID() == '_geo') {
						list($lat,$lon) = explode(',', $object->getXSDValue());

					}
					$first = false;
				}
				if ($lat != '' && $lon != '') {
					$locations[] = array($lat, $lon, $title, $text);
				}
			}
		}


		$coordinates = '1,1';
		$class = 'pmap';
		if (array_key_exists('width', $this->m_params)) {
			$width = $this->m_params['width'];
		} else {
			$width = 400;
		}
		if (array_key_exists('height', $this->m_params)) {
			$height = $this->m_params['height'];
		} else {
			$height = 400;
		}
		if (array_key_exists('center', $this->m_params)) {
			$center = $this->m_params['center'];
		} else {
			$center = null;
		}
		if (array_key_exists('zoom', $this->m_params)) {
			$zoom = $this->m_params['zoom'];
		} else {
			$zoom = 0;
		}
		if (array_key_exists('map type', $this->m_params)) {
			$type = $this->m_params['map type'];
		} else {
			$type = 'G_NORMAL_MAP';
		}
		if (array_key_exists('map control', $this->m_params)) {
			$control_class = $this->m_params['map control'];
		} else {
			$control_class = 'GLargeMapControl';
		}
                global $wgJsMimeType, $wgGoogleMapsKey, $wgGoogleMapsOnThisPage;

                if (!$wgGoogleMapsOnThisPage) {$wgGoogleMapsOnThisPage = 0;}
                $wgGoogleMapsOnThisPage++;

                $map_text = <<<END
<script src="http://maps.google.com/maps?file=api&v=2&key=$wgGoogleMapsKey" type="$wgJsMimeType"></script>
<script type="text/javascript">
function createMarker(point, title, label) {
	var marker = new GMarker(point, {title:title});
	GEvent.addListener(marker, 'click',
		function() {
			marker.openInfoWindowHtml(label, {maxWidth:350});
		});
	return marker;
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
<div id="map$wgGoogleMapsOnThisPage" class="$class"></div>
<script type="text/javascript">
function makeMap{$wgGoogleMapsOnThisPage}() {
	if (GBrowserIsCompatible()) {
		var map = new GMap2(document.getElementById("map$wgGoogleMapsOnThisPage"), {size: new GSize('$width', '$height')});
		map.setMapType($type);
		map.addControl(new {$control_class}());
		map.addControl(new GMapTypeControl());
END;
		if (count($locations) > 0 && $center == null) {
			// get the extremes among these points to calculate
			// the correct zoom level for the map
			$min_lat = 90;
			$max_lat = -90;
			$min_lon = 180;
			$max_lon = -180;
			foreach ($locations as $i => $location) {
				list($lat, $lon) = $location;
				if ($lat < $min_lat) {$min_lat = $lat; }
				if ($lat > $max_lat) {$max_lat = $lat; }
				if ($lon < $min_lon) {$min_lon = $lon; }
				if ($lon > $max_lon) {$max_lon = $lon; }
			}
			$center_lat = ($min_lat + $max_lat) / 2;
			$center_lon = ($min_lon + $max_lon) / 2;
			$lat_width = $max_lat - $min_lat;
			$lon_width = $max_lon - $min_lon;
			// get coordinates a little beyond the farthest
			// points among this set of locations, so that
			// the zoom level doesn't set some points at
			// the very edge of the map
			$max_lat_plus = $max_lat + (0.05 * $lat_width);
			$min_lat_plus = $min_lat - (0.05 * $lat_width);
			$max_lon_plus = $max_lon + (0.05 * $lon_width);
			$min_lon_plus = $min_lon - (0.05 * $lon_width);
			$map_text .=<<<END
	var center = new GLatLng($center_lat, $center_lon);
	var sw_point = new GLatLng($min_lat_plus, $min_lon_plus);
	var ne_point = new GLatLng($max_lat_plus, $max_lon_plus);
	var bounds = new GLatLngBounds(sw_point, ne_point);
	var zoom = map.getBoundsZoomLevel(bounds);
	map.setCenter(center, zoom);

END;
		} else {
			if ($center == null) {
				$center_lat = 0;
				$center_lon = 0;
			} else {
				// GLatLng class expects only numbers, no
				// letters or degree symbols
				list($center_lat, $center_lon) = SGMUtils::getLatLon($center);
			}
			$map_text .= "	map.setCenter(new GLatLng($center_lat, $center_lon), $zoom);\n";
		}
		// add a marker to the map for each location
		foreach ($locations as $i => $location) {
			list($lat, $lon, $title, $label) = $location;
			$title = str_replace("'", "\'", $title);
			$label = str_replace("'", "\'", $label);
			$map_text .=<<<END
	map.addOverlay(createMarker(new GLatLng($lat, $lon), '$title', '$label'));
END;
		}

                $map_text .=<<<END
	}
}
addLoadEvent(makeMap{$wgGoogleMapsOnThisPage});
</script>
END;
		// to avoid wiki parsing adding random '<p>' tags, we have
		// to replace all newlines with spaces
		$map_text = preg_replace('/\s+/m', ' ', $map_text);
		$result .= $map_text;

		// print further results footer
		// getSearchLabel() method was added in SMW 1.3
		if (method_exists($this, 'getSearchLabel')) {
			$search_label = $this->getSearchLabel(SMW_OUTPUT_HTML);
		} else {
			$search_label = $this->mSearchlabel;
		}
		if ( $this->mInline && $res->hasFurtherResults() && $search_label !== '') {
			$link = $res->getQueryLink();
			$link->setCaption($search_label);
			$result .= "\t<tr class=\"smwfooter\"><td class=\"sortbottom\" colspan=\"" . $res->getColumnCount() . '"> ' . $link->getText($outputmode,$this->mLinker) . "</td></tr>\n";
		}
		return array($result, 'noparse' => 'true', 'isHTML' => 'true');
	}
}
