<?php

/**
 * See http://www.mediawiki.org/wiki/Extension:Oversight
 * - Add a "permanently hide this revision" link on old revision / diff view
 * - Goes to a tool to slurp revisions into an alternate archive table
 * - Add a log for this
 *
 * Copyright (C) 2006 Brion Vibber <brion@pobox.com>
 * http://www.mediawiki.org/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

$wgAvailableRights = array_merge( $wgAvailableRights, array( 'hiderevision', 'oversight' ) );

// The 'hiderevision' permission allows use of revision hiding.
$wgGroupPermissions['*']['hiderevision'] = false;

// 'oversight' permission is required to view a previously-hidden revision.
$wgGroupPermissions['*']['oversight'] = false;

// You could add a group like this:
// $wgGroupPermissions['censor']['hiderevision'] = true;
// $wgGroupPermissions['quiscustodiet']['oversight'] = true;

$wgExtensionFunctions[] = 'hrSetup';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Oversight',
	'version' => '1.1',
	'author' => 'Brion Vibber',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Oversight',
	'description' => 'Hide individual revisions from all users for legal reasons, etc.',
);

$wgSpecialPages['HideRevision'] = array( 'SpecialPage', 'HideRevision', 'hiderevision',
		/*listed*/ true, /*function*/ false, /*file*/ false );
$wgSpecialPages['Oversight'] = array( 'SpecialPage', 'Oversight', 'oversight',
		/*listed*/ true, /*function*/ false, /*file*/ false );

/**
 * Setup function for HideRevision extension.
 * Adds the special page for the action form.
 */
function hrSetup() {
	$GLOBALS['wgHooks']['ArticleViewHeader'][] = 'hrArticleViewHeaderHook';
	$GLOBALS['wgHooks']['DiffViewHeader'][] = 'hrDiffViewHeaderHook';
	$GLOBALS['wgHooks']['UndeleteShowRevision'][] = 'hrUndeleteShowRevisionHook';

	require_once( dirname( __FILE__ ) . '/HideRevision.i18n.php' );
	foreach( efHideRevisionMessages() as $lang => $messages )
		$GLOBALS['wgMessageCache']->addMessages( $messages, $lang );
}

/**
 * Hook for article view, giving us a chance to insert a removal
 * tab on old version views.
 */
function hrArticleViewHeaderHook( $article ) {
	$oldid = intval( $article->mOldId );
	if( $oldid ) {
		hrInstallTab( $oldid );
	}
	return true;
}

/**
 * Hook for diff view, giving us a chance to insert a removal
 * tab on old version views.
 */
function hrDiffViewHeaderHook( $diff, $oldRev, $newRev ) {
	if( !empty( $newRev ) && $newRev->getId() ) {
		hrInstallTab( $newRev->getId() );
	}
	return true;
}

/**
 * Hook for deletion archive revision view, giving us a chance to
 * insert a removal tab for a deleted revision.
 */
function hrUndeleteShowRevisionHook( $title, $rev ) {
	hrInstallArchiveTab( $title, $rev->getTimestamp() );
	return true;
}

class HideRevisionTabInstaller {
	function __construct( $linkParam ) {
		$this->mLinkParam = $linkParam;
	}

	function insertTab( $skin, &$content_actions ) {
		$special = Title::makeTitle( NS_SPECIAL, 'HideRevision' );
			$content_actions['hiderevision'] = array(
				'class' => false,
				'text' => wfMsgHTML( 'hiderevision-tab' ),
				'href' => $special->getLocalUrl( $this->mLinkParam ) );
			return true;
	}
}

/**
 * If the user is allowed, installs a tab hook on the skin
 * which links to a handy permanent removal thingy.
 */
function hrInstallTab( $id ) {
	global $wgUser;
	if( $wgUser->isAllowed( 'hiderevision' ) ) {
		global $wgHooks;
		$tab = new HideRevisionTabInstaller( 'revision[]=' . $id );
		$wgHooks['SkinTemplateTabs'][] = array( $tab, 'insertTab' );
	}
}

/**
 * If the user is allowed, installs a tab hook on the skin
 * which links to a handy permanent removal thingy for
 * archived (deleted) pages.
 */
function hrInstallArchiveTab( $target, $timestamp ) {
	global $wgUser;
	if( $wgUser->isAllowed( 'hiderevision' ) ) {
		global $wgHooks;
		$tab = new HideRevisionTabInstaller(
			'target=' . $target->getPrefixedUrl() .
			'&timestamp[]=' . $timestamp );
		$wgHooks['SkinTemplateBuildContentActionUrlsAfterSpecialPage'][] =
			array( $tab, 'insertTab' );
	}
}

