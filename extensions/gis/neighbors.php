<?php
/** @file
 *
 *  Create a page which link to other articles in Wikipedia which are
 *  in the neighborhood
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
	var $attr;

	function neighbors( $coor, $dist, $title ) 
	{
		$this->p = new geo_param( $coor );
		$this->d = $dist;
		if ($this->d <= 0) $this->d = 1000; /* default to 1000 km */
		$this->title = $title;
		$this->attr = $this->p->get_attr();
	}

	function show() 
	{
		global $wgOut, $wgUser, $wgContLang;

		/* No reason for robots to follow map links */
		$wgOut->setRobotpolicy( 'noindex,nofollow' );

		$wgOut->setPagetitle( "Neighbors" );

		if (($e = $this->p->get_error()) != "") {
			$wgOut->addHTML(
			       "<p>" . htmlspecialchars( $e ) . "</p>");
			$wgOut->output();
			wfErrorExit();
			return;
		}

		$wgOut->addWikiText( $this->make_output() );
	}

	function make_output()
	{
		$lat0 = $this->p->latdeg;
		$lon0 = $this->p->londeg;

		$g = new gis_database();
		$g->select_radius_m( $lat0, $lon0, $this->d * 1000,
				     $this->attr['region'], $this->attr['type'] );
		$all = array();
		$all_pos = array(); /* temporary store reqd due to sort */

		while (($x = $g->fetch_position())) {
			$id = $x->gis_id;
			$lat = ($x->gis_latitude_min+$x->gis_latitude_max)/2;
			$lon = ($x->gis_longitude_min+$x->gis_longitude_max)/2;
			$gc = new greatcircle($lat,$lon, $lat0, $lon0);
			$all[$id] = $gc->distance;
			$all_pos[$id] = array(
				 'lat' => $lat,
				 'lon' => $lon,
				 'name' => $g->get_title($id),
				 'type' => $x->gis_type,
				 'octant' => $gc->octant(),
				 'heading' => $gc->heading);
		}

		/* Sort by distance */
		asort($all, SORT_NUMERIC);
		reset($all);

		/* Generate output */
		$out .= "''List of ". count($all)
		      . " locations within approx. ".$this->d." km of ";
		if ($this->title != "") {
			$out .= $this->title . ", ";
		}
		$out .= "coordinates "
		       . $this->p->make_position($lat0,$lon0)
		       . "''<br /><hr />\r\n";

		while (list($id, $d) = each($all)) {
			$out .= $this->show_location($id, $d, $all_pos[$id]);
		}
		return $out;
	}
	
	function show_location( $id, $d, $pos )
	{
		$id = $pos->gis_id;

		$out = "'''[[".$pos['name']."]]''' ";

		$type = $pos['type'];
		if ($type != "" and $type != "unknown") {
			$out .= "(".$type .") ";
		}
		if ($d < 1000) {
			$out .= round($d)." m ";
		} elseif ($d < 10000) {
			$out .= round($d/100)/10 ." km ";
		} else {
			$d = round($d/1000);
			if (d >= 1000) {
				$m = floor($d/1000);
				$out .= $m.",";
				$d -= $m*1000;
			}
			$out .= $d." km ";
		}
		return $out . $pos['octant'] . " (bearing "
		       . round($pos['heading']) . "&deg; towards "
		       . $this->p->make_position($pos['lat'],$pos['lon'])
		       . ")<br />\r\n";
	}
}
?>
