<?php
/**
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
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * implements Special:Unusedimages
 * @ingroup SpecialPage
 */
class UnusedimagesPage extends ImageQueryPage {

	// inexpensive?
	function isExpensive() { return true; }

	function __construct() {
		SpecialPage::__construct( 'Unusedimages' );
	}
	
	function sortDescending() {
		return false;
	}
	function isSyndicated() { return false; }

	function getQueryInfo() {
		global $wgCountCategorizedImagesAsUsed;
		$retval = array (
			'tables' => array ( 'image', 'imagelinks' ),
			'fields' => array ( "'" . NS_FILE . "' AS namespace",
					'img_name AS title',
					'img_timestamp AS value',
					'img_user', 'img_user_text',
					'img_description' ),
			'conds' => array ( 'il_to IS NULL' ),
			'join_conds' => array ( 'imagelinks' => array (
					'LEFT JOIN', 'il_to = img_name' ) )
		);
		if ( $wgCountCategorizedImagesAsUsed ) {
			// Order is significant
			// TODO: Revise query to LEFT JOIN page instead
			$retval['tables'] = array ( 'page', 'categorylinks',
					'imagelinks', 'image' );
			$retval['conds']['page_namespace'] = NS_FILE;
			$retval['conds'][] = 'cl_from IS NULL';
			$retval['join_conds']['categorylinks'] = array (
					'LEFT JOIN', 'cl_from = page_id' );
			$retval['join_conds']['imagelinks'] = array (
					'LEFT JOIN', 'il_to = page_title' );
			// TODO: Make this one implicit?
			$retval['join_conds']['image'] = array (
					'INNER JOIN', 'img_name = page_title' );
		}
		return $retval;
	}

	function usesTimestamps() {
		return true;
	}

	function getPageHeader() {
		return wfMsgExt( 'unusedimagestext', array( 'parse' ) );
	}

}
