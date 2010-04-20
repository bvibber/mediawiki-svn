<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * implements Special:Unusedtemplates
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @ingroup SpecialPage
 */
class UnusedtemplatesPage extends QueryPage {

	function getName() { return( 'Unusedtemplates' ); }
	function isExpensive() { return true; }
	function isSyndicated() { return false; }
	function sortDescending() { return false; }

	function getQueryInfo() {
		return array (
			'tables' => array ( 'page', 'templatelinks' ),
			'fields' => array ( "'{$this->getName()}' AS type",
					'page_namespace AS namespace',
					'page_title AS title',
					'0 AS value' ),
			'conds' => array ( 'page_namespace' => NS_TEMPLATE,
					'tl_from IS NULL',
					'page_redirect' => 0 ),
			'join_conds' => array ( 'templatelinks' => array (
				'LEFT JOIN', array ( 'tl_title = page_title',
					'tl_namespace = page_namespace' ) ) )
		);
	}

	function formatResult( $skin, $result ) {
		$title = Title::makeTitle( NS_TEMPLATE, $result->title );
		$pageLink = $skin->linkKnown(
			$title,
			null,
			array(),
			array( 'redirect' => 'no' )
		);
		$wlhLink = $skin->linkKnown(
			SpecialPage::getTitleFor( 'Whatlinkshere' ),
			wfMsgHtml( 'unusedtemplateswlh' ),
			array(),
			array( 'target' => $title->getPrefixedText() )
		);
		return wfSpecialList( $pageLink, $wlhLink );
	}

	function getPageHeader() {
		return wfMsgExt( 'unusedtemplatestext', array( 'parse' ) );
	}

}

function wfSpecialUnusedtemplates() {
	list( $limit, $offset ) = wfCheckLimits();
	$utp = new UnusedtemplatesPage();
	$utp->doQuery( $offset, $limit );
}
