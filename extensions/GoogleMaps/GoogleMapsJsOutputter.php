<?php

class GoogleMapsJsOutputter {
    var $mEnablePaths;
    var $mOutput;
    var $mLanguage;
    var $mProxyKey;

    function GoogleMapsJsOutputter(&$pLanguage, &$pProxyKey, $pEnablePaths = true) {
        $this->mOutput = '';
        $this->mLanguage =& $pLanguage;
        $this->mProxyKey =& $pProxyKey;
        $this->mEnablePaths = $pEnablePaths;
    }

    function addXmlSource($url) {
        $url = addslashes($url);
        $this->mOutput .= <<<JAVASCRIPT
      geoxml = new GGeoXml("{$url}");
      map.addOverlay(geoxml);
JAVASCRIPT;
    }

    /**
     * Adds a polyline or polygon to the google map.
     *
     * @param $pPolyline [string] - an array of lat/lon associative arrays
     * @param $pColor string - the hex code for the color of the polyline
     * @param $pStroke integer - the thickness, in pixels, of the polyline
     * @param $pFillColor string - the hex code for the fill color (optional)
     *
     * @return string - the javascript string for adding the polyline to the map
     **/
    function addPolyline ( $pPolyline, $pLineColor, $pLineOpacity, $pStroke, $pFillColor, $pFillOpacity )
    {
        // get a hex code and transparency for the color
        if (count($pPolyline) == 0) {
            return;
        }
        if (!$this->mEnablePaths) {
            return;
        }
        $polyline = array();
        foreach ($pPolyline as $p) {
            $polyline[] = "new GLatLng({$p['lat']}, {$p['lon']})";
        }
        $lineOpacity = GoogleMaps::hex2fraction( $pLineOpacity );

        if ( isset( $pFillColor ) ) {
            $fillOpacity = GoogleMaps::hex2fraction( $pFillOpacity );
            $polyline[] = $polyline[0];
            $this->mOutput .= "  map.addOverlay(new GPolygon( [ " . implode( ", ", $polyline )." ], ".
                "'#{$pLineColor}', {$pStroke} , {$lineOpacity}, ".
                "'#{$pFillColor}', {$fillOpacity}, {'clickable': false})); ";
        } else {
            // build the javascript for adding the polyline
            $this->mOutput .= "  map.addOverlay(new GPolyline( [ " . implode( ", ", $polyline )." ], ".
                "'#{$pLineColor}', {$pStroke}, {$lineOpacity}, {'clickable': false})); ";
        }
    }

    /**
     * Creates a new marker on the google map.
     *
     * @param $pLat numeric - the latitude for the marker
     * @param $pLon numeric - the longitude of the marker
     * @param $pIcon string - the identifier of the icon to use
     * @param $pTitle string - the title for the marker
     * @param $pCaption string - the caption for the marker
     * @param $pColor string - the color of the polyline (really just used to know if we're in
     *   a polyline or not and should draw markers without captions)
     * @param $pTabs [string] - array of tabs for the marker formatted as javascript for constructing
     *   GInfoWindowTab objects
     *
     * @return string - the javascript for adding the tabs to the map
     **/

    function addMarker ( $pLat, $pLon, $pIcon, $pTitle, $pCaption, $pIsLine ) {
        $title = GoogleMaps::fixStringDirection($pTitle, $this->mLanguage->isRTL());

        if (is_string($pCaption)) {
            $caption = preg_replace('/[\r\n]+/', '', $pCaption);
        } else if (is_array($pCaption)) {
            foreach ($pCaption as $tab) {
                $tab['gm-caption'] = preg_replace('/[\r\n]+/', '', $tab['gm-caption']);
            }
        }
        $options = array();
        // choose the appropriate icon for the marker
        $options['icon']      = $pIcon ? "mapIcons['{$pIcon}']" : "GME_DEFAULT_ICON";
        $options['clickable'] = $title || (is_string($pCaption) ? $caption : count($pCaption)) ? "true" : "false";

        if (!($pIsLine && $options['clickable'] == "false")) {
            $this->mOutput .= " marker = new GMarker(new GLatLng({$pLat}, {$pLon}), { ";
            if ($title) {
                $this->mOutput .= " 'title': '".addslashes($title)."', ";
            }
            $this->mOutput .= " 'icon': {$options['icon']}, ";
            $this->mOutput .= " 'clickable': {$options['clickable']} ";
            $this->mOutput .= "});";
        }

        if (is_string($pCaption)) {
            // if there's a caption, set it
            $this->mOutput .= " marker.caption = '';";
            if ($title) {
                $this->mOutput .= " marker.caption += '<p><b>" . addslashes($title) . "</b></p>';";
            }
            if( $caption ) {
                $this->mOutput .= " marker.caption += '" .
                    addslashes( GoogleMaps::fixBlockDirection(GoogleMaps::fixTidy($caption), $this->mLanguage->isRTL())) .
                    "';";
            }
        // if there's tabs add them to the marker
        } else if( is_array($pCaption) && count($pCaption) ) { // dump the tabs from the previous marker
            $tabs = array();
            foreach($pCaption as $t) {
                $tabs[] = "new GInfoWindowTab('" .
                    addslashes(GoogleMaps::fixStringDirection($t['title'], $this->mLanguage->isRTL())).
                    "', '".
                    addslashes(GoogleMaps::fixBlockDirection(GoogleMaps::fixTidy($t['gm-caption']), $this->mLanguage->isRTL())) .
                    "')";
            }
            $this->mOutput .= " marker.tabs = [ ".implode(',', $tabs)." ]; ";
        }

        // add the marker to the map
        $this->mOutput .= " map.addOverlay(marker);";
    }

