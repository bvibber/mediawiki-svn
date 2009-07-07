<?php
/** 
* 
* @file
* 
* @description
* OpenStreetMap SlippyMap - MediaWiki extension
*
* This defines what happens when <slippymap> tag is placed in the wikitext
*
* We show a map based on the lat/lon/zoom data passed in. This extension brings in
* the OpenLayers javascript, to show a slippy map.
*
* Usage example:
* <slippymap lat="51.485" lon="-0.15" z="11" w="300" h="200" layer="osmarender" marker="0" />
*
* Tile images are not cached local to the wiki.
* To acheive this (remove the OSM dependency) you might set up a squid proxy,
* and modify the requests URLs here accordingly.
*
* This file should be placed in the mediawiki 'extensions' directory
* ...and then it needs to be 'included' within LocalSettings.php
*
* @license
* 
* Copyright 2008 Harry Wood, Jens Frank, Grant Slater, Raymond Spekking and others
*
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
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
*/

class SlippyMap {

	var $mapId;
	var $lat;
	var $lon;
	var $width;
	var $height;
	var $mode;
	var $layer;
	var $zoom;
	var $caption;
	var $scale;
	var $resolution;
	var $resolutions;
	var $scales;
	
	/**
	 * minimum_longitude, minimum_latitude, maximum_longitude, maximum_latitude
	 */
	var $bounds;
	
	/*
	 * Constants
	 * Using static members instead of constants because arrays can't be const
	 */	
	protected static $DEFAULT_IMAGE_FORMAT = "png";
	
	/**
	 * Supported map modes and their layer types
	 */
	public static $MAP_MODES = array( 
		"mapnik", "osmarender", "maplint", "cycle"
	);
	
	protected static $MAP_OPTIONS = array ( 
		'staticRenderService' => 'http://tile.openstreetmap.org/cgi-bin/export?bbox=##bbox##&scale=##scale##&format=##format##',
		//'staticRenderService' => null,
		'defaultZoomLevel' => 14,
		
		/* from OpenLayers XYZ.js */
		'defaultLayer' => 'mapnik',
		'numZoomLevels' => 19,
		'maxResolution' => 156543.0339,
		'unit' => 'm',
		'sphericalMercator' => true
	);
	
	/** borrowed from OpenLayers.DOTS_PER_UNIT */
	protected static $INCHES_PER_UNIT = array (
	   	'dd' => 4374754,
	   	'm' => 39.3701
	);
    
	/** borrowed from OpenLayers.DOTS_PER_INCH */
	protected static $DOTS_PER_INCH = 72;
    
	/** pixel size in meters */
	protected static $PIXEL_SIZE = 0.00028;
        
	public function __construct( $mapParams ) {
		global $wgOut, $wgUser, $wgThumbLimits;
		wfLoadExtensionMessages( 'SlippyMap' );

		$this->errors = $this->validate();
		if ( ! $this->errors ) 
			return false;
		
		self::initMap( $mapParams );	
		self::setSize();
		self::setZoom();		
		self::initResolutionsAndScales();
		self::setBounds();
	}

	public function initMap( $mapParams ) {
		$this->mapId = $mapParams['mapId'];
		$this->lat = $mapParams['lat'];
		$this->lon = $mapParams['lon'];
		$this->width = $mapParams['w'];
		$this->height = $mapParams['h'];
		$this->mode = $mapParams['mode'];
		$this->zoom = $mapParams['zoom'];
		$this->caption = $mapParams['caption'];
		$this->layer = isset( $mapParams['layer'] ) ? strtolower($mapParams['layer']) : SlippyMap::$MAP_OPTIONS['defaultLayer'];
		$this->marker = ($mapParams['marker'] != '' && $mapParams['marker'] != '0' ? 1 : 0);

		// see if the 'z' paramater is used rather than 'zoom' (and allow it)
		if ( $this->zoom == '' && isset( $mapParams['z'] ) ) {
			$this->zoom = $mapParams['z'];
		}
	}

