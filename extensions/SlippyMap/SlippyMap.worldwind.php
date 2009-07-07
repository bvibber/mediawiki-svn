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

class WorldWind extends SlippyMap {

	/**
	 * Supported map modes and their layer types
	 */
	public static $MAP_MODES = array( 
				"urban", "landsat", "bluemarble"
			 	);
	
	protected static $MAP_OPTIONS = array ( 
		'staticRenderService' => null,
		'defaultZoomLevel' => 14,
		
		/* from OpenLayers XYZ.js */
		'defaultLayer' => 'landsat',
		'numZoomLevels' => 19,
		'maxResolution' => 156543.0339,
		'unit' => 'm',
		'sphericalMercator' => true
	);
	
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
		$this->layer = isset( $mapParams['layer'] ) ? strtolower($mapParams['layer']) : WorldWind::$MAP_OPTIONS['defaultLayer'];
		$this->marker = ($mapParams['marker'] != '' && $mapParams['marker'] != '0' ? 1 : 0);

		// see if the 'z' paramater is used rather than 'zoom' (and allow it)
		if ( $this->zoom == '' && isset( $mapParams['z'] ) ) {
			$this->zoom = $mapParams['z'];
		}
	}
	
	public function getMap() {
		global $wgOut, $wgJsMimeType;

		$mapCode .= <<<EOT
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

		$mapCode .= self::getDynamicMap();
		
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

	public function setZoom( $zoom = null ) {
		global $wgOut;
		if ( $this->zoom == '' ) $this->zoom = WorldWind::$MAP_OPTIONS['defaultZoomLevel'];
	}
}
