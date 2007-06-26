<?php

/**
 * Quickie special page to search the external-links table.
 * Currently only 'http' links are supported; LinkFilter needs to be
 * changed to allow other pretties.
 */

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Linksearch',
	'author' => 'Brion Vibber',
	'description' => 'Search for Weblinks',
	'url' => 'http://www.mediawiki.org/wiki/Extension:LinkSearch',
);
$wgHooks['LoadAllMessages'][] = 'wfLinkSearchLoadMessages';

$wgSpecialPages['Linksearch'] = array( /*class*/ 'SpecialPage', 
	/*name*/ 'Linksearch', /* permission */'', /*listed*/ true, 
	/*function*/ false, /*file*/ false );
$wgAutoloadClasses['LinkSearchPage'] = dirname(__FILE__) . '/LinkSearch_body.php';

function wfLinkSearchLoadMessages() {
	static $done = false;
	if ( $done ) {
		return true;
	}
	$done = true;
	require_once( dirname(__FILE__).'/LinkSearch.i18n.php' );
	global $wgMessageCache;
	foreach( $wgLinkSearchMessages as $lang => $messages ) {
		$wgMessageCache->addMessages( $messages, $lang );
	}
	return true;
}

function wfSpecialLinksearch( $par=null, $ns=null ) {
	list( $limit, $offset ) = wfCheckLimits();
	global $wgOut, $wgRequest, $wgUrlProtocols, $wgMiserMode;
	$target = $GLOBALS['wgRequest']->getVal( 'target', $par );
	$namespace = $GLOBALS['wgRequest']->getIntorNull( 'namespace', $ns );

	$protocols_list[] = '';
	foreach( $wgUrlProtocols as $prot ) {
		$protocols_list[] = $prot;
	}

	$target2 = $target;
	$protocol = '';
	$pr_sl = strpos($target2, '//' );
	$pr_cl = strpos($target2, ':' );
	if ( $pr_sl ) {
		// For protocols with '//'
		$protocol = substr( $target2, 0 , $pr_sl+2 );
		$target2 = substr( $target2, $pr_sl+2 );
	} elseif ( !$pr_sl && $pr_cl ) {
		// For protocols without '//' like 'mailto:'
		$protocol = substr( $target2, 0 , $pr_cl+1 );
		$target2 = substr( $target2, $pr_cl+1 );
	} elseif ( $protocol == '' && $target2 != '' ) {
		// default
		$protocol = 'http://';
	}
	if ( !in_array( $protocol, $protocols_list ) ) {
		// unsupported protocol, show original search request
		$target2 = $target;
		$protocol = '';
	}

	$self = Title::makeTitle( NS_SPECIAL, 'Linksearch' );

	$wgOut->addWikiText( wfMsg( 'linksearch-text', '<nowiki>' . implode( ', ',  $wgUrlProtocols) . '</nowiki>' ) );
	$s =	Xml::openElement( 'form', array( 'id' => 'mw-linksearch-form', 'method' => 'get', 'action' => $GLOBALS['wgScript'] ) ) .
		Xml::hidden( 'title', $self->getPrefixedDbKey() ) .
		'<fieldset>' .
		Xml::element( 'legend', array(), wfMsg( 'linksearch' ) ) .
		Xml::label( wfMsg( 'linksearch-pat' ), 'target' ) . ' ' .
		Xml::input( 'target', 50 , $target ) . ' ';
	if ( !$wgMiserMode ) {
		$s .= Xml::label( wfMsg( 'linksearch-ns' ), 'namespace' ) .
			XML::namespaceSelector( $namespace, '' );
	}
	$s .=	Xml::submitButton( wfMsg( 'linksearch-ok' ) ) .
		'</fieldset>' .
		Xml::closeElement( 'form' );
	$wgOut->addHtml( $s );

	if( $target != '' ) {
		$searcher = new LinkSearchPage( $target2, $namespace, $protocol );
		$searcher->doQuery( $offset, $limit );
	}
}

?>