	public function getMap() {
		global $wgOut, $wgJsMimeType;

		$mapCode = <<<EOT
			<script type="{$wgJsMimeType}">slippymaps.push(new slippymap_map({$this->mapId}, {
				mode: '{$this->mode}',
				layer: '{$this->layer}',
				lat: {$this->lat},
				lon: {$this->lon},
				zoom: {$this->zoom},
				width: {$this->width},
				height: {$this->height},
				marker: {$this->marker}
			}));</script>
EOT;

		// TODO: NOSCRIPT support
		if ( isset( SlippyMap::$MAP_OPTIONS['staticRenderService'] ) ) {
			$mapCode .= self::getStaticMap();
		} else {
			$mapCode .= self::getDynamicMap();
		}
		return $mapCode;
	}

	public function getDynamicMap() {
		$mapCode .= <<<EOT
			 <div class="mapframe" style="width:{$this->width}px">
				<div id="map{$this->mapId}" class="map" style="width:{$this->width}px; height:{$this->height}px;">
					<script type="{$wgJsMimeType}">slippymaps[{$this->mapId}].init();</script>
				</div>
EOT;
		if ( $this->caption ) {
			$mapcode .= <<<EOT
			       <div class="mapcaption">{$this->caption}</div>
EOT;
		}

		$mapCode .= <<<EOT
		</div>
EOT;
		return $mapCode;
	}

	public function getStaticMap() {
			$clickToActivate = wfMsg('slippymap_clicktoactivate');
			$staticmap = <<<EOT
				<div class="mapframe" style="width:{$this->width}px;">
					<div id="map{$this->mapId}" class="map" style="width:{$this->width}px; height:{$this->height}px;">
						<!-- Static preview -->
						<img
							id="mapPreview{$this->mapId}"
							class="mapPreview"
							src="{$this->getImgSrc()}"
							onclick="slippymaps[{$this->mapId}].init();"
							width="{$this->width}"
							height="{$this->height}"
							alt="Slippy Map"
							title="{$clickToActivate}"/>
					</div>
				</div>
EOT;

			if ( $this->caption ) {
				$staticmap .= '<div class="mapcaption">{$this->caption}</div>';
				$staticmap .= '</div>';
			}

			return $staticmap;
	}

	public function setZoom( $zoom = null ) {
		global $wgOut;
		if ( $this->zoom == '' ) $this->zoom = SlippyMap::$MAP_OPTIONS['defaultZoomLevel'];
	}
			
	public function setSize( $w = null, $h = null ) {
		global $wgUser, $wgOut, $wgThumbLimits;	
		// set thumbsize based on user preferences
		$thumbsize = $wgThumbLimits[$wgUser->getOption( 'thumbsize' )];

		// default values (meaning these parameters can be missed out)
		if ( $this->width == '' ) $this->width = $thumbsize;
		if ( $this->height == '' ) $this->height = round( $thumbsize * .72 );

		// trim off the 'px' on the end of pixel measurement numbers (ignore if present)
		if ( substr( $this->width, -2 ) == 'px' )
			$this->width = (int) substr( $this->width, 0, -2 );

		if ( substr( $this->height, - 2 ) == 'px' )
			$this->height = (int) substr( $this->height, 0, -2 );

	}

