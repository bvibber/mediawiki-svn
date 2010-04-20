<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * implements Special:Unusedimages
 * @ingroup SpecialPage
 */
class UnusedimagesPage extends ImageQueryPage {

	function isExpensive() { return true; }

	function getName() {
		return 'Unusedimages';
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

/**
 * Entry point
 */
function wfSpecialUnusedimages() {
	list( $limit, $offset ) = wfCheckLimits();
	$uip = new UnusedimagesPage();

	return $uip->doQuery( $offset, $limit );
}
