<?php
/**
 * This file has been copied from Extension:GlobalUsage and adapted
 * to show the usage of a template instead of a file.
 * Special page to show global template usage. Also contains hook functions for
 * showing usage on an template page.
 */

class SpecialGlobalTemplateUsage extends SpecialPage {
	public function __construct() {
		parent::__construct( 'GlobalTemplateUsage', 'globaltemplateusage' );
	}

	/**
	 * Entry point
	 */
	public function execute( $par ) {
		global $wgOut, $wgRequest;

		$target = $par ? $par : $wgRequest->getVal( 'target' );
		$this->target = Title::newFromText( $target );

		$this->setHeaders();

		$this->showForm();

		if ( is_null( $this->target ) )
		{
			$wgOut->setPageTitle( wfMsg( 'globaltemplateusage' ) );
			return;
		}

		$wgOut->setPageTitle( wfMsg( 'globaltemplateusage-for', $this->target->getPrefixedText() ) );

		$this->showResult();
	}
	
	/**
	 * Shows the search form
	 */
	private function showForm() {
		global $wgScript, $wgOut, $wgRequest;

		/* Build form */
		$html = Xml::openElement( 'form', array( 'action' => $wgScript ) ) . "\n";
		// Name of SpecialPage
		$html .= Xml::hidden( 'title', $this->getTitle( )->getPrefixedText( ) ) . "\n";
		// Limit
		$html .= Xml::hidden( 'limit', $wgRequest->getInt( 'limit', 50 ) );
		// Input box with target prefilled if available
		$formContent = "\t" . Xml::input( 'target', 40, is_null( $this->target ) ? ''
					: $this->target->getPrefixedText( ) )
		// Submit button
			. "\n\t" . Xml::element( 'input', array(
					'type' => 'submit',
					'value' => wfMsg( 'globaltemplateusage-ok' )
					) );
		
		// Wrap the entire form in a nice fieldset
		$html .= Xml::fieldSet( wfMsg( 'globaltemplateusage-text' ), $formContent ) . "\n</form>";

		$wgOut->addHtml( $html );
	}

	/**
	 * Creates as queryer and executes it based on $wgRequest
	 */
	private function showResult() {
		global $wgRequest;

		$query = new GlobalTemplateUsageQuery( $this->target );

		// Extract params from $wgRequest
		if ( $wgRequest->getText( 'from' ) ) {
			$query->setOffset( $wgRequest->getText( 'from' ) );
		} elseif ( $wgRequest->getText( 'to' ) ) {
			$query->setOffset( $wgRequest->getText( 'to' ), true );			
		}
		$query->setLimit( $wgRequest->getInt( 'limit', 50 ) );

		// Perform query
		$query->execute();

		// Show result
		global $wgOut;

		// Don't show form element if there is no data
		if ( $query->count() == 0 ) {
			$wgOut->addWikiMsg( 'globaltemplateusage-no-results', $this->target->getPrefixedText( ) );
			return;
		}

		$offset = $query->getOffsetString( );
		$navbar = $this->getNavBar( $query );
		$targetName = $this->target->getPrefixedText( );

		// Top navbar
		$wgOut->addHtml( $navbar );

		$wgOut->addHtml( '<div id="mw-globaltemplateusage-result">' );
		foreach ( $query->getSingleTemplateResult() as $wiki => $result ) {
			$wgOut->addHtml(
					'<h2>' . wfMsgExt(
						'globaltemplateusage-on-wiki', 'parseinline',
						$targetName, WikiMap::getWikiName( $wiki ) )
					. "</h2><ul>\n" );
			foreach ( $result as $item ) {
				$wgOut->addHtml( "\t<li>" . self::formatItem( $item ) . "</li>\n" );
			}
			$wgOut->addHtml( "</ul>\n" );
		}
		$wgOut->addHtml( '</div>' );

		// Bottom navbar
		$wgOut->addHtml( $navbar );
	}
	
	/**
	 * Helper to format a specific item
	 */
	public static function formatItem( $item ) {
		if ( !$item['namespace'] ) {
			$page = $item['title'];
		} else {
			$page = "{$item['namespace']}:{$item['title']}";
		}

		$link = WikiMap::makeForeignLink( $item['wiki'], $page,
				str_replace( '_', ' ', $page ) );
		// Return only the title if no link can be constructed
		return $link === false ? $page : $link;
	}

