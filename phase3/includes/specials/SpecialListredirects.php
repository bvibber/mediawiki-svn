<?php
/**
 * @file
 * @ingroup SpecialPage
 *
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

/**
 * Special:Listredirects - Lists all the redirects on the wiki.
 * @ingroup SpecialPage
 */
class ListredirectsPage extends QueryPage {

	function getName() { return 'Listredirects'; }
	// inexpensive?
	function isExpensive() { return true; }
	function isSyndicated() { return false; }
	function sortDescending() { return false; }

	function getQueryInfo() {
		return array(
			'tables' => array( 'page AS p1', 'redirect', 'page AS p2' ),
			'fields' => array( 'p1.page_namespace AS namespace',
					'p1.page_title AS title',
					'rd_namespace',
					'rd_title',
					'p2.page_id AS redirid' ),
			'conds' => array( 'p1.page_is_redirect' => 1 ),
			'join_conds' => array( 'redirect' => array(
					'LEFT JOIN', 'rd_from=p1.page_id' ),
				'page AS p2' => array( 'LEFT JOIN', array(
					'p2.page_namespace=rd_namespace',
					'p2.page_title=rd_title' ) ) )
		);
	}

	function getOrderFields() {
		return array ( 'namespace', 'title' );
	}

	function formatResult( $skin, $result ) {
		global $wgContLang;

		# Make a link to the redirect itself
		$rd_title = Title::makeTitle( $result->namespace, $result->title );
		$rd_link = $skin->makeKnownLinkObj(
			$rd_title,
			null,
			'redirect=no'
		);

		# Find out where the redirect leads
		$target = null;
		if( !is_null( $result->rd_namespace ) ) {
			$target = Title::makeTitle( $result->rd_namespace, $result->rd_title );
		} else {
			$revision = Revision::newFromTitle( $rd_title );
			if( $revision ) {
				$target = Title::newFromRedirect( $revision->getText() );
			}
		}
		
		# Make a link to the destination page
		if( $target ) {
			$arr = $wgContLang->getArrow() . $wgContLang->getDirMark();
			if( !is_null ( $result->redirid ) )
				$targetLink = $skin->makeKnownLinkObj( $target );
			else if( !is_null( $result->rd_namespace ) )
				$targetLink = $skin->makeBrokenLinkObj( $target );
			else
				$targetLink = $skin->link( $target );
			return "$rd_link $arr $targetLink";
		} else {
			return "<s>$rd_link</s>";
		}
	}
}

function wfSpecialListredirects() {
	list( $limit, $offset ) = wfCheckLimits();
	$lrp = new ListredirectsPage();
	$lrp->doQuery( $offset, $limit );
}
