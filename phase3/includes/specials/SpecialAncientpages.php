<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * Implements Special:Ancientpages
 * @ingroup SpecialPage
 */
class AncientPagesPage extends QueryPage {

	function getName() {
		return "Ancientpages";
	}

	function isExpensive() {
		return true;
	}

	function isSyndicated() { return false; }

	function getQueryInfo() {
		// FIXME convert timestamps elsewhere
		// Possibly add bool returnsTimestamps()
		// FIXME standardize 'name' AS type ?
		global $wgDBtype;
		$epoch = $wgDBtype == 'mysql' ? 'UNIX_TIMESTAMP(rev_timestamp)' :
				'EXTRACT(epoch FROM rev_timestamp)';
		return array(
			'tables' => array( 'page', 'revision' ),
			'fields' => array( "'{$this->getName()}' AS type",
					'page_namespace AS namespace',
					'page_title AS title',
					"$epoch AS value" ),
			'conds' => array( 'page_namespace' => NS_MAIN,
					'page_is_redirect' => 0,
					'page_latest=rev_id' )
		);
	}

	function sortDescending() {
		return false;
	}

	function formatResult( $skin, $result ) {
		global $wgLang, $wgContLang;

		$d = $wgLang->timeanddate( wfTimestamp( TS_MW, $result->value ), true );
		$title = Title::makeTitle( $result->namespace, $result->title );
		$link = $skin->linkKnown(
			$title,
			htmlspecialchars( $wgContLang->convert( $title->getPrefixedText() ) )
		);
		return wfSpecialList($link, htmlspecialchars($d) );
	}
}

function wfSpecialAncientpages() {
	list( $limit, $offset ) = wfCheckLimits();

	$app = new AncientPagesPage();

	$app->doQuery( $offset, $limit );
}