    function addIcon($icon, $template) {
        $this->mOutput .= " mapIcons['{$icon}'] = new GIcon(G_DEFAULT_ICON, '" .
            addslashes( str_replace( "{label}", $icon, $template ) ) . "');";
    }

    function addHeader($o) {
        $numberOfMaps = $o['number_of_maps'];
        $incompatibleMessage = $o['incompatible_message'];
      // output the main dif with the specified height/width/direction
      $this->mOutput .= '<div id="map' . $numberOfMaps . '" style="width: ' . $o['width'].'px; height: ' . $o['height'].'px; direction: ltr;"></div>';

      $incompatibleMessage = addslashes($incompatibleMessage);
      // The Google Maps API shows polylines incorrectly in IE if the direction
      // of the Map is RTL. So for RTL languages, we set the map div to LTR,
      // then make the info balloons RTL.

      // wrap the JS block with a token which will be stripped later
      $this->mOutput .= '%%BEGINJAVASCRIPT' . $this->mProxyKey . '%%';

      // We have a JS function specific to each map on the page.
      // Note that we now have one click listener per map, rather
      // than one per marker. This speeds things up a lot.
      $this->mOutput .= <<<JAVASCRIPT
      function makeMap{$numberOfMaps}() {
      if (!GBrowserIsCompatible()) {
          document.getElementById("map{$numberOfMaps}").innerHTML = "{$incompatibleMessage}";
          return;
      }
      var map = new GMap2(document.getElementById("map{$numberOfMaps}"));
      map.addMapType(G_PHYSICAL_MAP);
      GME_DEFAULT_ICON = G_DEFAULT_ICON;
      map.setCenter(new GLatLng({$o['lat']}, {$o['lon']}), {$o['zoom']}, {$o['type']});
      GEvent.addListener(map, 'click', function(overlay, point) {
          if (overlay) {
            if (overlay.tabs) {
              overlay.openInfoWindowTabsHtml(overlay.tabs);
            } else if (overlay.caption) {
              overlay.openInfoWindowHtml(overlay.caption);
            }
          }
      });
JAVASCRIPT;

          // make gmap api calls to implement the various settings
          if( $o['zoomstyle'] == 'smooth' ) {
              $this->mOutput .= ' map.enableContinuousZoom(); ';
          }
          if( $o['doubleclick'] == 'zoom' ) {
              $this->mOutput .= ' map.enableDoubleClickZoom(); ';
          }
          if( $o['scrollwheel'] == 'zoom') {
              $this->mOutput .= ' map.enableScrollWheelZoom(); ';
          }
          if( $o['scale'] == 'yes' ) {
              $this->mOutput .= ' map.addControl(new GScaleControl()); ';
          }
          if( $o['selector'] == 'yes' ) {
              $this->mOutput .= ' map.addControl(new GHierarchicalMapTypeControl()); ';
          }
          if( $o['overview'] == 'yes' ) {
              $this->mOutput .= ' map.addControl(new GOverviewMapControl()); ';
          }
          if( $o['controls'] != 'none' ) {
              $this->mOutput .= ' map.addControl(new '.$o['controls'].'()); ';
          }
          if( $o['icon'] != 'http://www.google.com/mapfiles/marker.png' ) {
              $this->mOutput .= " GME_DEFAULT_ICON = new GIcon(G_DEFAULT_ICON, '".addslashes($o['icon'])."');";
          }
      }

    function addTrailer($options) {
        $numberOfMaps = $options['number_of_maps'];
        // reset the default icon
        $this->mOutput .= " GME_DEFAULT_ICON = G_DEFAULT_ICON;";
        // set the text direction explicitly if it's rtl
        if( $this->mLanguage->isRTL() ) {
            $this->mOutput .= " document.getElementById('map{$numberOfMaps}').style.direction = 'rtl'; ";
        }

        $this->mOutput .= '} addLoadEvent(makeMap' . $numberOfMaps . ');%%ENDJAVASCRIPT' .
            $this->mProxyKey . '%%';
    }

    // strip out the tabs and newlines
      function render() {
          return preg_replace( '/[\t\n]/', '', $this->mOutput );
      }
}
