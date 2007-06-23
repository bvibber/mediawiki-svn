<?php

/**
 * Special page lists new pages on the wiki
 *
 * @addtogroup SpecialPage
 */
class SpecialNewPages extends SpecialPage {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'Newpages' );
	}
	
	/**
	 * Main execution function
	 *
	 * @param mixed $par Parameter passed to the page
	 */
	public function execute( $par = false ) {
		global $wgOut, $wgRequest;
		$this->setHeaders();
		
		$namespace = $wgRequest->getVal( 'namespace', NS_MAIN );
		$username = $wgRequest->getText( 'username' );
		
		$wgOut->addHtml( $this->buildFilterUI( $namespace, $username ) );
		
		$pager = new NewPagesPager( $namespace, $username );
		if( $pager->getNumRows() > 0 ) {
			$wgOut->addHtml(
				$pager->getNavigationBar()
				. $pager->getBody()
				. $pager->getNavigationBar()
			);
		} else {
			# ???
		}		
	}
	
	/**
	 * Build the namespace/username filtering from
	 *
	 * @param mixed $namespace
	 * @param mixed $username
	 * @return string
	 */
	private function buildFilterUI( $namespace, $username ) {
		$self = SpecialPage::getTitleFor( 'Newpages' );
		$form = Xml::openElement( 'form', array( 'method' => 'post', 'action' => $self->getLocalUrl() ) );
		# Namespace selector
		$form .= '<table><tr><td align="right">' . Xml::label( wfMsg( 'namespace' ), 'namespace' ) . '</td>';
		$form .= '<td>' . Xml::namespaceSelector( $namespace, 'all' ) . '</td></tr>';
		# Username filter
		$form .= '<tr><td align="right">' . Xml::label( wfMsg( 'newpages-username' ), 'mw-np-username' ) . '</td>';
		$form .= '<td>' . Xml::input( 'username', 30, $username, array( 'id' => 'mw-np-username' ) ) . '</td></tr>';
		$form .= '<tr><td></td><td>' . Xml::submitButton( wfMsg( 'allpagessubmit' ) ) . '</td></tr></table>';
		$form .= '</form>';
		return $form;
	}

}


/**
 *
 * @addtogroup SpecialPage
 */

/**
 * implements Special:Newpages
 * @addtogroup SpecialPage
 */
