<?php
/** \file
 *
 *  Support the <geo> extension, see also:
 *  http://en.wikipedia.org/wiki/Wikipedia:WikiProject_Geographical_coordinates
 *
 *  To install, put the following in your LocalSettings.php:
 *
 *      $wgMapsourcesURL = "/phase3/extensions/gis/index.php?geo=";
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

$wgExtensionFunctions[] = "wfGeoExtension";

/**
 *  Installer
 */
function wfGeoExtension () {
	global $wgParser ;
	$wgParser->setHook ( "geo" , parse_geo ) ;
}

/**
 *  Called whenever a <geo> needs to be parsed
 *
 *  Return markup, but also a pointer to Map sources
 */
function parse_geo ( $text ) {

	$geo = new geo_param( $text );

	if (($e = $geo->get_error()) != "") {
		return "(".$e.")";
	}

	# support Internet Geo headers http://geotags.com/geo/geotags2.html
	global $wgOut;
	$wgOut->addMeta( "geo.position", $geo->latdeg.";".$geo->londeg );

	# FIXME: obtain title
	if (($title = $wgOut->getPageTitle())) {
		$title = str_replace($title,'_',' ');
	}
	if ($title) {
		$wgOut->addMeta( "geo.placename", $title );
	}
	$attr = $geo->get_attr();
	if (($r = $attr['region'])) {
		$wgOut->addMeta( "geo.region", $r);
	}

	global $wgMapsourcesURL;
	if ( isset ( $wgMapsourcesURL ) ) {
		return '<a href="'
			. $wgMapsourcesURL . "?geo=" . urlencode($text)
			. '&title="' . $title
			. '">'
			. $geo->get_markup()
			. '</a>';
	} else {
		# Mapsources extension not present, so just do the markup
		return $geo->get_markup();
	}
}

/**
 *   Parse geo parameters
 */
class geo_param {
	var $pieces;
	var $n_params;
	var $error;

	var $latdeg;
	var $londeg;
	var $latmin;
	var $lonmin;
	var $latsec;
	var $lonsec;

	var $latNS;
	var $lonEW;

	/**
	 *   Constructor
	 */
	function geo_param( $param ) {
		$this->pieces = explode(" ", str_replace( '_', ' ', $param ));

		if ($this->is_coor($this->pieces[1],$this->pieces[3])) {
			$this->latdeg = $this->pieces[0];
			$this->latNS  = $this->pieces[1];
			$this->londeg = $this->pieces[2];
			$this->lonEW  = $this->pieces[3];
			$this->n_params = 4;
		} elseif ($this->is_coor($this->pieces[2],$this->pieces[5])) {
			$this->latdeg = $this->pieces[0];
			$this->latmin = $this->pieces[1];
			$this->latNS  = $this->pieces[2];
			$this->londeg = $this->pieces[3];
			$this->lonmin = $this->pieces[4];
			$this->lonEW  = $this->pieces[5];
			$this->n_params = 6;

		} elseif ($this->is_coor($this->pieces[3],$this->pieces[7])) {
			$this->latdeg = $this->pieces[0];
			$this->latmin = $this->pieces[1];
			$this->latsec = $this->pieces[2];
			$this->latNS  = $this->pieces[3];
			$this->londeg = $this->pieces[4];
			$this->lonmin = $this->pieces[5];
			$this->lonsec = $this->pieces[6];
			$this->lonEW  = $this->pieces[7];
			$this->n_params = 8;
		} else {
			# BUG: support decimal, signed lat, lon
			$this->error = "Unrecognized";
		}

		if ($this->latdeg > 90 or $this->latdeg < -90
		 or $this->londeg > 180 or $this->londeg < -180
		 or $this->latmin > 60 or $this->latmin < 0
		 or $this->lonmin > 60 or $this->lonmin < 0
		 or $this->latsec > 60 or $this->latsecsec < 0
		 or $this->lonsec > 60 or $this->lonsecsec < 0) {
			$this->error = "Out of range";
		}

		if ($this->latNS == "S") {
			$this->latdeg = -$this->latdeg;
		}
		if ($this->lonEW == "W") {
			$this->londeg = -$this->londeg;
		}

		if ($this->latmin == "") {
			# Minus not stated, must be decimal degree
			$this->latmin = 60.0 * $this->frac($this->latdeg);
			$this->lonmin = 60.0 * $this->frac($this->londeg);
		} else {
			# Minus stated, make decimal degree
			# BUG: This may fail
			if ($this->latNS == "S") {
				$this->latdeg -= $this->latmin/60.0
					    + $this->lonsec/3600.0;
			} else {
				$this->latdeg += $this->latmin/60.0
					    + $this->latsec/3600.0;
			}
			# BUG: This may fail
			if ($this->lonEW == "W") {
				$this->londeg -= $this->lonmin/60.0
					    + $this->lonsec/3600.0;
			} else {
				$this->londeg += $this->lonmin/60.0
					    + $this->lonsec/3600.0;
			}
		}

		if ($this->latsec == "") {
			# Seconds not stated, must be decimal minutes
			$this->latsec = 60.0 * ($this->latmin - intval($this->latmin));
			$this->lonsec = 60.0 * ($this->lonmin - intval($this->lonmin));
		} else {
			# Seconds stated, make decimal minutes
			$this->latmin += $this->latsec/60.0;
			$this->lonmin += $this->lonsec/60.0;
		}

		/* make sure N/S E/W still is correct */
		if ( $this->latdeg > 0) $this->latNS = "N";
		elseif ( $this->latdeg < 0) $this->latNS = "S";
		if ( $this->londeg > 0) $this->lonEW = "E";
		elseif ( $this->londeg < 0) $this->lonEW = "W";

		#
		# Round to a suitable number of digits
		# FIXME: should reflect precision
		#
		$this->latdeg = round( $this->latdeg, 6);
		$this->londeg = round( $this->londeg, 6);
		$this->latmin = round( $this->latmin, 4);
		$this->lonmin = round( $this->lonmin, 4);
		$this->latsec = round( $this->latsec, 2);
		$this->lonsec = round( $this->lonsec, 2);

	}

