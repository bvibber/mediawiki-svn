<?php

/**
 * This groupe contains all Google Maps related files of the UK Geocoding for Maps extension.
 * 
 * @ingroup UKGeocodingForMaps
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$egMapsServices['googlemaps2']['pf']['display_uk_point'] = array('class' => 'UKGGoogleMapsDispUkPoint', 'file' => 'UKGeocodingForMaps/GoogleMaps/UKG_GoogleMapsDispUkPoint.php');