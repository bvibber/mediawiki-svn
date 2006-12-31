<?php
/** \file
 *
 *  Support the <geo> extension, see also:
 *  http://en.wikipedia.org/wiki/Wikipedia:WikiProject_Geographical_coordinates
 *
 *  To install, put the following in your LocalSettings.php:
 *
 *      include( "extensions/gis/geo.php" );
 *
 *  If $wgMapsourcesURL is not defined, there will not be links to the 
 *  "Map sources" page, but the geo tag will still be rendered.
 *
 *  To add the points to a database, see the gis/geodb extension
 *
 *  \todo Translations
 *  \todo Various FIXMEs
 *
 *  ----------------------------------------------------------------------
 *
 *  Copyright 2005, Egil Kvaleberg <egil@kvaleberg.no>
 *  Copyright 2006, Jens Frank <jeluf@wikimedia.org>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( !defined( 'MEDIAWIKI' ) ) {
        echo "Geo extension\n";
        exit( 1 ) ;
}


$wgExtensionFunctions[] = "wfGeoExtension";

/**
 *  Installer
 */
function wfGeoExtension () {
	global $wgParser ;
	$wgParser->setHook ( 'geo' , 'ParseGeo' ) ;
}

if ( !function_exists( 'extAddSpecialPage' ) ) {
        require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/Specialgeo_body.php', 'Geo', 'GeoPage' );

$wgExtensionCredits['specialpage'][] = array(
        'name' => 'Geo',
        'description' => 'Enables rich geotagging functionality',
        'author' => 'Egil Kvaleberg, Jens Frank'
);

global $wgAutoloadClasses;
$wgAutoloadClasses['GeoParam'] = dirname(__FILE__) . '/GeoParam.php';


/**
 *  Called whenever a <geo> needs to be parsed
 *
 *  Return markup, but also a pointer to Map sources
 */
function ParseGeo ( $text, $params, &$parser ) {
	global $wgUser;

	$geo = new GeoParam( $text );

	if (($e = $geo->get_error()) != "") {
		return "(".$e.")";
	}

	# support Internet Geo headers http://geotags.com/geo/geotags2.html
	global $wgOut;
	$wgOut->addMeta( "geo.position", $geo->latdeg.";".$geo->londeg );

	if ( !isset( $geo->title ) || $geo->title == "") {
		$geo->title = $parser->mTitle->getDBkey();
	}
	$geo->title = str_replace(' ', '_', $geo->title );
	if ($geo->title != "" && $geo->title != " ") {
		$wgOut->addMeta( "geo.placename", $geo->title );
	}
	$attr = $geo->get_attr();
	if ( isset( $attr['region'] ) && ($r = $attr['region'])) {
		$wgOut->addMeta( "geo.region", $r);
	}

	$skin = $wgUser->getSkin();

	// !JF1 Replace Special: by NS call.
	return $skin->makeKnownLink( 'Special:Geo', $geo->get_markup(), $geo->get_param_string() );

	// !JF1
	/*
	global $wgMapsourcesURL;
	if ( isset ( $wgMapsourcesURL ) ) {
		return '<a href="'
			. $wgMapsourcesURL . "?geo=" . urlencode($text)
			. '&title=' . $geo->title
			. '">'
			. $geo->get_markup()
			. '</a>';
	} else {
		# Mapsources extension not present, so just do the markup
		return $geo->get_markup();
	}
	*/
}

?>
