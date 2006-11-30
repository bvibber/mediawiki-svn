<?php

/**
 * Special page allowing privileged users to view the deleted
 * contributions of a user.
 *
 * @package MediaWiki
 * @subpackage Special pages
 */

/**
 *
 * @package MediaWiki
 * @subpackage SpecialPage
 */
class DeletedcontribsPage extends ContributionsPage {

	function getName() {
		return 'Deletedcontribs';
	}

	// no hax please
	function newbiesTargetName() {
		return 'newbies';
	}

	function getPageHeader() {
		$this->setSubtitle();
		return $this->getNamespaceForm();
	}

	function makeSQLCond( $dbr ) {
		$cond = ' ar_user_text = ' . $dbr->addQuotes( $this->target );
		if ( isset($this->namespace) )
			$cond .= ' AND ar_namespace = ' . (int)$this->namespace;
		return $cond;
	}

	function getSQL() {
		$dbr = wfGetDB( DB_SLAVE );

		$archive = $dbr->tableName( 'archive' );

		$cond = $this->makeSQLCond( $dbr );

		return "SELECT 'Deletedcontribs' as type,
				ar_namespace  AS namespace,
				ar_title      AS title,
				ar_timestamp  AS value,
				ar_minor_edit AS is_minor,
				ar_rev_id     AS rev_id,
				ar_comment    AS comment
			FROM $archive
			WHERE {$cond}";
	}

	function preprocessResults( $dbr, $res ) {
		# Do a batch existence check
		$linkBatch = new LinkBatch();
		while( $row = $dbr->fetchObject( $res ) ) {
			$linkBatch->addObj( Title::makeTitle( $row->namespace, $row->title ) );
		}
		$linkBatch->execute();
		# Seek to start
		if( $dbr->numRows( $res ) > 0 )
			$dbr->dataSeek( $res, 0 );
	}

	/**
	 * Format a row, providing the timestamp, links to the
	 * page/diff/history and a comment
	 *
	 * @param $sk Skin to use
	 * @param $row Result row
	 * @return string
	 */
	function formatResult( $sk, $row ) {
		global $wgLang, $wgContLang, $wgUser;

		$dm = $wgContLang->getDirMark();

		/*
		 * Cache UI messages in a static array so we don't
		 * have to regenerate them for each row.
		 */
		static $messages;
		if( !isset( $messages ) ) {
			foreach( explode( ' ', 'deletedcontribs-undelete deletedcontribs-show minoreditletter' ) as $msg ) {
				$messages[$msg] = wfMsgExt( $msg, array( 'escape') );
			}
		}

		/*
		 * Cache Special:Undelete page title.
		 */
		static $ut;
		if( !isset( $utu ) )
			$ut =& SpecialPage::getTitleFor( 'Undelete' );
		
		$page = Title::makeTitle( $row->namespace, $row->title );

		$ts = wfTimestamp( TS_MW, $row->value );
		$pg = $page->getPrefixedUrl();

		$time = $wgLang->timeAndDate( $ts, true );
		
		if( $row->minor )
			$mflag = '<span class="minor">' . $messages['minoreditletter'] . '</span> ';
		else
			$mflag = '';

		$undel = $sk->makeKnownLinkObj( $ut, $messages['deletedcontribs-undelete'], "target=$pg" );
		$show  = $sk->makeKnownLinkObj( $ut, $messages['deletedcontribs-show'], "target=$pg&timestamp=$ts" );

		$pglink  = $sk->makeLinkObj( $page );
		$comment = $sk->commentBlock( $row->comment );

		return "{$time} ({$undel}) ({$show}) {$mflag} {$dm}{$pglink} {$comment}";
	}
}

/**
 *
 */
function wfSpecialDeletedcontribs( $par = null ) {
	global $wgRequest;

	$username = ( isset($par) ? $par : $wgRequest->getVal( 'target' ) );

	if( !isset($username) || $username == '' ) {
		global $wgOut;
		$wgOut->showErrorPage( 'notargettitle', 'notargettext' );
		return;
	}

	$page = new DeletedcontribsPage( $username );

	$page->namespace = $wgRequest->getIntOrNull( 'namespace' );
	
	list( $limit, $offset ) = wfCheckLimits();
	return $page->doQuery( $offset, $limit );
}

?>
