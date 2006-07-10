<?php
/**
 * @package MediaWiki
 * @subpackage SpecialPage
 */

function wfSpecialSnapshot( $par ) {
	global $wgRequest, $wgOut, $wgUser;
	
	if( $wgRequest->wasPosted() ) {
		$page = $wgRequest->getText( 'wpTarget' );
		$revId = $wgRequest->getInt( 'wpRevision' );
		$tag = $wgRequest->getVal( 'wpTag' );
		
		$title = Title::newFromText( $page );
		$rev = Revision::newFromTitle( $title, $revId );
		$text = $rev->getText();
		$parserOptions = ParserOptions::newFromUser( $temp = NULL );
		
		global $wgParser;
		$parserOutput = $wgParser->parse( $text, $title,
			$parserOptions, true, true,
			$revId );
		
		$snap = $parserOutput->getSnapshot();
		$snapId = $snap->insertTag( $rev->getPage(), $revId, $tag );
		
		$wgOut->addWikiText( "Saved tag snapshot id $snapId" );
	}
	
	// Quick hackie test form
	$special = Title::makeTitle( NS_SPECIAL, 'Snapshot' );
	$wgOut->addHtml(
		Xml::openElement( 'form', array(
			'method' => 'post',
			'action' => $special->getLocalUrl() ) ) .
		Xml::inputLabel( 'Snapshot page:', 'wpTarget', 'wpTarget', 40, $par ) .
		'<br />' .
		Xml::inputLabel( 'Revision id:', 'wpRevision', 'wpRevision', 20 ) .
		'<br />' .
		Xml::inputLabel( 'Tag name:', 'wpTag', 'wpTag', 16, 'reviewed' ) .
		'<br />' .
		Xml::submitButton( 'Save snapshot' ) .
		Xml::hidden( 'wpEditToken', $wgUser->editToken() ) .
		Xml::closeElement( 'form' ) );
}

?>
