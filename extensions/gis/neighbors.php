<?php
/** @file
 *
 *  Create a page which link to other articles in Wikipedia
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

require_once( "geo.php" );
require_once( "database.php" );
require_once( 'greatcircle.php' );

/**
 *  Base class
 */
class neighbors {
	var $p;
	var $d;
	var $title;

	function neighbors( $coor, $dist, $title ) 
	{
		$this->p = new geo_param( $coor );
		$this->d = $dist;
		if ($this->d <= 0) $this->d = 1000; /* default to 1000 km */
		$this->title = $title;
	}

	function show() 
	{
		global $wgOut;

		/* No reason for robots to follow map links */
		$wgOut->setRobotpolicy( 'noindex,nofollow' );

		$wgOut->setPagetitle( "Neighbors" );
		$this->showList();
	}
	
	function showList() 
	{
		global $wgOut, $wgUser, $wgContLang;

		if (($e = $this->p->get_error()) != "") {
			$wgOut->addHTML(
			       "<p>".
			       htmlspecialchars( $e ));
			$wgOut->output();
			wfErrorExit();
			return;
		}

		$lat0 = $this->p->latdeg;
		$lon0 = $this->p->londeg;

		$out .= "''Locations within approx. ".$this->d." km of ";
		if ($this->title != "") {
			$out .= $this->title . ", ";
		}
		$out .= "coordinates "
				. $lat0."&deg; ".$lon0
				. "&deg;''<br>";

		$g = new gis_database();
		$g->select_radius_m( $lat0, $lon0, $this->d * 1000);

		# BUG: see below
		$dbx =& wfGetDB( DB_MASTER );

		while (($x = $g->fetch_position())) {
			$id = $x->gis_id;
			$type = $x->gis_type; /* BUG: */

			if ( $type == "") $type = "unknown";

			# BUG: use classes..
			# BUG: version 1.4 is different
			$name_dbkey = $dbx->selectField( 'page',
				      'page_title',
				       array( 'page_id' => $id),
				       $fname );
			$name_text = str_replace( '_', ' ', $name_dbkey );

			$gc = new greatcircle( $x->gis_latitude_min, $x->gis_longitude_min,
						$lat0, $lon0);
			$d = $gc->distance;

			$out .= "'''[[".$name_text."]]''' ";
			if ($type != "" and $type != "unknown") {
				$out .= "(".$type .") ";
			}
			if ($d < 1000) {
				$out .= round($d)." m";
			} elseif ($d < 10000) {
				$out .= round($d/100)/10 ." km";
			} else {
				$d = round($d/1000);
				if (d >= 1000) {
					$m = floor($d/1000);
					$out .= $m.",";
					$d -= $m*1000;
				}
				$out .= $d." km";
			}
			$out .= " bearing ".round($gc->heading)."&deg; "
			       . $gc->octant()."<br>";
		}
		$wgOut->addWikiText( $out );
	}
}

?>







