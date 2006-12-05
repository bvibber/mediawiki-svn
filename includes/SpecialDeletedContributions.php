<?php

/**
 * Special page allowing privileged users to view the deleted
 * contributions of a user.
 *
 * @package MediaWiki
 * @subpackage Special pages
 */
class DeletedContributionsPage extends ContributionsPage {

	function getName() {
		return 'DeletedContributions';
	}

	function getDeletedContributionsLink() {
		return '';  // no self-links please
	}

	function makeSQLCond( $dbr ) {
		$cond = ' ar_user_text = ' . $dbr->addQuotes( $this->getUsername() );

		if ( isset($this->namespace) )
			$cond .= ' AND ar_namespace = ' . (int)$this->namespace;

		return $cond;
	}

	function getSQL() {
		$dbr = wfGetDB( DB_SLAVE );

		$archive = $dbr->tableName( 'archive' );

		return "SELECT 'DeletedContributions' as type,
				ar_namespace  AS namespace,
				ar_title      AS title,
				ar_timestamp  AS value,
				ar_minor_edit AS is_minor,
				ar_rev_id     AS rev_id,
				ar_comment    AS comment
			FROM $archive
			WHERE " . $this->makeSQLCond( $dbr );
	}

	function preprocessResults( $dbr, $res ) {
		// Do a batch existence check
		$linkBatch = new LinkBatch();
		while( $row = $dbr->fetchObject( $res ) ) {
			$linkBatch->add( $row->namespace, $row->title );
		}
		$linkBatch->execute();

		// Seek to start
		if( $dbr->numRows( $res ) > 0 )
			$dbr->dataSeek( $res, 0 );
	}

	/**
	 * Format a row, providing the timestamp, links to the
	 * page/diff/history and a comment
	 *
	 * @param $skin Skin to use
	 * @param $row Result row
	 * @return string
	 */
	function formatResult( $skin, $row ) {
		global $wgLang, $wgContLang, $wgUser;

		$dm = $wgContLang->getDirMark();

		// Cache UI messages in a static array so we don't
		// have to regenerate them for each row.
		static $messages;
		if( !isset( $messages ) ) {
			foreach( explode( ' ', 'deletedcontribs-list deletedcontribs-view minoreditletter' ) as $msg ) {
				$messages[$msg] = wfMsgExt( $msg, array( 'escape') );
			}
		}
		// Cache Special:Undelete page title
		static $ut;
		if( !isset( $ut ) )
			$ut = SpecialPage::getTitleFor( 'Undelete' );
		
		$page = Title::makeTitle( $row->namespace, $row->title );

		$ts = wfTimestamp( TS_MW, $row->value );
		$pg = $page->getPrefixedUrl();

		$time = $wgLang->timeAndDate( $ts, true );
		
		if( $row->minor )
			$mflag = '<span class="minor">' . $messages['minoreditletter'] . '</span> ';
		else
			$mflag = '';

		$list = $skin->makeKnownLinkObj( $ut, $messages['deletedcontribs-list'], "target=$pg" );
		$view = $skin->makeKnownLinkObj( $ut, $messages['deletedcontribs-view'], "target=$pg&timestamp=$ts" );

		$pglink  = $skin->makeLinkObj( $page );
		$comment = $skin->commentBlock( $row->comment );

		return "{$time} ({$list}) ({$view}) {$mflag} {$dm}{$pglink} {$comment}";
	}
}

/**
 *
 */
function wfSpecialDeletedContributions( $par = null ) {
	global $wgRequest, $wgOut;

	$username = ( isset($par) ? $par : $wgRequest->getVal( 'target' ) );

	$page = new DeletedContributionsPage( $username );

	if( !$page->user ) {
		$wgOut->showErrorPage( 'notargettitle', 'notargettext' );
		return;
	}

	$page->namespace = $wgRequest->getIntOrNull( 'namespace' );
	
	list( $limit, $offset ) = wfCheckLimits();
	return $page->doQuery( $offset, $limit );
}

?>