	/**
	 *  Get the additional attributes in an associative array
	 */
	function get_attr() {
		$a = array();
		$n = $this->n_params;
		while (($s = $this->pieces[$n++ ]) != "") {  # BUG:
			if (($i = strpos($s,":")) >= 1) {
				$attr = substr($s,0,$i);
				$val = substr($s,$i+1);
				if (($j = strpos($val,"("))
				 && ($k = strpos($val,")"))
				 && ($k > $j)) {
					$a["arg:".$attr] = substr($val,$j+1,$k-($j+1));
					$val = substr($val,0,$j);
				}
				$a[$attr] = $val;
			} elseif (intval($s) > 0) {
			    if ($a['$scale'] != "")
				$a['scale'] = intval($s);
			}
		}
		return $a;
	}

	function is_coor( $ns,$ew )
	{
		return (($ns=="N" or $ns=="S") and
			($ew=="E" or $ew=="W"));
	}

	function frac( $f)
	{
		return abs($f) - abs(intval($f));
	}

	/**
	 *  Get composite position in RFC2045 format
	 */
	function get_position( )
	{
		return $this->latdeg.";".$this->londeg;
	}

	/**
	 *  Get error message that applies, or "" of all is well
	 */
	function get_error()
	{
		if ($this->error != "") {
			return "Error:".$this->error;
		}
		return "";
	}

	/**
	 *  Produce markup suitable for use in page
	 *  Use original content as much as possible
	 */
	function get_markup() 
	{
		if ($this->n_params == 4) {
			return $this->pieces[0].'&deg;&nbsp;'.
			       $this->pieces[1].' '.
			       $this->pieces[2].'&deg;&nbsp;'.
			       $this->pieces[3];

		} elseif ($this->n_params == 6) {
			return $this->pieces[0].'&deg;'.
			       $this->pieces[1].'&prime;&nbsp;'.
			       $this->pieces[2].' '.
			       $this->pieces[3].'&deg;'.
			       $this->pieces[4].'&prime;&nbsp;'.
			       $this->pieces[5];

		} elseif ($this->n_params == 8) {
			return $this->pieces[0].'&deg;'.
			       $this->pieces[1].'&prime;'.
			       $this->pieces[2].'&Prime;&nbsp;'.
			       $this->pieces[3].' '.
			       $this->pieces[4].'&deg;'.
			       $this->pieces[5].'&prime;'.
			       $this->pieces[6].'&Prime;&nbsp;'.
			       $this->pieces[7];
		} else {
			return $this->get_error();
		}
	}
}

?>