/*class NewPagesPage extends QueryPage {

	var $namespace;
	var $username = '';

	function NewPagesPage( $namespace = NS_MAIN, $username = '' ) {
		$this->namespace = $namespace;
		$this->username = $username;
	}

	function getName() {
		return 'Newpages';
	}

	function isExpensive() {
		# Indexed on RC, and will *not* work with querycache yet.
		return false;
	}

	function makeUserWhere( &$dbo ) {
		$title = Title::makeTitleSafe( NS_USER, $this->username );
		if( $title ) {
			return ' AND rc_user_text = ' . $dbo->addQuotes( $title->getText() );
		} else {
			return '';
		}
	}

	private function makeNamespaceWhere() {
		return $this->namespace !== 'all'
			? ' AND rc_namespace = ' . intval( $this->namespace )
			: '';
	}

	function preprocessResults( &$dbo, &$res ) {
		# Do a batch existence check on the user and talk pages
		$linkBatch = new LinkBatch();
		while( $row = $dbo->fetchObject( $res ) ) {
			$linkBatch->addObj( Title::makeTitleSafe( NS_USER, $row->user_text ) );
			$linkBatch->addObj( Title::makeTitleSafe( NS_USER_TALK, $row->user_text ) );
		}
		$linkBatch->execute();
		# Seek to start
		if( $dbo->numRows( $res ) > 0 )
			$dbo->dataSeek( $res, 0 );
	}

	function formatResult( $skin, $result ) {
		global $wgLang, $wgContLang;
		$dm = $wgContLang->getDirMark();

		$title = Title::makeTitleSafe( $result->namespace, $result->title );
		$time = $wgLang->timeAndDate( $result->timestamp, true );
		$plink = $skin->makeKnownLinkObj( $title, '', $this->patrollable( $result ) ? 'rcid=' . $result->rcid : '' );
		$hist = $skin->makeKnownLinkObj( $title, wfMsgHtml( 'hist' ), 'action=history' );
		$length = wfMsgExt( 'nbytes', array( 'parsemag', 'escape' ), $wgLang->formatNum( htmlspecialchars( $result->length ) ) );
		$ulink = $skin->userLink( $result->user, $result->user_text ) . ' ' . $skin->userToolLinks( $result->user, $result->user_text );
		$comment = $skin->commentBlock( $result->comment );

		return "{$time} {$dm}{$plink} ({$hist}) {$dm}[{$length}] {$dm}{$ulink} {$comment}";
	}

	function patrollable( $result ) {
		global $wgUser, $wgUseRCPatrol;
		return $wgUseRCPatrol && $wgUser->isAllowed( 'patrol' ) && !$result->patrolled;
	}

	function feedItemDesc( $row ) {
		if( isset( $row->rev_id ) ) {
			$revision = Revision::newFromId( $row->rev_id );
			if( $revision ) {
				return '<p>' . htmlspecialchars( wfMsg( 'summary' ) ) . ': ' .
					htmlspecialchars( $revision->getComment() ) . "</p>\n<hr />\n<div>" .
					nl2br( htmlspecialchars( $revision->getText() ) ) . "</div>";
			}
		}
		return parent::feedItemDesc( $row );
	}
	
	function getPageHeader() {
		$self = SpecialPage::getTitleFor( $this->getName() );
		$form = Xml::openElement( 'form', array( 'method' => 'post', 'action' => $self->getLocalUrl() ) );
		# Namespace selector
		$form .= '<table><tr><td align="right">' . Xml::label( wfMsg( 'namespace' ), 'namespace' ) . '</td>';
		$form .= '<td>' . Xml::namespaceSelector( $this->namespace, 'all' ) . '</td></tr>';
		# Username filter
		$form .= '<tr><td align="right">' . Xml::label( wfMsg( 'newpages-username' ), 'mw-np-username' ) . '</td>';
		$form .= '<td>' . Xml::input( 'username', 30, $this->username, array( 'id' => 'mw-np-username' ) ) . '</td></tr>';
		
		$form .= '<tr><td></td><td>' . Xml::submitButton( wfMsg( 'allpagessubmit' ) ) . '</td></tr></table>';
		$form .= Xml::hidden( 'offset', $this->offset ) . Xml::hidden( 'limit', $this->limit ) . '</form>';
		return $form;
	}
	
	function linkParameters() {
		return( array( 'namespace' => $this->namespace, 'username' => $this->username ) );
	}
	
}*/

/**
 * constructor
 */
/*function wfSpecialNewpages($par, $specialPage) {
	global $wgRequest, $wgContLang;

	list( $limit, $offset ) = wfCheckLimits();
	$namespace = NS_MAIN;
	$username = '';

	if ( $par ) {
		$bits = preg_split( '/\s*,\s*'/', trim( $par ) );
		foreach ( $bits as $bit ) {
			if ( 'shownav' == $bit )
				$shownavigation = true;
			if ( is_numeric( $bit ) )
				$limit = $bit;

			$m = array();
			if ( preg_match( '/^limit=(\d+)$/', $bit, $m ) )
				$limit = intval($m[1]);
			if ( preg_match( '/^offset=(\d+)$/', $bit, $m ) )
				$offset = intval($m[1]);
			if ( preg_match( '/^namespace=(.*)$/', $bit, $m ) ) {
				$ns = $wgContLang->getNsIndex( $m[1] );
				if( $ns !== false ) {
					$namespace = $ns;
				}
			}
		}
	} else {
		if( $ns = $wgRequest->getText( 'namespace', NS_MAIN ) )
			$namespace = $ns;
		if( $un = $wgRequest->getText( 'username' ) )
			$username = $un;
	}
	
	if ( ! isset( $shownavigation ) )
		$shownavigation = ! $specialPage->including();

	$npp = new NewPagesPage( $namespace, $username );

	if ( ! $npp->doFeed( $wgRequest->getVal( 'feed' ), $limit ) )
		$npp->doQuery( $offset, $limit, $shownavigation );
}*/

?>
