<?php
/** \file
 *
 *  Create a page which link to other map resources by adding the facility
 *  to embed coordinates in the URLs of these map resources according to
 *  various rules. See also
 *  http://en.wikipedia.org/wiki/Wikipedia:WikiProject_Geographical_coordinates
 *
 *  The displayed page is based on "Wikipedia:Map sources" (or similar)
 *
 *  \todo Translations
 *
 *  ----------------------------------------------------------------------
 *
 *  Copyright 2005, Egil Kvaleberg <egil@kvaleberg.no>
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

include_once ( "gissettings.php" ) ;

# if ( isset ( $wikibasedir ) )
#       {
#       require_once( "{$wikibasedir}/extensions/gis/geo.php" );
#       }
require_once( 'transversemercator.php' );

/**
 *  Base class
 */
class map_sources {
	var $p;
	var $mapsources;

	function map_sources( $coor, $title ) {
		$this->p = new geo_param( $coor, $title );
		$this->p->title = $title;

		$this->mapsources = "Map sources";
		# FIXME: translate via wfMsg( "mapsources" )
	}

	function show() {
		global $wgOut;

		/* No reason for robots to follow map links */
		$wgOut->setRobotpolicy( 'noindex,nofollow' );

		$wgOut->setPagetitle( $this->mapsources );
		$this->showList();
	}
	
