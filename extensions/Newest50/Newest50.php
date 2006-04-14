<?php

/**
 * Special page to show the last 50 pages added to the wiki
 * This doesn't use recent changes so the items don't expire
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	require_once( 'SpecialPage.php' );
	$wgExtensionFunctions[] = 'efNewest50';
	
	function efNewest50() {
		global $wgMessageCache;
		SpecialPage::addPage( new Newest50() );
		$wgMessageCache->addMessage( 'newest50', 'Newest 50' );
		$wgMessageCache->addMessage( 'newest50-header', "'''This page lists the 50 newest pages on the wiki.'''" );
		$wgMessageCache->addMessage( 'newest50-showing', 'Found $1 pages; listing newest first:' );
		$wgMessageCache->addMessage( 'newest50-lastedit', 'last edited $1' );
		$wgMessageCache->addMessage( 'newest50-none', 'No entries were found.' );
	}
	
	class Newest50 extends SpecialPage {
	
		function Newest50() {
			SpecialPage::SpecialPage( 'Newest50' );
		}
	
		function execute() {
			global $wgOut;
			$this->setHeaders();
			$wgOut->addWikiText( wfMsg( 'newest50-header' ) );
			$dbr =& wfGetDB( DB_SLAVE );
			$res = $dbr->query( "SELECT page_namespace, page_title, page_touched FROM page ORDER BY page_id DESC LIMIT 0,50" );
			$count = $dbr->numRows( $res );
			if( $count > 0 ) {
				# Make list
				$wgOut->addWikiText( wfMsg( 'newest50-showing', $count ) );
				$wgOut->addHTML( "<ol>\n" );
				while( $row = $dbr->fetchObject( $res ) )
					$wgOut->addHTML( $this->makeListItem( $row ) );
				$wgOut->addHTML( "</ol>\n" );
			} else {
				$wgOut->addWikiText( wfMsg( 'newest50-none' ) );
			}
			$dbr->freeResult( $res );			
		}
		
		function makeListItem( $row ) {
			global $wgUser, $wgLang;
			$title = Title::makeTitleSafe( $row->page_namespace, $row->page_title );
			if( !is_null( $title ) ) {
				$skin = $wgUser->getSkin();
				$link = $skin->makeKnownLinkObj( $title );
				$lastEdit = wfMsgHtml( 'newest50-lastedit', $wgLang->timeAndDate( $row->page_touched ) );
				return( "<li>{$link} ({$lastEdit})</li>\n" );
			} else {
				return( "<!-- Invalid title " . htmlspecialchars( $row->page_title ) . " in namespace " . htmlspecialchars( $row->page_namespace ) . " -->\n" );
			}
		}
	
	}

} else {
	echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
	die( -1 );
}

?>