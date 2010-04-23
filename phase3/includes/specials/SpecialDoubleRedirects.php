<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * A special page listing redirects to redirecting page.
 * The software will automatically not follow double redirects, to prevent loops.
 * @ingroup SpecialPage
 */
class DoubleRedirectsPage extends PageQueryPage {

	function __construct() {
		SpecialPage::__construct( 'DoubleRedirects' );
	}
	
	// inexpensive?
	function isExpensive() { return true; }
	function isSyndicated() { return false; }
	function sortDescending() { return false; }

	function getPageHeader() {
		return wfMsgExt( 'doubleredirectstext', array( 'parse' ) );
	}

	function reallyGetQueryInfo( $namespace = null, $title = null ) {
		$limitToTitle = !( $namespace === null && $title === null );
		$retval = array (
			'tables' => array ( 'redirect ra', 'redirect rb',
					'page pa', 'page pb',
					'page pc' ),
			'fields' => array ( 'pa.page_namespace AS namespace',
					'pa.page_title AS title',
					'pb.page_namespace AS nsb',
					'pb.page_title AS tb',
					'pc.page_namespace AS nsc',
					'pc.page_title AS tc' ),
			'conds' => array ( 'ra.rd_from = pa.page_id',
					'pb.page_namespace = ra.rd_namespace',
					'pb.page_title = ra.rd_title',
					'rb.rd_from = pb.page_id',
					'pc.page_namespace = rb.rd_namespace',
					'pc.page_title = rb.rd_title' )
		);
		if ( $limitToTitle ) {
			$retval['conds']['pa.page_namespace'] = $namespace;
			$retval['conds']['pa.page_title'] = $title;
		}
		return $retval;
	}

	function getQueryInfo() {
		return $this->reallyGetQueryInfo();
	}

	function getOrderFields() {
		return array ( 'ra.rd_namespace', 'ra.rd_title' );
	}

	function formatResult( $skin, $result ) {
		global $wgContLang;

		$titleA = Title::makeTitle( $result->namespace, $result->title );

		if ( $result && !isset( $result->nsb ) ) {
			$dbr = wfGetDB( DB_SLAVE );
			$qi = $this->reallyGetQueryInfo( $result->namespace,
					$result->title );
			$res = $dbr->select($qi['tables'], $qi['fields'],
					$qi['conds'], __METHOD__ );
			if ( $res ) {
				$result = $dbr->fetchObject( $res );
				$dbr->freeResult( $res );
			}
		}
		if ( !$result ) {
			return '<s>' . $skin->link( $titleA, null, array(), array( 'redirect' => 'no' ) ) . '</s>';
		}

		$titleB = Title::makeTitle( $result->nsb, $result->tb );
		$titleC = Title::makeTitle( $result->nsc, $result->tc );

		$linkA = $skin->linkKnown(
			$titleA,
			null,
			array(),
			array( 'redirect' => 'no' )
		);
		$edit = $skin->linkKnown(
			$titleA,
			wfMsgExt( 'parentheses', array( 'escape' ), wfMsg( 'editlink' ) ),
			array(),
			array(
				'redirect' => 'no',
				'action' => 'edit'
			)
		);
		$linkB = $skin->linkKnown(
			$titleB,
			null,
			array(),
			array( 'redirect' => 'no' )
		);
		$linkC = $skin->linkKnown( $titleC );
		$arr = $wgContLang->getArrow() . $wgContLang->getDirMark();

		return( "{$linkA} {$edit} {$arr} {$linkB} {$arr} {$linkC}" );
	}
}
