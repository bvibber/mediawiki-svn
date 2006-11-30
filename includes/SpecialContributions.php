<?php

/**
 * Special page allowing users to view their own contributions
 * and those of others.
 *
 * @package MediaWiki
 * @subpackage Special pages
 */

/**
 *
 * @package MediaWiki
 * @subpackage SpecialPage
 */
class ContributionsPage extends QueryPage {
	var $target, $user;
	var $namespace = null;
	var $newbies = false;
	var $botmode = false;

	function ContributionsPage( $target ) {
		// This is an ugly hack.  I don't know who came up with it.
		if ( $target == 'newbies' ) {
			$this->newbies = true;
			$this->target = 'newbies';
			return;
		}

		$this->user = User::newFromName( $target );
		if ( $this->user )
			$this->target = $this->user->getName();
	}

	function getName() {
		return 'Contributions';
	}

	/**
	 * Not expensive, won't work with the query cache anyway.
	 */
	function isExpensive() { return false; }

	function isSyndicated() { return false; }

	function linkParameters() {
		$params['target'] = $this->target;

		if ( isset($this->namespace) )
			$params['namespace'] = $this->namespace;

		if ( $this->botmode )
			$params['bot'] = 1;
			
		return $params;
	}

	function subtitleLinks() {
		global $wgSysopUserBans, $wgLang, $wgUser;

		$sk = $wgUser->getSkin();
		$id = $this->user->getId();

		$userpage = $this->user->getUserPage();
		$userlink = $sk->makeLinkObj( $userpage, $this->target );

		// talk page link
		$tools[] = $sk->makeLinkObj( $userpage->getTalkPage(), $wgLang->getNsText( NS_TALK ) );

		// block or block log link
		if ( ( $id != 0 && $wgSysopUserBans ) || ( $id == 0 && User::isIP( $this->target ) ) ) {
			if( $wgUser->isAllowed( 'block' ) )
				$tools[] = $sk->makeKnownLinkObj( SpecialPage::getTitleFor( 'Blockip', $this->target ),
								  wfMsgHtml( 'blocklink' ) );
			else
				$tools[] = $sk->makeKnownLinkObj( SpecialPage::getTitleFor( 'Log' ),
								  htmlspecialchars( LogPage::logName( 'block' ) ),
								  'type=block&page=' . $userpage->getPrefixedUrl() );
		}

		// other logs link
		$tools[] = $sk->makeKnownLinkObj( SpecialPage::getTitleFor( 'Log' ),
						  wfMsgHtml( 'log' ),
						  'user=' . $userpage->getPartialUrl() );

		return $userlink . ' (' . implode( ' | ', $tools ) . ')';
	}

	function setSubtitle() {
		global $wgOut;
		if ( $this->newbies )
			$wgOut->setSubtitle( wfMsgHtml( 'sp-contributions-newbies-sub' ) );
		else
			$wgOut->setSubtitle( wfMsgHtml( 'contribsub', $this->subtitleLinks() ) );
	}

	function doQuery( $offset, $limit ) {
		// This needs to be checked before doing anything else
		if ( !$this->user && !$this->newbies ) {
			$wgOut->showErrorPage( 'notargettitle', 'notargettext' );
			return;
		}

		// Set output page subtitle
		$this->setSubtitle();

		// OK, everything checks out, let's get on with it
		return parent::doQuery( $offset, $limit );
	}

	function getDeletedcontribsLink() {
		global $wgUser;

		if( $this->newbies || !$wgUser->isAllowed( 'deletedhistory' ) )
			return '';

		$dbr =& wfGetDB( DB_SLAVE );
		$n = $dbr->selectField( 'archive', 'count(*)', array( 'ar_user_text' => $this->target ), __METHOD__ );

		if ( $n == 0 )
			return '';

		$msg = wfMsg( ( $wgUser->isAllowed( 'delete' ) ? 'thisisdeleted' : 'viewdeleted' ),
			      $wgUser->getSkin()->makeKnownLinkObj(
				      SpecialPage::getTitleFor( 'Deletedcontribs', $this->target ),
				      wfMsgExt( 'restorelink', array( 'parsemag', 'escape' ), $n ) ) );

		return '<p>' . $msg . '</p>';
	}