	public function setBounds() {

		/* Determine scale and map bounds for static render request */
		$this->resolution = $this->resolutions[round( $this->zoom )];
		$this->scale = self::getScaleFromResolution( $this->resolution );

		/*
		 * Calculate width for Mapnik output using a standard pixel size of 0.00028m
		 * @see http://trac.mapnik.org/wiki/ScaleAndPpi
		 */
		$w_deg = $this->width * SlippyMap::$PIXEL_SIZE * $this->scale;
		$h_deg = $this->height * SlippyMap::$PIXEL_SIZE * $this->scale;

		$center = array( $this->lon, $this->lat );
		if ( SlippyMap::$MAP_OPTIONS['sphericalMercator'] ) {
			// Calculate bounds within a spherical mercator projection if that is what the scale is based on
			$mercatorCenter = SlippyMap::forwardMercator( $center );
			$mbounds = array( 
				$mercatorCenter[0] - $w_deg / 2, 
				$mercatorCenter[1] - $h_deg / 2, 
				$mercatorCenter[0] + $w_deg / 2, 
				$mercatorCenter[1] + $h_deg / 2 
			);
			$this->bounds = SlippyMap::inverseMercator( $mbounds );
		}
		else {
			// Calculate bounds within WGS84
			$this->bounds = array( $center[0] - $w_deg / 2, $center[1] - $h_deg / 2, $center[0] + $w_deg / 2, $center[1] + $h_deg / 2 );
		}
	}
	
	public function getImgSrc() {
		if ( isset( SlippyMap::$MAP_OPTIONS['staticRenderService'] ) ) {
			return $this->parameterize( SlippyMap::$MAP_OPTIONS['staticRenderService'], 
					array( 'bbox' => implode( ',', $this->bounds ),
					  'scale' => $this->scale,
					  'lat' => $this->lat,
					  'long' => $this->lon,
					  'z' => $this->zoom,
					  'w' => $this->width,
					  'h' => $this->height,
					  'format' => SlippyMap::$DEFAULT_IMAGE_FORMAT 
				)
			);
		}
	}
	
	/**
	 * Substitutes parameters in a template using double hash sign delimiters, for example: 
	 * 
	 * <code>
	 *      <tag xml:lang="##lang##"/>
	 * </code>
	 * 
	 * A corresponding parameter "lang" may then be used to provide a replacement that is
	 * transparently substituted.
	 *
	 * @param string $template	The template text
	 * @param array $params		A map with parameter names as keys and their replacements as values
	 * @return string	Parameterized text
	 */
	protected static function parameterize( $template, $params ) {
		foreach ( $params as $key => $value ) {
			$template = str_replace( "##{$key}##", $value, $template );
		}
		return $template;
	}
	
	/**
	* Borrowed from OpenLayers.Util.getScaleFromResolution
	*/
	protected function getScaleFromResolution( $resolution ) {
		return $resolution * SlippyMap::$INCHES_PER_UNIT[SlippyMap::$MAP_OPTIONS['unit']] * SlippyMap::$DOTS_PER_INCH;
	}
    
	/**
	* Determines resolutions and scales based on a maximum resolution and number of zoom levels
	* Borrowed from OpenLayers.Layer.initResolutions
	*/
	protected function initResolutionsAndScales() {
       		$this->resolutions = array();
	    	$base = 2;
		
    		for ( $i = 0; $i < SlippyMap::$MAP_OPTIONS['numZoomLevels']; $i++ ) {
    			$this->resolutions[$i] = SlippyMap::$MAP_OPTIONS['maxResolution'] / pow( $base, $i );
			$this->scales[$i] = $this->getScaleFromResolution( $this->resolutions[$i] );
		}
	}
    
	/**
	 * Convert from WGS84 to spherical mercator
	 */
	protected static function forwardMercator($lonlat) {
		for ($i=0; $i<count($lonlat); $i+=2) {
			/* lon */
			$lonlat[$i] = $lonlat[$i] * (2 * M_PI * 6378137 / 2.0) / 180.0;
			
			/* lat */
			$lonlat[$i+1] = log(tan((90 + $lonlat[$i+1]) * M_PI / 360.0)) / (M_PI / 180.0);
			$lonlat[$i+1] = $lonlat[$i+1] * (2 * M_PI * 6378137 / 2.0) / 180.0;
		}		
		return $lonlat;
	}
	
