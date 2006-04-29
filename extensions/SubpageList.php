<?php

/**
 * Add a <subpages /> tag which produces a linked list of all subpages of the current page
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0 or later
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efSubpageListSetup';
	$wgExtensionCredits['parser'][] = array( 'name' => 'Subpage List', 'author' => 'Rob Church' );

	function efSubpageListSetup() {
		global $wgParser;
		$wgParser->setHook( 'subpages', 'efSubpageList' );
	}
	
	function efSubpageList( $input, $args, &$parser ) {
		$dbr =& wfGetDB( DB_SLAVE );
		
		# Use the skin and title provided by the parser
		$skin = $parser->mOptions->getSkin();
		$self = $parser->mTitle;
		
		# Prepare to run the SQL queries
		$page = $dbr->tableName( 'page' );
		$ns = (int)$self->getNamespace();
		$like = $dbr->addQuotes( $self->getDBkey() . '/%' );
		
		# Execute the SQL and retrieve a list of pages		
		$sql = "SELECT page_title FROM $page WHERE page_namespace = {$ns} AND page_title LIKE {$like}";
		$res = $dbr->query( $sql, 'efSubpageList' );
		
		# Prepare a bunch of links to the pages
		while( $row = $dbr->fetchObject( $res ) ) {
			$title = Title::makeTitleSafe( $ns, $row->page_title );
			if( is_object( $title ) )
				$links[] = '<li>' . $skin->makeKnownLinkObj( $title, efSubpageListGetText( $title ) ) . '</li>';
		}
		$dbr->freeResult( $res );
		
		# Dump out the HTML
		return count( $links ) ? "<ul>" . implode( "\n", $links ) . "</ul>" : '';
	}
	
	/**
	 * Given a title, e.g. Foo/Bar, return the rightmost segment
	 * Foo/Bar => Bar
	 * Dog/Cat/Mouse => Cat/Mouse
	 */
	function efSubpageListGetText( &$title ) {
		$parts = explode( '/', $title->getText() );
		array_shift( $parts );
		return implode( '/', $parts );
	}

} else {
	echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
	die( -1 );
}

?>