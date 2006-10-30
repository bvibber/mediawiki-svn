<?php
/*
 *  Maintain a database of articles containing the <GEO> tag
 *
 *  To install, put the following in your LocalSettings.php
 *

include( "extensions/gis/database.php" );

 *
 *  The database also needs the table given in "gisdb.sql" to be added.
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

$wgExtensionFunctions[] = "wfGISDatabaseExtension";

require_once( "geo.php" );

/**
 *  Hook up
 */
function wfGISDatabaseExtension ()
{
	global $wgHooks ;
	$wgHooks['ArticleSaveComplete'][] = 'article_save_geo';
	$wgHooks['ArticleDelete'][] = 'article_delete_geo';
}

/**
 *  Hook function called every time a page is saved
 *  Use the ArticleSaveComplete instead of ArticleSave since the ID is
 *  not available upon ArticleSave for new articles
 */
function article_save_geo ( $article, $user, $text ) 
{
	$id = $article->getID();

	$g = new gis_database();

	$g->delete_position( $id );

	$tag = 'geo';
	$gis_content = array();
	$text = Parser::extractTags( $tag, $text, $gis_content );
	foreach( $gis_content as $marker => $content ) {

		$p = new geo_param( $content );
		$attr = $p->get_attr();

		$g->add_position( $id,
				   $p->latdeg_min, $p->londeg_min,
				   $p->latdeg_max, $p->londeg_max,
				   $attr['globe'],
				   $attr['type'], $attr['arg:type'] );
	}
	return true;
}

/**
 *  Hook function called every time a page is deleted
 */
function article_delete_geo ( $article ) 
{
	$id = $article->getID();

	$g = new gis_database();

	$g->delete_position( $id );

	return true;
}

/**
 *
 */
class gis_database {

	var $db;
	var $result;

	var $version;

	function gis_database()
	{
		$this->db =& wfGetDB( DB_MASTER );

		global $wgVersion;
		$this->version = explode('.',$wgVersion);
	}


	/**
	 * delete all position data related to this article
	 */
	function delete_position( $id )
	{
		$fname = 'gis_database::delete_positions';

		$this->db->delete( 'gis', array( 'gis_page' => $id ), $fname);
	}

	/**
	 *  Add a position to the database
	 */
	function add_position( $id, $latmin, $lonmin, 
			       $latmax, $lonmax, $globe, $type, $type_arg )
	{
		$fname = 'gis_database::add_position';

		if ($id == 0) return; # should not happen...

		if (!$globe) $globe = "";

		$type_arg = str_replace( ',', '', $type_arg); /* ignore commas */

		$this->db->insert( 'gis',
			       array(
					'gis_page'          => $id,
					'gis_latitude_min'  => $latmin,
					'gis_longitude_min' => $lonmin,
					'gis_latitude_max'  => $latmax,
					'gis_longitude_max' => $lonmax,
					'gis_globe'         => $globe,
					'gis_type'          => $type,
					'gis_type_arg'      => $type_arg,
					'gis_sector'	    => ( IntVal( $latmin+90 ) *360 + IntVal( $lonmin ) ) ),
			       $fname );
	}

	/**
	 *  Select entities with a certain radius expressed in meters
	 *  Also select by globe and type if specified
	 *  Note that the bounding box created by this function will
	 *  by nature also include points beyond the given radius
	 */
	 function select_radius_m( $lat, $lon, $r, $globe, $type, $type_arg )
	 {
		$delta_lat = $r / (60 * 1852);
		$c = cos($lat * (M_PI / 180));
		if ($c <= 0.001) {
			$delta_lon = 360;
		} else {
			$delta_lon = $r / (60 * 1852 * $c);
		}

		$latmin = $lat - $delta_lat;
		$latmax = $lat + $delta_lat;
		if ($latmax > 90 or $latmin < -90) {
			# we cross the poles, so look all over the place
			$lonmin = -180;
			$lonmax = 180;
		} else {
			$lonmin = $lon - $delta_lon;
			$lonmax = $lon + $delta_lon;
		}
		return $this->select_area( $latmin, $lonmin, $latmax, $lonmax,
					   $globe, $type, $type_arg );
	}

	/**
	 *  Select entities belonging to or overlapping an area
	 *  Also select by globe and type if specified
	 */
	 function select_area( $latmin, $lonmin, $latmax, $lonmax, 
			       $globe, $type, $type_arg )
	 {
		if (!$globe) $globe = "";

		$condition = "gis_globe = '" . $globe . "'";

		if ($latmin > -90) {
			$condition .= " AND gis_latitude_max >= " . $latmin;
		}
		if ($latmax < 90) {
			$condition .= " AND gis_latitude_min <= " . $latmax;
		}
		if ($lonmin > -180) {
			$condition .= " AND gis_longitude_max >= " . $lonmin;
		}
		if ($lonmax < 180) {
			$condition .= " AND gis_longitude_min <= " . $lonmax;
		}
		if ($type and $type != "") {
			$condition .= " AND gis_type = '" . $type . "'";
			if ($type_arg and $type_arg != "") {
				$condition .= " AND gis_type_arg >= "
					   . str_replace( ',', '', $type_arg);
			}
		}
		return $this->select_position( $condition );
	}

	/**
	 *  Select entities according to a specific condition
	 */
	 function select_position( $condition )
	 {
		$fname = 'gis_database::select_position';

		# BUG: use selectRow instead
		$this->result = $this->db->select( array( 'gis', 'page' ),
			      array(
				'gis_page',
				'gis_latitude_min',
				'gis_latitude_max',
				'gis_longitude_min',
				'gis_longitude_max',
				'gis_globe',
				'gis_type', 
				'gis_type_arg',
				'page_title',
				'page_namespace' ),
			      array_merge($condition , array( 'page_id = gis_page' ) ),
			      $fname );
	}

	/**
	 *  Fetch selected points following call to select_position()
	 */
	function fetch_position()
	{
		return $this->db->fetchObject ( $this->result );
	}
}
?>
