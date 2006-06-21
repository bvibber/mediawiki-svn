<?php

/**
 * See http://www.mediawiki.org/wiki/Hiding_revisions
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

// The 'hiderevision' permission allows use of revision hiding.
$wgGroupPermissions['*']['hiderevision'] = false;

// 'oversight' permission is required to view a previously-hidden revision.
$wgGroupPermissions['*']['oversight'] = false;

// You could add a group like this:
// $wgGroupPermissions['censor']['hiderevision'] = true;
// $wgGroupPermissions['quiscustodiet']['oversight'] = true;

$wgExtensionFunctions[] = 'hrSetup';

/**
 * Setup function for HideRevision extension.
 * Adds the special page for the action form.
 */
function hrSetup() {
	require_once 'SpecialPage.php';
	SpecialPage::addPage( new SpecialPage( 'HideRevision', 'hiderevision',
		/*listed*/ true, /*function*/ false, /*file*/ false ) );
	
	SpecialPage::addPage( new SpecialPage( 'Oversight', 'oversight',
		/*listed*/ true, /*function*/ false, /*file*/ false ) );
	
	$GLOBALS['wgHooks']['ArticleViewHeader'][] = 'hrArticleViewHeaderHook';
	$GLOBALS['wgHooks']['DiffViewHeader'][] = 'hrDiffViewHeaderHook';
	
	$GLOBALS['wgMessageCache']->addMessages(
		array(
			'hiderevision' => 'Permanently hide revisions',
			
			// Empty form
			'hiderevision-prompt' => 'Revision number to remove:',
			'hiderevision-continue' => 'Continue',
			
			// Confirmation form
			'hiderevision-text' =>
"This should '''only''' be used for the following cases:
* Inappropriate personal information
*: ''home addresses and telephone numbers, social security numbers, etc''

'''Abuse of this system will result in loss of privileges.'''

Removed items will not be visible to anyone through the web site,
but the deletions are logged and can be restored manually by a
database administrator if you make a mistake.",
			'hiderevision-reason' => 'Reason (will be logged privately):',
			'hiderevision-submit' => 'Hide this data permanently',
			
			// Tab displayed to allowed users on old revision display
			'hiderevision-tab' => 'Hide revision',
			
			// Status & errors on action
			'hiderevision-norevisions' => 'No revisions specified to delete.',
			'hiderevision-noreason' => 'You must decribe the reason for this removal.',
			
			'hiderevision-status' => 'Revision $1: $2',
			'hiderevision-success' => 'Archived and deleted successfully.',
			'hiderevision-error-missing' => 'Not found in database.',
			'hiderevision-error-current' => 'Cannot delete the latest edit to a page. Revert this change first.',
			'hiderevision-error-delete' => 'Could not archive; was it previously deleted?',
			
			// Logging
			'oversight-log-hiderev' => 'removed an edit from $1',
			
			// Oversight review page
			'oversight' => 'Oversight',
			'oversight-view' => 'details',
		) );
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
}

/**
 * If the user is allowed, installs a tab hook on the skin
 * which links to a handy permanent removal thingy.
 */
function hrInstallTab( $id ) {
	global $wgUser;
	if( $wgUser->isAllowed( 'hiderevision' ) ) {
		global $wgHooks;
		$wgHooks['SkinTemplateTabs'][] = create_function(
			'$skin, &$content_actions',
			"\$special = Title::makeTitle( NS_SPECIAL, 'HideRevision' );
				\$content_actions['hiderevision'] = array(
					'class' => false,
					'text' => wfMsgHTML( 'hiderevision-tab' ),
					'href' => \$special->getLocalUrl( 'revision[]=$id' ) );
				return true;" );
	}
}

/**
 * Special page handler function for Special:HideRevision
 */
function wfSpecialHideRevision( $par=null ) {
	global $wgRequest, $wgUser;
	$revisions = $wgRequest->getIntArray( 'revision' );
	$reason = $wgRequest->getText( 'wpReason' );
	if( empty( $revisions ) ) {
		hrShowEmpty( $reason );
	} elseif( $wgRequest->wasPosted()
		&& $wgRequest->getVal( 'action' ) == 'submit'
		&& $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) ) ) {
		hrSubmitForm( $revisions, $reason );
	} else {
		hrShowForm( $revisions, $reason );
	}
}

/**
 * If no revisions are specified, prompt for a revision id
 */
function hrShowEmpty( $reason='' ) {
	global $wgOut, $wgUser;
	$special = Title::makeTitle( NS_SPECIAL, 'HideRevision' );
	
	$wgOut->addHtml(
		wfOpenElement( 'form', array(
			'action' => $special->getLocalUrl(),
			'method' => 'post' ) ) .
		
		// Visible fields
		wfInputLabel( wfMsg( 'hiderevision-prompt' ), 'revision[]', 'wpRevision', 10 ) .
		"<br />" .
		wfInputLabel( wfMsg( 'hiderevision-reason' ), 'wpReason', 'wpReason', 60 ) .
		"<br />" .
		wfSubmitButton( wfMsg( 'hiderevision-continue' ) ) .
		
		wfCloseElement( 'form' ) );
}

/**
 * Once a set of revisions have been selected,
 * list them and request a reason/comment for confirmation.
 */