/**
 * Special page handler function for Special:HideRevision
 */
function wfSpecialHideRevision( $par=null ) {
	global $wgRequest;
	$form = new HideRevisionForm( $wgRequest );
	$form->run();
}

class HideRevisionForm {
	function __construct( $request ) {
		global $wgUser;

		// For live revisions
		$this->mRevisions = (array)$request->getIntArray( 'revision' );

		// For deleted/archived revisions
		$this->mTarget = Title::newFromUrl( $request->getVal( 'target' ) );
		$this->mTimestamps = (array)$request->getArray( 'timestamp' );
		if( is_null( $this->mTarget ) ) {
			// title and timestamps must go together
			$this->mTimestamps = array();
		}

		$this->mPopulated =
			!empty( $this->mRevisions ) ||
			!empty( $this->mTimestamps );

		$this->mReason = $request->getText( 'wpReason' );

		$this->mSubmitted = $request->wasPosted() &&
			$request->getVal( 'action' ) == 'submit' &&
			$wgUser->matchEditToken( $request->getVal( 'wpEditToken' ) );
	}

	function run() {
		if( $this->mPopulated && $this->mSubmitted ) {
			$this->submit();
		} elseif( $this->mPopulated ) {
			$this->showForm();
		} else {
			$this->showEmpty();
		}
	}

	/**
	 * If no revisions are specified, prompt for a revision id
	 */
	function showEmpty() {
		global $wgOut, $wgUser;
		$special = Title::makeTitle( NS_SPECIAL, 'HideRevision' );

		$wgOut->addHtml(
			wfOpenElement( 'form', array(
				'action' => $special->getLocalUrl(),
				'method' => 'post' ) ) .

			// Visible fields
			wfInputLabel( wfMsgHTML( 'hiderevision-prompt' ), 'revision[]', 'wpRevision', 10 ) .
			"<br />" .
			wfInputLabel( wfMsgHTML( 'hiderevision-reason' ), 'wpReason', 'wpReason', 60 ) .
			"<br />" .
			wfSubmitButton( wfMsgHTML( 'hiderevision-continue' ) ) .

			wfCloseElement( 'form' ) );
	}

	/**
	 * Once a set of revisions have been selected,
	 * list them and request a reason/comment for confirmation.
	 */
	function showForm() {
		global $wgOut, $wgUser;
		$special = Title::makeTitle( NS_SPECIAL, 'HideRevision' );

		$wgOut->addWikiText( wfMsg( 'hiderevision-text' ) );
		$wgOut->addHtml(
			$this->revisionList() .

			$this->archiveList() .

			wfOpenElement( 'form', array(
				'action' => $special->getLocalUrl( 'action=submit' ),
				'method' => 'post' ) ) .

			// Visible fields
			"<br />" .
			wfInputLabel( wfMsgHTML( 'hiderevision-reason' ), 'wpReason', 'wpReason', 60, $this->mReason ) .
			"<br />" .
			wfSubmitButton( wfMsgHTML( 'hiderevision-submit' ) ) .

			// Hidden fields
			$this->revisionFields() .
			wfHidden( 'wpEditToken', $wgUser->editToken() ) .

			wfCloseElement( 'form' ) );
	}

	function revisionList() {
		if( !$this->mRevisions ) {
			return '';
		}

		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->select(
			array( 'page', 'revision' ),
			'*, 0 AS rc_id, 1 AS rc_patrolled, 0 AS counter, 0 AS rc_old_len, 0 AS rc_new_len,
			NULL AS rc_log_action, 0 AS rc_deleted, 0 AS rc_logid, NULL AS rc_log_type, "" AS rc_params',
			array(
				'rev_id' => $this->mRevisions,
				'rev_page=page_id',
			),
			__METHOD__ );

		return $this->makeList( $dbr->resultObject( $result ) );
	}

	function makeList( $resultSet ) {
		global $IP, $wgUser;
		require_once( "$IP/includes/ChangesList.php" );
		$changes = ChangesList::newFromUser( $wgUser );

		$skin = $wgUser->getSkin();

		$out = $changes->beginRecentChangesList();
		while( $row = $resultSet->fetchObject() ) {
			$rc = RecentChange::newFromCurRow( $row );
			$rc->counter = 0; // ???
			$out .= $changes->recentChangesLine( $rc );
		}
		$out .= $changes->endRecentChangesList();

		$resultSet->free();
		return $out;
	}