	/**
	 * Convert from spherical mercator to WGS84
	 */
	protected static function inverseMercator($lonlat) {
		for ($i=0; $i<count($lonlat); $i+=2) {
			/* lon */
			$lonlat[$i] = $lonlat[$i] / ((2 * M_PI * 6378137 / 2.0) / 180.0);
			
			/* lat */
			$lonlat[$i+1] = $lonlat[$i+1] / ((2 * M_PI * 6378137 / 2.0) / 180.0);
			$lonlat[$i+1] = 180.0 / M_PI * (2 * atan(exp($lonlat[$i+1] * M_PI / 180.0)) - M_PI / 2);
		}		
		
		return $lonlat;
	}

	public function validMode( $mode ) {
		global $wgMapModes;
		$this->errors = '';
		
		if ( ! in_array( $mode, $wgMapModes ) ) {
			$this->errors .= wfMsg( 'slippymap_invalidmode',  htmlspecialchars( $this->mode ) );
			return false;
		}

		return true;
	}
	
	public function validate() {
		wfLoadExtensionMessages( 'SlippyMap' );
		$errors = null;
		
		if ( $this->lat == '' ) $errors .= wfMsg( 'slippymap_latmissing' ) . ' ';
		if ( $this->lon == '' ) $errors .= wfMsg( 'slippymap_lonmissing' ) . ' ';
		if ( $this->zoom == '' ) $errors .= wfMsg( 'slippymap_zoommissing' ) . ' ';

		if ( $errors == '' ) {
			// no errors so far. Now check the values
			if ( !is_numeric( $this->width ) ) {
				$errors = wfMsg( 'slippymap_widthnan', $this->width );
			} else if ( !is_numeric( $this->height ) ) {
				$errors = wfMsg( 'slippymap_heightnan', $this->height );
			} else if ( !is_numeric( $this->zoom ) ) {
				$errors = wfMsg( 'slippymap_zoomnan', $this->zoom );
			} else if ( !is_numeric( $this->lat ) ) {
				$errors = wfMsg( 'slippymap_latnan', $this->lat );
			} else if ( !is_numeric( $this->lon ) ) {
				$errors = wfMsg( 'slippymap_lonnan', $this->lon );
			} else if ( $this->width > 1000 ) {
				$errors = wfMsg( 'slippymap_widthbig' );
			} else if ( $this->width < 100 ) {
				$errors = wfMsg( 'slippymap_widthsmall' );
			} else if ( $this->height > 1000 ) {
				$errors = wfMsg( 'slippymap_heightbig' );
			} else if ( $this->height < 100 ) {
				$errors = wfMsg( 'slippymap_heightsmall' );
			} else if ( $this->lat > 90 ) {
				$errors = wfMsg( 'slippymap_latbig' );
			} else if ( $this->lat < -90 ) {
				$errors = wfMsg( 'slippymap_latsmall' );
			} else if ( $this->lon > 180 ) {
				$errors = wfMsg( 'slippymap_lonbig' );
			} else if ( $this->lon < -180 ) {
				$errors = wfMsg( 'slippymap_lonsmall' );
			} else if ( $this->zoom < 0 ) {
				$errors = wfMsg( 'slippymap_zoomsmall' );
			} else if ( $this->zoom == 18 ) {
				$errors = wfMsg( 'slippymap_zoom18' );
			} else if ( $this->zoom > 17 ) {
				$errors = wfMsg( 'slippymap_zoombig' );
			}
		}
	
		return $errors;
	}
	
	public function getErrors() {
		if ( $this->errors != "" ) {
			// Something was wrong. Spew the error message and input text.
			$output  = '';
			$output .= "<span class=\"error\">" . wfMsg( 'slippymap_maperror' ) . ' ' . $this->errors . "</span><br />";
			$output .= htmlspecialchars( $input );
			return $output;
		}
	}

	public function getMapId() {
		return $this->mapId;
	}

	public function getLat() {
		return $this->lat;
	}

	public function getLon() {
		return $this->lon;
	}

	public function getWidth() {
		return $this->width;
	}

	public function getHeight() {
		return $this->height;
	}

	public function getMode() {
		return $this->mode;
	}

	public function getZoom() {
		return $this->zoom;
	}

	public function getLayer() {
		return $this->layer;
	}
}