	/**
	 * Helper function to create the navbar, stolen from wfViewPrevNext
	 * 
	 * @param $query GlobalTemplateUsageQuery An executed GlobalTemplateUsageQuery object
	 * @return string Navbar HTML
	 */
	protected function getNavBar( $query ) {
		global $wgLang, $wgUser;

		$skin = $wgUser->getSkin();

		$target = $this->target->getPrefixedText();
		$limit = $query->getLimit();
		$fmtLimit = $wgLang->formatNum( $limit );
	
		# Find out which strings are for the prev and which for the next links
		$offset = $query->getOffsetString();
		$continue = $query->getContinueString();
		if ( $query->isReversed() ) {
			$from = $offset;
			$to = $continue;
		} else {
			$from = $continue;
			$to = $offset;
		}

		# Get prev/next link display text
		$prev =  wfMsgExt( 'prevn', array( 'parsemag', 'escape' ), $fmtLimit );
		$next =  wfMsgExt( 'nextn', array( 'parsemag', 'escape' ), $fmtLimit );
		# Get prev/next link title text
		$pTitle = wfMsgExt( 'prevn-title', array( 'parsemag', 'escape' ), $fmtLimit );
		$nTitle = wfMsgExt( 'nextn-title', array( 'parsemag', 'escape' ), $fmtLimit );

		# Fetch the title object
		$title = $this->getTitle();

		# Make 'previous' link
		if ( $to ) {
			$attr = array( 'title' => $pTitle, 'class' => 'mw-prevlink' );
			$q = array( 'limit' => $limit, 'to' => $to, 'target' => $target );
			$plink = $skin->link( $title, $prev, $attr, $q );
		} else {
			$plink = $prev;
		}

		# Make 'next' link
		if ( $from ) {
			$attr = array( 'title' => $nTitle, 'class' => 'mw-nextlink' );
			$q = array( 'limit' => $limit, 'from' => $from, 'target' => $target );
			$nlink = $skin->link( $title, $next, $attr, $q );
		} else {
			$nlink = $next;
		}

		# Make links to set number of items per page
		$numLinks = array();
		foreach ( array( 20, 50, 100, 250, 500 ) as $num ) {
			$fmtLimit = $wgLang->formatNum( $num );
			
			$q = array( 'offset' => $offset, 'limit' => $num, 'target' => $target );
			$lTitle = wfMsgExt( 'shown-title', array( 'parsemag', 'escape' ), $num );
			$attr = array( 'title' => $lTitle, 'class' => 'mw-numlink' );

			$numLinks[] = $skin->link( $title, $fmtLimit, $attr, $q );
		}
		$nums = $wgLang->pipeList( $numLinks );

		return wfMsgHtml( 'viewprevnext', $plink, $nlink, $nums );
	}
}

/**
 * This class has been copied from Extension:GlobalUsage / GlobalUsageQuery.php
 * Extension:GlobalUsage should be built-in and the GlobalUsageQuery adapted
 * to be able to fetch the global usage of templates as well as files.
 */
class GlobalTemplateUsageQuery {
	private $limit = 50;
	private $offset;
	private $hasMore = false;
	private $result;
	private $continue;
	private $reversed = false;
	private $target = null;

	/**
	 * @param $target mixed Title or db key, or array of db keys of target(s)
	 */
	public function __construct( $target ) {
		global $wgGlobalDatabase;
		$this->db = wfGetDB( DB_SLAVE, array(), $wgGlobalDatabase );
		$this->target = $target;
		$this->offset = array();
	}