	function archiveList() {
		if( !$this->mTarget || !$this->mTimestamps ) {
			return '';
		}

		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->select(
			array( 'archive' ),
			array(
				'ar_namespace AS page_namespace',
				'ar_title AS page_title',
				'ar_comment AS rev_comment',
				'ar_user AS rev_user',
				'ar_user_text AS rev_user_text',
				'ar_timestamp AS rev_timestamp',
				'ar_minor_edit AS rev_minor_edit',
				'ar_rev_id AS rev_id',
				'0 AS rc_id',
				'1 AS rc_patrolled',
				'0 AS counter',
				'0 AS page_id',
				'0 AS page_is_new',
				'0 AS rc_old_len',
				'0 AS rc_new_len',
				'0 AS rc_deleted',
				'0 AS rc_logid',
				'NULL AS rc_log_type',
				'NULL AS rc_log_action',
				'"" AS rc_params'
			),
			array(
				'ar_namespace' => $this->mTarget->getNamespace(),
				'ar_title' => $this->mTarget->getDbKey(),
				'ar_timestamp' => $this->mTimestamps,
			),
			__METHOD__ );

		return $this->makeList( $dbr->resultObject( $result ) );
	}

	function revisionFields() {
		$out = '';
		foreach( $this->mRevisions as $id ) {
			$out .= wfHidden( 'revision[]', $id );
		}
		if( $this->mTarget ) {
			$out .= wfHidden( 'target', $this->mTarget->getPrefixedDbKey() );
		}
		foreach( $this->mTimestamps as $timestamp ) {
			$out .= wfHidden( 'timestamp[]', wfTimestamp( TS_MW, $timestamp ) );
		}
		return $out;
	}

	/**
	 * Handle submission of deletion form
	 */
	function submit() {
		global $wgOut;
		if( !$this->mPopulated ) {
			$wgOut->addWikiText( wfMsg( 'hiderevision-norevisions' ) );
			$this->showForm();
		} elseif( empty( $this->mReason ) ) {
			$wgOut->addWikiText( wfMsg( 'hiderevision-noreason' ) );
			$this->showForm();
		} else {
			$dbw = wfGetDB( DB_MASTER );
			$success = hrHideRevisions( $dbw, $this->mTarget,
				$this->mRevisions, $this->mTimestamps, $this->mReason );
			$wgOut->addWikiText( '* ' . implode( "\n* ", $success ) );
		}
	}

}

/**
 * Go kill the revisions and return status information.
 * @param $dbw database
 * @param $title Title
 * @param $revisions array of revision ID numbers
 * @param $timetsamps array of timestamps for archived (deleted) revisions to kill
 * @param $reason comment text for the reason
 * @return array of wikitext strings with success/failure messages
 */
function hrHideRevisions( $dbw, $title, $revisions, $timestamps, $reason ) {
	// Live revisions
	foreach( $revisions as $id ) {
		$success[] = wfMsgHTML( 'hiderevision-status', $id,
			wfMsgHTML (hrHideRevision( $dbw, $id, $reason ) ) );
	}

	// Archived revisions
	foreach( $timestamps as $timestamp ) {
		global $wgLang;
		$success[] = wfMsgHTML( 'hiderevision-archive-status',
			$wgLang->timeanddate( $timestamp ),
			wfMsgHTML( hrHideArchivedRevision( $dbw, $title,
				$timestamp, $reason ) ) );
	}
	return $success;
}

/**
 * Actually go in the database and kill things.
 * @return message key string for success or failure message
 */
function hrHideRevision( $dbw, $id, $reason ) {
	global $wgUser;

	$dbw->begin();

	$rev = Revision::newFromId( $id );
	if( is_null( $rev ) ) {
		$dbw->rollback();
		return 'hiderevision-error-missing';
	}
	if( $rev->isCurrent() ) {
		$dbw->rollback();
		return 'hiderevision-error-current';
	}
	$title = $rev->getTitle();

	// Our tasks:
	// Copy revision to "hidden" table
	hrInsertRevision( $dbw, $title, $rev, $reason );

	if( $dbw->affectedRows() != 1 ) {
		$dbw->rollback();
		return 'hiderevision-error-delete';
	}

	// Remove from "revision"
	$dbw->delete( 'revision', array( 'rev_id' => $id ), __FUNCTION__ );

	// Remove from "recentchanges"
	// The page ID is used to get us a relatively usable index
	$dbw->delete( 'recentchanges',
		array(
			'rc_cur_id'     => $rev->getPage(),
			'rc_this_oldid' => $id
		),
		__FUNCTION__ );

	// Invalidate cache of page history
	$title->invalidateCache();

	// Done with all database pieces; commit!
	$dbw->immediateCommit();

	// Also purge remote proxies.
	// Ideally this would be built into the above, but squid code is
	// old crappy style.
	global $wgUseSquid;
	if ( $wgUseSquid ) {
		// Send purge
		$update = SquidUpdate::newSimplePurge( $title );
		$update->doUpdate();
	}

	return 'hiderevision-success';
}