	function getNamespaceForm() {
		$title = $this->getTitle();

		$ns = $this->namespace;
		if ( !isset($ns) )
			$ns = '';

		$form = Xml::openElement( 'form', array( 'method' => 'post', 'action' => $title->getLocalUrl() ) );
		$form .= wfMsgHtml( 'namespace' ) . ' ';
		$form .= Xml::namespaceSelector( $ns, '' ) . ' ';
		$form .= Xml::submitButton( wfMsg( 'allpagessubmit' ) );
		$form .= Xml::hidden( 'offset', $this->offset );
		$form .= Xml::hidden( 'limit',  $this->limit );
		$form .= Xml::hidden( 'target', $this->target );
		if ( $this->botmode )
			$form .= Xml::hidden( 'bot', 1 );
		$form .= '</form>';

		return '<p>' . $form . '</p>';
	}

	function getPageHeader() {
		// hook for Contributionseditcount extension
		if ( $this->user )
			wfRunHooks( 'SpecialContributionsBeforeMainOutput', $this->user->getId() );
		
		return $this->getDeletedcontribsLink() . $this->getNamespaceForm();
	}

	function makeSQLCond( &$dbr ) {
		$cond = '';

		if ( $this->newbies ) {
			$max = $dbr->selectField( 'user', 'max(user_id)', false, 'make_sql' );
			$cond .= ' AND rev_user > ' . (int)($max - $max / 100);
		} else {
			$cond .= ' AND rev_user_text = ' . $dbr->addQuotes( $this->user->getName() );
		}

		if ( isset($this->namespace) )
			$cond .= ' AND page_namespace = ' . (int)$this->namespace;

		return $cond;
	}

	function getSQL() {
		$dbr =& wfGetDB( DB_SLAVE );

		list( $page, $revision ) = $dbr->tableNamesN( 'page', 'revision' );

		$cond = $this->makeSQLCond( $dbr );

		return "SELECT 'Contributions' as type,
				page_namespace AS namespace,
				page_title     AS title,
				rev_timestamp  AS value,
				rev_minor_edit AS is_minor,
				page_is_new    AS is_new,
				page_latest    AS cur_id,
				rev_id         AS rev_id,
				rev_comment    AS comment,
				rev_deleted    AS deleted
			FROM $page,$revision
			WHERE page_id = rev_page {$cond}";
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
			foreach( explode( ' ', 'uctop diff newarticle rollbacklink diff hist minoreditletter' ) as $msg )
				$messages[$msg] = wfMsgExt( $msg, array( 'escape') );
		}

		$page = Title::makeTitle( $row->namespace, $row->title );

		/*
		 * HACK: We need a revision object, so we make a very
		 * heavily stripped-down one.  All we really need are
		 * the comment, the title and the deletion bitmask.
		 */
		$rev = new Revision( array(
			'comment'   => $row->comment,
			'deleted'   => $row->deleted,
			'user_text' => $this->username,
			'user'      => 0,  // whatever, just don't default to $wgUser->getId();
		) );
		$rev->setTitle( $page );

		$ts = wfTimestamp( TS_MW, $row->value );
		$time = $wgLang->timeAndDate( $ts, true );
		$hist = $sk->makeKnownLinkObj( $page, $messages['hist'], 'action=history' );

		if ( $rev->userCan( Revision::DELETED_TEXT ) )
			$diff = $sk->makeKnownLinkObj( $page, $messages['diff'], 'diff=prev&oldid=' . $row->rev_id );
		else
			$diff = $messages['diff'];

		if( $row->minor )
			$mflag = '<span class="minor">' . $messages['minoreditletter'] . '</span> ';
		else
			$mflag = '';

		$link    = $sk->makeKnownLinkObj( $page );
		$comment = $sk->revComment( $rev );

		$notes = '';

		if( $row->rev_id == $row->cur_id ) {
			$notes .= ' <strong>' . $messages['uctop'] . '</strong>';

			if( $wgUser->isAllowed( 'rollback' ) )
				$notes .= ' ' . $sk->generateRollback( $rev );
		}
		
		if( $rev->isDeleted( Revision::DELETED_TEXT ) ) {
			$time = '<span class="history-deleted">' . $time . '</span>';
			$notes .= ' ' . wfMsgHtml( 'deletedrev' );
		}
		
		return "{$time} ({$hist}) ({$diff}) {$mflag} {$dm}{$link} {$comment}{$notes}";
	}
}

/**
 *
 */
function wfSpecialContributions( $par = null ) {
	global $wgRequest, $wgUser;

	$page = new ContributionsPage( isset($par) ? $par : $wgRequest->getVal( 'target' ) );

	$page->namespace = $wgRequest->getIntOrNull( 'namespace' );
	$page->botmode   = ( $wgUser->isAllowed( 'rollback' ) && $wgRequest->getBool( 'bot' ) );
	
	list( $limit, $offset ) = wfCheckLimits();
	return $page->doQuery( $offset, $limit );
}

?>