	/**
	 * Set the offset parameter
	 *
	 * @param $offset string offset
	 * @param $reversed bool True if this is the upper offset
	 */
	public function setOffset( $offset, $reversed = null ) {
		if ( !is_null( $reversed ) ) {
			$this->reversed = $reversed;
		}
		
		if ( !is_array( $offset ) ) {
			$offset = explode( '|', $offset );
		}

		if ( count( $offset ) == 3 ) {
			$this->offset = $offset;
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Return the offset set by the user
	 *
	 * @return array offset
	 */
	public function getOffsetString() {
		return implode( '|', $this->offset );
	}
	
	/**
	 * Is the result reversed
	 * 
	 * @return bool
	 */
	public function isReversed() {
		return $this->reversed;
	}
	
	/**
	 * Returns the string used for continuation
	 * 
	 * @return string
	 * 
	 */
	public function getContinueString() {
		if ( $this->hasMore() )
			return "{$this->lastRow->gtl_to_title}|{$this->lastRow->gtl_from_wiki}|{$this->lastRow->gtl_from_page}";
		else
			return '';
	}

	/**
	 * Set the maximum amount of items to return. Capped at 500.
	 *
	 * @param $limit int The limit
	 */
	public function setLimit( $limit ) {
		$this->limit = min( $limit, 500 );
	}
	
	/**
	 * Returns the user set limit
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * Executes the query
	 */
	public function execute() {
		global $wgLocalInterwiki;
		
		/* Construct a where clause */
		// Add target template(s)
		$where = array( 'gtl_to_prefix' => $wgLocalInterwiki,
						'gtl_to_namespace' => $this->target->getNamespace( ),
						'gtl_to_title' => $this->target->getDBkey( )
				);

		// Set the continuation condition
		$order = 'ASC';
		if ( $this->offset ) {
			$qTo = $this->db->addQuotes( $this->offset[0] );
			$qWiki = $this->db->addQuotes( $this->offset[1] );
			$qPage = intval( $this->offset[2] );
			
			// Check which limit we got in order to determine which way to traverse rows
			if ( $this->reversed ) {
				// Reversed traversal; do not include offset row
				$op1 = '<';
				$op2 = '<';
				$order = 'DESC';
			} else {
				// Normal traversal; include offset row
				$op1 = '>';
				$op2 = '>=';
				$order = 'ASC';
			}
			
			$where[] = "(gtl_to_title $op1 $qTo) OR " .
				"(gtl_to_title = $qTo AND gtl_from_wiki $op1 $qWiki) OR " .
				"(gtl_to_title = $qTo AND gtl_from_wiki = $qWiki AND gtl_from_page $op2 $qPage)";
		}

		/* Perform select (Duh.) */
		$res = $this->db->select( 'globaltemplatelinks',
				array(
					'gtl_to_title',
					'gtl_from_wiki',
					'gtl_from_page',
					'gtl_from_namespace',
					'gtl_from_title'
				),
				$where,
				__METHOD__,
				array(
					'ORDER BY' => "gtl_to_title $order, gtl_from_wiki $order, gtl_from_page $order",
					// Select an extra row to check whether we have more rows available
					'LIMIT' => $this->limit + 1,
				)
		);

		/* Process result */
		// Always return the result in the same order; regardless whether reversed was specified
		// reversed is really only used to determine from which direction the offset is
		$rows = array();
		foreach ( $res as $row ) {
			$rows[] = $row;
		}
		if ( $this->reversed ) {
			$rows = array_reverse( $rows );
		}
		
		// Build the result array
		$count = 0;
		$this->hasMore = false;
		$this->result = array();
		foreach ( $rows as $row ) {
			$count++;
			if ( $count > $this->limit ) {
				// We've reached the extra row that indicates that there are more rows
				$this->hasMore = true;
				$this->lastRow = $row;
				break;
			}

			if ( !isset( $this->result[$row->gtl_to_title] ) ) {
				$this->result[$row->gtl_to_title] = array();
			}
			if ( !isset( $this->result[$row->gtl_to_title][$row->gtl_from_wiki] ) ) {
				$this->result[$row->gtl_to_title][$row->gtl_from_wiki] = array();
			}

			$this->result[$row->gtl_to_title][$row->gtl_from_wiki][] = array(
				'template'	=> $row->gtl_to_title,
				'id' => $row->gtl_from_page,
				'namespace' => $row->gtl_from_namespace,
				'title' => $row->gtl_from_title,
				'wiki' => $row->gtl_from_wiki,
			);
		}
	}
	/**
	 * Returns the result set. The result is a 4 dimensional array
	 * (file, wiki, page), whose items are arrays with keys:
	 *   - template: File name 
	 *   - id: Page id
	 *   - namespace: Page namespace text
	 *   - title: Unprefixed page title
	 *   - wiki: Wiki id
	 * 
	 * @return array Result set
	 */
	public function getResult() {
		return $this->result;
	}
	
	/**
	 * Returns a 3 dimensional array with the result of the first file. Useful
	 * if only one template was queried.
	 * 
	 * For further information see documentation of getResult()
	 * 
	 * @return array Result set
	 */
	public function getSingleTemplateResult() {
		if ( $this->result ) {
			return current( $this->result );
		} else {
			return array();
		}
	}

	/**
	 * Returns whether there are more results
	 *
	 * @return bool
	 */
	public function hasMore() {
		return $this->hasMore;
	}

	/**
	 * Returns the result length
	 * 
	 * @return int
	 */
	public function count() {
		return count( $this->result );
	}
}