function hrHideArchivedRevision( $dbw, $title, $timestamp, $reason ) {
	$archive = new PageArchive( $title );
	$rev = $archive->getRevision( $timestamp );
	if( !$rev ) {
		$dbw->rollback();
		return 'hiderevision-error-missing';
	}

	hrInsertRevision( $dbw, $title, $rev, $reason );
	if( $dbw->affectedRows() != 1 ) {
		$dbw->rollback();
		return 'hiderevision-error-delete';
	}

	$dbw->delete( 'archive', array(
		'ar_namespace' => $title->getNamespace(),
		'ar_title'     => $title->getDbKey(),
		'ar_timestamp' => $timestamp ),
		__METHOD__ );

	$dbw->commit();
	return 'hiderevision-success';
}

function hrInsertRevision( $dbw, $title, $rev, $reason ) {
	global $wgUser;
	return $dbw->insert( 'hidden',
		array(
			'hidden_page'       => $rev->getPage(),
			'hidden_namespace'  => $title->getNamespace(),
			'hidden_title'      => $title->getDbKey(),

			'hidden_rev_id'     => $rev->getId(),
			'hidden_text_id'    => $rev->getTextId(),

			'hidden_comment'    => $rev->getRawComment(),
			'hidden_user'       => $rev->getRawUser(),
			'hidden_user_text'  => $rev->getRawUserText(),
			'hidden_timestamp'  => $dbw->timestamp( $rev->getTimestamp() ),
			'hidden_minor_edit' => $rev->isMinor() ? 1 : 0,
			'hidden_deleted'    => $rev->mDeleted, // FIXME: private field access

			'hidden_by_user'      => $wgUser->getId(),
			'hidden_on_timestamp' => $dbw->timestamp(),
			'hidden_reason'       => $reason,
		),
		__FUNCTION__,
		array( 'IGNORE' ) );
}

/**
 * Special page handler function for Special:Oversight
 */
function wfSpecialOversight( $par=null ) {
	global $wgRequest, $wgUser, $wgRCMaxAge;
	$revision = $wgRequest->getIntOrNull( 'revision' );
	if ( $wgRequest->getCheck( 'diff' ) && !is_null( $revision )) {
		sosShowDiff( $revision);
	} else if( is_null( $revision ) ) {
		sosShowList( $wgRCMaxAge );
	} else {
		sosShowRevision( $revision );
	}
}

function sosShowList( $from=null ) {
	$dbr = wfGetDB( DB_SLAVE );

	$fromTime = $dbr->timestamp( $from );
	$result = sosGetRevisions( $dbr,
		array( 'hidden_on_timestamp >= ' . $dbr->addQuotes( $fromTime ) ) );

	global $wgOut;
	$wgOut->addWikiText( wfMsgNoTrans( 'oversight-header' ) );
	$wgOut->addHtml( '<ul>' );
	while( $row = $dbr->fetchObject( $result ) ) {
		$wgOut->addHtml( sosListRow( $row ) );
	}
	$wgOut->addHtml( '</ul>' );
	$dbr->freeResult( $result );
}

function sosGetRevisions( $db, $condition ) {
	return $db->select(
		array( 'hidden', 'user' ),
		array(
			'hidden_page as page_id',
			'hidden_namespace as page_namespace',
			'hidden_title as page_title',

			'hidden_page as rev_page',
			'hidden_comment as rev_comment',
			'hidden_user as rev_user',
			'hidden_user_text as rev_user_text',
			'hidden_timestamp as rev_timestamp',
			'hidden_minor_edit as rev_minor_edit',
			'hidden_deleted as rev_deleted',
			'hidden_rev_id as rev_id',
			'hidden_text_id as rev_text_id',

			'0 as rev_len',

			'hidden_by_user',
			'hidden_on_timestamp',
			'hidden_reason',

			'user_name',

			'0 as page_is_new',
			'0 as rc_id',
			'1 as rc_patrolled',
			'0 as rc_old_len',
			'0 as rc_new_len',
			'0 as rc_params',

			'NULL AS rc_log_action',
			'0 AS rc_deleted',
			'0 AS rc_logid',
			'NULL AS rc_log_type' ),
		array_merge(
			$condition,
			array( 'hidden_by_user=user_id' ) ),
		__FUNCTION__,
		array(
			'ORDER BY' => 'hidden_on_timestamp DESC' ) );
}

