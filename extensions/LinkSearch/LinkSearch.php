<?php

/**
 * Quickie special page to search the external-links table.
 * Currently only 'http' links are supported; LinkFilter needs to be
 * changed to allow other pretties.
 */

$wgExtensionFunctions[] = 'wfLinkSearchSetup';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Linksearch',
	'author' => 'Brion Vibber',
	'description' => 'Search for Weblinks',
);

# Internationalisation file
require_once( 'LinkSearch.i18n.php' );

function wfLinkSearchSetup() {
	# Add messages
	global $wgMessageCache, $wgLinkSearchMessages;
	foreach( $wgLinkSearchMessages as $lang => $messages ) {
		$wgMessageCache->addMessages( $messages, $lang );
	}

	$GLOBALS['wgSpecialPages']['Linksearch'] = array( /*class*/ 'SpecialPage', 
		/*name*/ 'Linksearch', /* permission */'', /*listed*/ true, 
		/*function*/ false, /*file*/ false );

	class LinkSearchPage extends QueryPage {
		function __construct( $query , $ns , $prot ) {
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
			return array( 'target' => $this->mQuery, 'namespace' => $this->mNs , 'protocol' => $this->mProt );
		}

		function getSQL() {
			$dbr = wfGetDB( DB_SLAVE );
			$page = $dbr->tableName( 'page' );
			$externallinks = $dbr->tableName( 'externallinks' );
			$encSearch = $dbr->addQuotes( self::mungeQuery( $this->mQuery,  $this->mProt ) );
			$encSQL = '';
			if ( isset ($this->mNs) ) $encSQL = 'AND page_namespace=' . $this->mNs;
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
		function doQuery( $offset, $limit ) {
			global $wgOut;
			$this->mMungedQuery = LinkSearchPage::mungeQuery( $this->mQuery, $this->mProt );
			if( $this->mMungedQuery === false ) {
				$wgOut->addWikiText( wfMsg( 'linksearch-error' ) );
			} else {
				// For debugging
				$wgOut->addHtml( "\n<!-- " . htmlspecialchars( $this->mMungedQuery ) . " -->\n" );
				parent::doQuery( $offset, $limit );
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

	function wfSpecialLinksearch( $par=null, $ns=null ) {
		list( $limit, $offset ) = wfCheckLimits();
		global $wgOut, $wgRequest, $wgUrlProtocols;
		$target = $GLOBALS['wgRequest']->getVal( 'target', $par );
		$namespace = $GLOBALS['wgRequest']->getIntorNull( 'namespace', $ns );
		$protocol = $GLOBALS['wgRequest']->getVal( 'protocol', $prot );
		$self = Title::makeTitle( NS_SPECIAL, 'Linksearch' );

		$wgOut->addWikiText( wfMsg( 'linksearch-text', '<nowiki>' . implode( ', ',  $wgUrlProtocols) . '</nowiki>' ) );
		$s =	Xml::openElement( 'form', array( 'method' => 'get', 'action' => $GLOBALS['wgScript'] ) ) .
			Xml::hidden( 'title', $self->getPrefixedDbKey() ) .
			'<fieldset>' .
			Xml::element( 'legend', array(), wfMsg( 'linksearch' ) ) .
			Xml::label( wfMsg( 'linksearch-pat' ), 'target' ) . ' ' .
			"<select id='protocol' name='protocol' class='protocolselector'>";
		foreach( $wgUrlProtocols as $prot ) {
			if ( $prot == $protocol ) {
				$s .= Xml::option( $prot, $prot, true );
			} else {
				$s .= Xml::option( $prot, $prot );
			}
		}
		$s .=	Xml::input( 'target', 50 , $target ) . '<br />' .
			Xml::label( wfMsg( 'linksearch-ns' ), 'namespace' ) .
			XML::namespaceSelector( $namespace, '' ) .
			Xml::submitButton( wfMsg( 'linksearch-ok' ) ) .
			'</fieldset>' .
			Xml::closeElement( 'form' );
		$wgOut->addHtml( $s );

		if( $target != '' ) {
			$searcher = new LinkSearchPage( $target, $namespace, $protocol );
			$searcher->doQuery( $offset, $limit );
		}
	}
}

?>
