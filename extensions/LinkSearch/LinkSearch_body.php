<?php
class LinkSearchPage extends QueryPage {
	function __construct( $query , $ns , $prot ) {
		wfLinkSearchLoadMessages();
		$this->mQuery = $query;
		$this->mNs = $ns;
		$this->mProt = $prot;
	}

	function getName() {
		return 'Linksearch';
	}

	/**
	 * Disable RSS/Atom feeds
	 */
	function isSyndicated() {
		return false;
	}

	/**
	 * Return an appropriately formatted LIKE query
	 */
	static function mungeQuery( $query , $prot ) {
		return LinkFilter::makeLike( $query , $prot );
	}

	function linkParameters() {
		return array( 'target' => $this->mQuery, 'namespace' => $this->mNs );
	}

	function getSQL() {
		global $wgMiserMode;
		$dbr = wfGetDB( DB_SLAVE );
		$page = $dbr->tableName( 'page' );
		$externallinks = $dbr->tableName( 'externallinks' );

		/* strip everything past first wildcard, so that index-based-only lookup would be done */
		$munged = self::mungeQuery( $this->mQuery, $this->mProt );
		$stripped = substr($munged,0,strpos($munged,'%')+1);
		$encSearch = $dbr->addQuotes( $stripped );

		$encSQL = '';
		if ( isset ($this->mNs) && !$wgMiserMode ) $encSQL = 'AND page_namespace=' . $this->mNs;


		return
			"SELECT
				page_namespace AS namespace,
				page_title AS title,
				el_index AS value,
				el_to AS url
			FROM
				$page,
				$externallinks FORCE INDEX (el_index)
			WHERE
				page_id=el_from
				AND el_index LIKE $encSearch
				$encSQL";
	}

	function formatResult( $skin, $result ) {
		$title = Title::makeTitle( $result->namespace, $result->title );
		$url = $result->url;
		$pageLink = $skin->makeKnownLinkObj( $title );
		$urlLink = $skin->makeExternalLink( $url, $url );

		return wfMsgHtml( 'linksearch-line', $urlLink, $pageLink );
	}

	/**
	 * Override to check query validity.
	 */
	function doQuery( $offset, $limit, $shownavigation=true ) {
		global $wgOut;
		$this->mMungedQuery = LinkSearchPage::mungeQuery( $this->mQuery, $this->mProt );
		if( $this->mMungedQuery === false ) {
			$wgOut->addWikiText( wfMsg( 'linksearch-error' ) );
		} else {
			// For debugging
			$wgOut->addHtml( "\n<!-- " . htmlspecialchars( $this->mMungedQuery ) . " -->\n" );
			parent::doQuery( $offset, $limit, $shownavigation );
		}
	}

	/**
	 * Override to squash the ORDER BY.
	 * We do a truncated index search, so the optimizer won't trust
	 * it as good enough for optimizing sort. The implicit ordering
	 * from the scan will usually do well enough for our needs.
	 */
	function getOrder() {
		return '';
	}
}
?>