function sosListRow( $row ) {
	global $wgUser, $wgLang;
	$skin = $wgUser->getSkin();
	$self = Title::makeTitle( NS_SPECIAL, 'Oversight' );
	$userPage = Title::makeTitle( NS_USER, $row->user_name );
	$victim = Title::makeTitle( $row->page_namespace, $row->page_title );
	return "<li>(" .
		$skin->makeKnownLinkObj( $self, wfMsgHTML( 'oversight-view' ),
			'revision=' . $row->rev_id ) .
		") " .
		"(" .
		$skin->makeKnownLinkObj( $self, wfMsgHTML( 'diff' ),
			'revision=' . $row->rev_id . '&diff=1') .
		") " .
		$wgLang->timeanddate( $row->hidden_on_timestamp ) .
		" " .
		$skin->makeLinkObj( $userPage, htmlspecialchars( $userPage->getText() ) ) .
		" " .
		wfMsgHTML( 'oversight-log-hiderev', $skin->makeLinkObj( $victim ) ) .
		" " .
		$skin->commentBlock( $row->hidden_reason ) .
		"</li>\n";
}

function sosShowRevision( $revision ) {
	global $wgOut;

	$dbr = wfGetDB( DB_SLAVE );
	$result = sosGetRevisions( $dbr, array( 'hidden_rev_id' => $revision ) );

	while( $row = $dbr->fetchObject( $result ) ) {
		$info = sosListRow( $row );
		$list = sosRevisionInfo( $row );
		$rev = new Revision( $row );
		$text = $rev->getText();
		$wgOut->addHtml(
			"<ul>" .
			$info .
			"</ul>\n" .
			$list );
	    if ( $text === false ) {
		$wgOut->addWikiText(wfmsg('hiderevision-error-missing'));
		} else {
		$wgOut->addHtml(
			"<div>" .
			wfOpenElement( 'textarea',
				array(
					'cols' => 80,
					'rows' => 25,
					'wrap' => 'virtual',
					'readonly' => 'readonly' ) ) .
			htmlspecialchars( $text ) .
			wfCloseElement( 'textarea' ) .
			"</div>" );
		}
	}
	$dbr->freeResult( $result );
}

function sosRevisionInfo( $row ) {
	global $wgUser;
	$changes = ChangesList::newFromUser( $wgUser );
	$out = $changes->beginRecentChangesList();
	$rc = RecentChange::newFromCurRow( $row );
	$rc->counter = 0; // ???
	$out .= $changes->recentChangesLine( $rc );
	$out .= $changes->endRecentChangesList();
	return $out;
}

function sosShowDiff( $revision )
{
	global $wgOut;

	$dbr = wfGetDB( DB_SLAVE );
	$result = sosGetRevisions( $dbr, array( 'hidden_rev_id' => $revision ) );

	while( $row = $dbr->fetchObject( $result ) ) {
		$info = sosListRow( $row );
		$list = sosRevisionInfo( $row );
		$rev = new Revision( $row );
		$rev->mTitle = Title::makeTitle( $row->page_namespace, $row->page_title );
		$prevId = $rev->mTitle->getPreviousRevisionID( $row->rev_id );
		if ( $prevId ) {
			$prev = Revision::newFromTitle( $rev->mTitle, $prevId );
			$otext = strval( $prev->getText());
		} else {
			$wgOut->addHtml(
			"<ul>" .
			$info .
			"</ul>\n" .
			$list );
			$wgOut->addWikiText( wfMsgNoTrans( 'oversight-nodiff' ) );
			return;
		}
		$ntext = strval( $rev->getText());

		$diffEngine = new DifferenceEngine();
		$diffEngine->showDiffStyle();
		$wgOut->addHtml(
			"<ul>" .
			$info .
			"</ul>\n" .
			$list .
			"<p><strong>" .
			wfMsgHTML('oversight-difference') .
			"</strong>" .
			"</p>" .
			"<div>" .
			"<table border='0' width='98%' cellpadding='0' cellspacing='4' class='diff'>" .
			"<col class='diff-marker' />" .
			"<col class='diff-content' />" .
			"<col class='diff-marker' />" .
			"<col class='diff-content' />" .
			"<tr>" .
				"<td colspan='2' width='50%' align='center' class='diff-otitle'>" . wfMsgHTML('oversight-prev') . " (#$prevId)" . "</td>" .
				"<td colspan='2' width='50%' align='center' class='diff-ntitle'>" . wfMsgHTML('oversight-hidden') . "</td>" .
			"</tr>" .
			$diffEngine->generateDiffBody( $otext, $ntext ) .
			"</table>" .
			"</div>\n" );
	}
	$dbr->freeResult( $result );
}