function hrShowForm( $revisions, $reason='' ) {
	global $wgOut, $wgUser;
	$special = Title::makeTitle( NS_SPECIAL, 'HideRevision' );
	
	$wgOut->addWikiText( wfMsg( 'hiderevision-text' ) );
	$wgOut->addHtml(
		hrRevisionList( $revisions ) .
		wfOpenElement( 'form', array(
			'action' => $special->getLocalUrl( 'action=submit' ),
			'method' => 'post' ) ) .
		
		// Visible fields
		"<br />" .
		wfInputLabel( wfMsg( 'hiderevision-reason' ), 'wpReason', 'wpReason', 60, $reason ) .
		"<br />" .
		wfSubmitButton( wfMsg( 'hiderevision-submit' ) ) .
		
		// Hidden fields
		hrRevisionFields( $revisions ) .
		wfHidden( 'wpEditToken', $wgUser->editToken() ) .
		
		wfCloseElement( 'form' ) );
}

function hrRevisionList( $revisions ) {
	global $IP, $wgUser;
	//require_once( "$IP/includes/SpecialContributions.php" );
	require_once( "$IP/includes/ChangesList.php" );
	$changes = ChangesList::newFromUser( $wgUser );
	
	$skin = $wgUser->getSkin();
	
	$dbr = wfGetDB( DB_SLAVE );
	$result = $dbr->select(
		array( 'page', 'revision' ),
		'*, 0 AS rc_id, 1 AS rc_patrolled, 0 AS counter',
		array(
			'rev_id' => $revisions,
			'rev_page=page_id',
		),
		__FUNCTION__ );
	
	$out = $changes->beginRecentChangesList();
	while( $row = $dbr->fetchObject( $result ) ) {
		//$out .= ucListEdit( $skin, $row );
		$rc = RecentChange::newFromCurRow( $row );
		$rc->counter = 0; // ???
		$out .= $changes->recentChangesLine( $rc );
	}
	$out .= $changes->endRecentChangesList();
	
	$dbr->freeResult( $result );
	return $out;
}

function hrRevisionFields( $revisions ) {
	$out = '';
	foreach( $revisions as $id ) {
		$out .= wfHidden( 'revision[]', $id );
	}
	return $out;
}

/**
 * Handle submission of deletion form
 */
function hrSubmitForm( $revisions, $reason ) {
	global $wgOut;
	if( empty( $revisions ) ) {
		$wgOut->addWikiText( wfMsg( 'hiderevision-norevisions' ) );
		hrShowForm( $revisions, $reason );
	} elseif( empty( $reason ) ) {
		$wgOut->addWikiText( wfMsg( 'hiderevision-noreason' ) );
		hrShowForm( $revisions, $reason );
	} else {
		$dbw = wfGetDB( DB_MASTER );
		$success = hrHideRevisions( $dbw, $revisions, $reason );
		$wgOut->addWikiText( '* ' . implode( "\n* ", $success ) );
	}
}

/**
 * Go kill the revisions and return status information.
 * @param $dbw database
 * @param $revisions array of revision ID numbers
 * @param $reason comment text for the reason
 * @return array of wikitext strings with success/failure messages
 */
function hrHideRevisions( $dbw, $revisions, $reason ) {
	foreach( $revisions as $id ) {
		$success[] = wfMsg( 'hiderevision-status', $id,
			wfMsg( hrHideRevision( $dbw, $id, $reason ) ) );
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
	$dbw->insert( 'hidden',
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


/**
 * Special page handler function for Special:Oversight
 */
function wfSpecialOversight( $par=null ) {
	global $wgRequest, $wgUser;
	$revision = $wgRequest->getIntOrNull( 'revision' );
	if( is_null( $revision ) ) {
		sosShowList();
	} else {
		sosShowRevision( $revision );
	}
}

function sosShowList( $from=null ) {
	$dbr = wfGetDB( DB_SLAVE );
	
	$fromTime = $dbr->timestamp( $from );
	$result = sosGetRevisions( $dbr,
		array( 'hidden_on_timestamp < ' . $dbr->addQuotes( $fromTime ) ) );
	
	global $wgOut;
	while( $row = $dbr->fetchObject( $result ) ) {
		$wgOut->addHtml( sosListRow( $row ) );
	}
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
			
			'hidden_by_user',
			'hidden_on_timestamp',
			'hidden_reason',
			
			'user_name',
			
			'0 as page_is_new',
			'0 as rc_id',
			'1 as rc_patrolled' ),
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
		$skin->makeLinkObj( $self, wfMsgHtml( 'oversight-view' ),
			'revision=' . $row->rev_id ) .
		") " .
		$wgLang->timeanddate( $row->hidden_on_timestamp ) .
		" " .
		$skin->makeLinkObj( $userPage, htmlspecialchars( $userPage->getText() ) ) .
		" " .
		wfMsgHtml( 'oversight-log-hiderev', $skin->makeLinkObj( $victim ) ) .
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
		
		$wgOut->addHtml(
			"<ul>" .
			$info .
			"</ul>\n" .
			$list .
			"<div>" .
			wfElement( 'textarea',
				array(
					'cols' => 80,
					'rows' => 25,
					'wrap' => 'virtual',
					'readonly' => 'readonly' ),
				strval( $rev->getText() ) ) .
			"</div>\n" );
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

?>