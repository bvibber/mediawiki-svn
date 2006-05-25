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

$wgGroupPermissions['*']['oversight'] = false;

// You could add a group like this:
// $wgGroupPermissions['censor']['oversight'] = true;

$wgExtensionFunctions[] = 'hrSetup';

/**
 * Setup function for HideRevision extension.
 * Adds the special page for the action form.
 */
function hrSetup() {
	global $wgMessageCache, $wgHooks, $wgContLang;
	
	require_once 'SpecialPage.php';
	SpecialPage::addPage( new SpecialPage( 'HideRevision', 'oversight',
		/*listed*/ true, /*function*/ false, /*file*/ false ) );
	
	$wgHooks['ArticleViewHeader'][] = 'hrArticleViewHeaderHook';
	$wgHooks['DiffViewHeader'][] = 'hrDiffViewHeaderHook';
	
	$wgMessageCache->addMessages(
		array(
			'hiderevision' => 'Permanently hide revisions',
			
			// Empty form
			'hiderevision-empty' => 'No revisions specified.',
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
			'hiderevision-reason' => 'Reason:',
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
	if( $wgUser->isAllowed( 'oversight' ) ) {
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
	
	$wgOut->addWikiText( wfMsg( 'hiderevision-empty' ) );
	$wgOut->addHtml(
		wfOpenElement( 'form', array(
			'action' => $special->getLocalUrl(),
			'method' => 'post' ) ) .
		
		// Visible fields
		wfInputLabel( wfMsg( 'hiderevision-prompt' ), 'revision[]', 'wpRevision', 10 ) .
		"<br />" .
		wfInputLabel( wfMsg( 'hiderevision-reason' ), 'wpReason', 'wpReason', 60 ) .
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
		wfInputLabel( wfMsg( 'hiderevision-reason' ), 'wpReason', 'wpReason', 60, $reason ) .
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
		$update = SquidUpdate::newSimplePurge( $this->mTitle );
		$update->doUpdate();
	}
	
	return 'hiderevision-success';
}

?>