	function showList() {
		global $wgOut, $wgUser, $wgContLang;

		if (($e = $this->p->get_error()) != "") {
			$wgOut->addHTML(
			       "<p>" . htmlspecialchars( $e ) . "</p>");
			$wgOut->output();
			wfErrorExit();
			return;
		}

		$attr = $this->p->get_attr();

		#
		#  Default scale
		#
		if ($attr['scale'] <= 0) {
			if ($attr['default'] == 0) {
				$default_scale = array(
				'country'   =>  10000000, # 10 mill
				'state'     =>   3000000, # 3 mill
				'adm1st'    =>   1000000, # 1 mill
				'adm2nd'    =>    300000, # 300 thousand
				'city'      =>    100000, # 100 thousand
				'mountain'  =>    100000, # 100 thousand
				'isle'      =>    100000, # 100 thousand
				'airport'   =>     30000, # 30 thousand
				'landmark'  =>     10000  # 10 thousand
				);

				$attr['default'] = 
						$default_scale[$attr['type']];
				# FIXME: Scale according to city size, if available
			}
			if ($attr['default'] == 0) {
				/* No type and no default, make an assumption */
				$attr['default'] = 300000;
			}
			$attr['scale'] = $attr['default'];
		}

		/*
		 *  Convert coordinates to various Transverse Mercator forms
		 */

		/* standard UTM */
		$utm = new transversemercator();
		$utm->LatLon2UTM( $this->p->latdeg, $this->p->londeg );
		$utm->Zone = $utm->LatLon2UTMZone( $this->p->latdeg, $this->p->londeg );

		/* fixed UTM as used by iNatur */
		$utm33 = new transversemercator();
		$utm33->LatLonZone2UTM( $this->p->latdeg, $this->p->londeg, "33V" );

		/*  UK National Grid, see http://www.gps.gov.uk/guide7.asp
		 *  central meridian 47N 2W, offset 100km N 400km W */
		$osgb36 = new transversemercator();
		$osgb36ref = $osgb36->LatLon2OSGB36( $this->p->latdeg, $this->p->londeg );

		/* Swiss traditional national grid */
		$ch1903 = new transversemercator();
		$ch1903->LatLon2CH1903( $this->p->latdeg, $this->p->londeg );

		/*
		 *  Mapquest style zoom
		 *  9 is approx 1:6,000
		 *  5 (default) is approx 1:333,000
		 *  2 is approx 1:8,570,000
		 *  0 is minimum
		 */
		if ($attr['scale'] > 0) {
			$zoom = intval(18.0 - log($attr['scale']));
		} else {
			$zoom = 9;
		}
		if ($zoom < 0) $zoom = 0;
		if ($zoom > 9) $zoom = 9;

		/*
		 *  MSN uses an altitude equivalent
		 *  instead of a scale: 
		 *  143 == 1:1000000 scale
		 */
		$altitude = intval( $attr['scale'] * 143/1000000 );
		if ($altitude < 1) $altitude = 1;

		/*
		 * Tiger and Google uses a span
		 * FIXME calibration
		 * 1.0 for 1:1000000
		 */
		$span = $attr['scale'] * 1.0 / 1000000;

		/*
		 * Multimap has a fixed set of scales
		 * and will choke unless one of them are specified
		 */
		if     ($attr['scale'] >= 30000000) $mmscale = 40000000;
		elseif ($attr['scale'] >= 14000000) $mmscale = 20000000;
		elseif ($attr['scale'] >= 6300000)  $mmscale = 10000000;
		elseif ($attr['scale'] >= 2800000)  $mmscale =  4000000;
		elseif ($attr['scale'] >= 1400000)  $mmscale =  2000000;
		elseif ($attr['scale'] >= 700000)   $mmscale =  1000000;
		elseif ($attr['scale'] >= 310000)   $mmscale =   500000;
		elseif ($attr['scale'] >= 140000)   $mmscale =   200000;
		elseif ($attr['scale'] >=  70000)   $mmscale =   100000;
		elseif ($attr['scale'] >=  35000)   $mmscale =    50000;
		elseif ($attr['scale'] >=  15000)   $mmscale =    25000;
		elseif ($attr['scale'] >=   7000)   $mmscale =    10000;
		else                                $mmscale =     5000;

		/*
		 *  Make minutes and seconds, and round
		 */
		$lat = $this->p->make_minsec($this->p->latdeg);
		$lon = $this->p->make_minsec($this->p->londeg);

		/*
		 *  Hack for negative, small degrees
		 */
		$latdegint = intval($lat['deg']);
		$londegint = intval($lon['deg']);
		if ($this->p->latdeg < 0 and $latdegint == 0) {
			$latdegint = "-0";
		}
		if ($this->p->londeg < 0 and $londegint == 0) {
			$londegint = "-0";
		}

		/*
		 *  Look up page from Wikipedia
		 *  See if we have something in
		 *  [[Wikipedia:Map sources]] or equivalent.
		 *  A subpage can be specified
		 */
		$src = $this->mapsources;
		$region = "";
		if ($attr['page'] != "") {
		    $src .= "/" . $attr['page']; # subpage specified
		} elseif ($attr['region'] != "") {
		    $region = strtoupper(substr($attr['region'],0,2));
		    $region = "/" . $region; # subpage specified
		}
		$bstitle = Title::makeTitleSafe( NS_PROJECT, $src.$region );
		$bsarticle = new Article( $bstitle );

		if (($region != "")
		 and ($bsarticle->getID() == 0)) {
			/* Region article does not exist, and was a subpage
			 * Default to main page  */
			$bstitle = Title::makeTitleSafe( NS_PROJECT, $src );
			$bsarticle = new Article( $bstitle );
		}
		if ($bsarticle->getID() == 0) {
			$wgOut->addHTML( 
			       "<p>".
			       htmlspecialchars( "Please add this page: ".
						  $bstitle->getText() ));
			$wgOut->output();
			wfErrorExit();
			return;
		}
		$bstext = $bsarticle->getContent( false ); # allow redir

		/*
		 * Replace in page
		 */
		$out = str_replace( "{latdegdec}",       $lat['deg'],
		       str_replace( "{londegdec}",       $lon['deg'],
		       str_replace( "{londegneg}",       -$lon['deg'],
		       str_replace( "{latdegint}",       $latdegint,
		       str_replace( "{londegint}",       $londegint,
		       str_replace( "{latdegabs}",       abs(intval($lat['deg'])),
		       str_replace( "{londegabs}",       abs(intval($lon['deg'])),
		       str_replace( "{latmindec}",       $lat['min'],
		       str_replace( "{lonmindec}",       $lon['min'],
		       str_replace( "{latminint}",       intval($lat['min']),
		       str_replace( "{lonminint}",       intval($lon['min']),
		       str_replace( "{latsecdec}",       $lat['sec'],
		       str_replace( "{lonsecdec}",       $lon['sec'],
		       str_replace( "{latsecint}",       intval($lat['sec']),
		       str_replace( "{lonsecint}",       intval($lon['sec']),
		       str_replace( "{latNS}",           $lat['NS'],
		       str_replace( "{lonEW}",           $lon['EW'],
		       str_replace( "{utmzone}",         $utm->Zone,
		       str_replace( "{utmnorthing}",     round($utm->Northing),
		       str_replace( "{utmeasting}",      round($utm->Easting),
		       str_replace( "{utm33northing}",   round($utm33->Northing),
		       str_replace( "{utm33easting}",    round($utm33->Easting),
		       str_replace( "{osgb36ref}",       $osgb36ref,
		       str_replace( "{osgb36northing}",  round($osgb36->Northing),
		       str_replace( "{osgb36easting}",   round($osgb36->Easting),
		       str_replace( "{ch1903northing}",  round($ch1903->Northing),
		       str_replace( "{ch1903easting}",   round($ch1903->Easting),
		       str_replace( "{scale}",           $attr['scale'],
		       str_replace( "{mmscale}",         $mmscale,
		       str_replace( "{altitude}",        $altitude,
		       str_replace( "{zoom}",            $zoom,
		       str_replace( "{span}",            $span,
		       str_replace( "{type}",            $attr['type'],
		       str_replace( "{region}",          $attr['region'],
		       str_replace( "{page}",            $attr['page'],
		       $bstext )))))))))))))))))))))))))))))))))));
		$wgOut->addWikiText( $out );
	}
}

?>
