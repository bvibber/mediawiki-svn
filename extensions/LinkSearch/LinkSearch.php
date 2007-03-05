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
		function __construct( $query , $ns ) {
			$this->mQuery = $query;
			$this->mNs = $ns;
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
		global $wgOut, $wgRequest;
		$target = $GLOBALS['wgRequest']->getVal( 'target', $par );
		$self = Title::makeTitle( NS_SPECIAL, 'Linksearch' );

		$wgOut->addWikiText( wfMsg( 'linksearch-text' ) );
		$patternbox = "<input type='text' size='50' name='target' id='pattern' value=\"" . htmlspecialchars ( $target ) . '" />';
		$submitbutton = '<input type="submit" value="' . wfMsgHtml( 'linksearch-ok' ) . '" />';
		$namespaceselect = HTMLnamespaceselector($namespace, '');

		$out = "<div class='namespaceoptions'><form method='get' action='{$wgScript}'>";
		$out .= '<input type="hidden" name="title" value="'.$self->getPrefixedDbKey().'" />';
		$out .= "<table id='nsselect' class='linksearch'>
			<tr>
				<td align='right'>" . wfMsgHtml('linksearch-pat') . "</td>
				<td align='left'><label for='nsfrom'>$patternbox</label></td>
			</tr>
			<tr>
				<td align='right'><label for='namespace'>" . wfMsgHtml('linksearch-ns') . "</label></td>
				<td align='left'>$namespaceselect $submitbutton</td>
			</tr>
			</table>";
		$out .= '</form></div>';
		$wgOut->addHtml($out);
		$namespace = $wgRequest->getIntorNull( 'namespace' );

		if( $target != '' ) {
			$searcher = new LinkSearchPage( $target, $namespace );
			$searcher->doQuery( $offset, $limit );
		}
	}
}

?>
