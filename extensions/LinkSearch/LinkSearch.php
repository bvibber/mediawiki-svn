<?php

/**
 * Quickie special page to search the external-links table.
 * Currently only 'http' links are supported; LinkFilter needs to be
 * changed to allow other pretties.
 */

$wgExtensionFunctions[] = 'wfLinkSearchSetup';

function wfLinkSearchSetup() {
	$GLOBALS['wgMessageCache']->addMessages(
		array(
			'linksearch' => 'Search web links',
			'linksearch-text' => 'Wildcards such as "*.wikipedia.org" may be used.',
			'linksearch-line' => '$1 linked from $2',
		)
	);
	
	SpecialPage::addPage( new SpecialPage( 'Linksearch', /* permission */false,
		/*listed*/ true, /*function*/ false, /*file*/ false ) );
	
	class LinkSearchPage extends QueryPage {
		function __construct( $query ) {
			$this->mQuery = $query;
		}
		
		function getName() {
			return 'Linksearch';
		}
		
		/**
		 * Return an appropriately formatted LIKE query
		 * @fixme Fix up LinkFilter to work with non-http links as well
		 */
		static function mungeQuery( $query ) {
			if( substr( $query, 0, 7 ) == 'http://' ) {
				$query = substr( $query, 7 );
			}
			return LinkFilter::makeLike( $query );
		}
		
		function linkParameters() {
			return array( 'target' => $this->mQuery );
		}
		
		function getSQL() {
			$dbr = wfGetDB( DB_SLAVE );
			
			$page = $dbr->tableName( 'page' );
			$externallinks = $dbr->tableName( 'externallinks' );
			
			$search = self::mungeQuery( $this->mQuery );
			$encSearch = $dbr->addQuotes( $search );
			
			return
				"SELECT
					page_namespace AS namespace,
					page_title AS title,
					el_index AS value,
					el_to AS url
				FROM
					$page,
					$externallinks
				WHERE
					page_id=el_from
					AND el_index LIKE $encSearch";
		}
		
		function formatResult( $skin, $result ) {
			$title = Title::makeTitle( $result->namespace, $result->title );
			$url = $result->url;
			
			$pageLink = $skin->makeKnownLinkObj( $title );
			$urlLink = $skin->makeExternalLink( $url, $url );
			
			return wfMsgHtml( 'linksearch-line', $urlLink, $pageLink );
		}
	
	}
	
	function wfSpecialLinksearch( $par=null ) {
		list( $limit, $offset ) = wfCheckLimits();
		$target = $GLOBALS['wgRequest']->getVal( 'target', $par );
		
		$self = Title::makeTitle( NS_SPECIAL, 'Linksearch' );
		
		global $wgOut;
		$wgOut->addWikiText( wfMsg( 'linksearch-text' ) );
		$wgOut->addHtml(
			wfOpenElement( 'form',
				array( 'method' => 'get', 'action' => $GLOBALS['wgScript'] ) ) .
			wfHidden( 'title', $self->getPrefixedUrl() ) .
			wfInput( 'target', 50, $target ) .
			wfSubmitButton( wfMsg( 'search' ) ) .
			wfCloseElement( 'form' ) );

		if( $target != '' ) {
			// For debugging
			$search = LinkSearchPage::mungeQuery( $target );
			$wgOut->addHtml( "\n<!-- " . htmlspecialchars( $search ) . " -->\n" );
			
			$searcher = new LinkSearchPage( $target );
			$searcher->doQuery( $offset, $limit );
		}
	}
}

?>
