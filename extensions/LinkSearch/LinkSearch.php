<?php

/**
 * Quickie special page to search the external-links table.
 * Currently only 'http' links are supported; LinkFilter needs to be
 * changed to allow other pretties.
 */

$wgExtensionFunctions[] = 'wfLinkSearchSetup';

# Internationalisation file
require_once( 'LinkSearch.i18n.php' );

function wfLinkSearchSetup() {
	# Add messages
	global $wgMessageCache, $wgLinkSearchMessages;
	foreach( $wgLinkSearchMessages as $key => $value ) {
		$wgMessageCache->addMessages( $wgLinkSearchMessages[$key], $key );
	}

	$GLOBALS['wgSpecialPages']['Linksearch'] = array( /*class*/ 'SpecialPage', 
		/*name*/ 'Linksearch', /* permission */'', /*listed*/ true, 
		/*function*/ false, /*file*/ false );
	
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
			
			$encSearch = $dbr->addQuotes( self::mungeQuery( $this->mQuery ) );
			
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
					AND el_index LIKE $encSearch";
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
			$this->mMungedQuery = LinkSearchPage::mungeQuery( $this->mQuery );
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
	
	function wfSpecialLinksearch( $par=null ) {
		list( $limit, $offset ) = wfCheckLimits();
		$target = $GLOBALS['wgRequest']->getVal( 'target', $par );
		
		$self = Title::makeTitle( NS_SPECIAL, 'Linksearch' );
		
		global $wgOut;
		$wgOut->addWikiText( wfMsg( 'linksearch-text' ) );
		$wgOut->addHtml(
			wfOpenElement( 'form',
				array( 'method' => 'get', 'action' => $GLOBALS['wgScript'] ) ) .
			wfHidden( 'title', $self->getPrefixedDbKey() ) .
			wfInput( 'target', 50, $target ) .
			wfSubmitButton( wfMsg( 'search' ) ) .
			wfCloseElement( 'form' ) );

		if( $target != '' ) {
			$searcher = new LinkSearchPage( $target );
			$searcher->doQuery( $offset, $limit );
		}
	}
}

?>
