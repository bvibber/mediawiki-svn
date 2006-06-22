<?php

/**
 * Extension adds improved patrolling interface
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0
 */
 
if( defined( 'MEDIAWIKI' ) ) {

	require_once( 'SpecialPage.php' );
	require_once( 'Patroller.i18n.php' );

	$wgExtensionFunctions[] = 'efPatroller';
	$wgExtensionCredits['specialpage'][] = array( 'name' => 'Patrol', 'author' => 'Rob Church' );
	
	$wgAvailableRights[] = 'patroller';
	$wgGroupPermissions['sysop']['patroller'] = true;
	
	function efPatroller() {
		global $wgMessageCache, $wgHooks;
		efPatrollerAddMessages( $wgMessageCache );
		SpecialPage::addPage( new Patroller() );
	}
	
	class Patroller extends SpecialPage {
		
		function Patroller() {
			SpecialPage::SpecialPage( 'Patrol', 'patroller' );
		}
		
		function execute() {
			global $wgUser, $wgRequest, $wgOut;
			$this->setHeaders();
			
			# Check permissions
			if( !$wgUser->isAllowed( 'patroller' ) ) {
				$wgOut->permissionRequired( 'patroller' );
				return;
			}
			
			# Prune old assignments if needed
			wfSeedRandom();
			if( 0 == mt_rand( 0, 499 ) )
				$this->pruneAssignments();
			
			# See if something needs to be done
			if( $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getText( 'wpToken' ) ) ) {
				if( $rcid = $wgRequest->getIntOrNull( 'wpRcId' ) ) {
					if( $wgRequest->getCheck( 'wpPatrolEndorse' ) ) {
						# Mark the change patrolled
						RecentChange::markPatrolled( $rcid );
						$wgOut->setSubtitle( wfMsgHtml( 'patrol-endorsed-ok' ) );
						wfDebugLog( 'patroller', 'Endorsed ' . $rcid );
					} elseif( $wgRequest->getCheck( 'wpPatrolRevert' ) ) {
						# Revert the change
						$edit = $this->loadChange( $rcid );
						$this->revert( $edit, $this->revertReason( $wgRequest ) );
						$wgOut->setSubtitle( wfMsgHtml( 'patrol-reverted-ok' ) );
						wfDebugLog( 'patroller', 'Reverted ' . $rcid );
					} elseif( $wgRequest->getCheck( 'wpPatrolSkip' ) ) {
						# Do bugger all, for now
						$wgOut->setSubtitle( wfMsgHtml( 'patrol-skipped-ok' ) );
					}
				}
			}

			# Pop an edit off recentchanges
			$haveEdit = false;
			while( !$haveEdit ) {
				$edit = $this->fetchChange( $wgUser );
				if( $edit ) {
					# Attempt to assign it
					if( $this->assignChange( $edit ) ) {
						$haveEdit = true;
						$this->showDiffDetails( $edit );
						$wgOut->addHtml( '<br /><hr />' );
						$this->showDiff( $edit );
						$wgOut->addHtml( '<br /><hr />' );
						$this->showControls( $edit );
					}
				} else {
					# Can't find a suitable edit
					$haveEdit = true; # Don't keep going, there's nothing to find
					$wgOut->addWikiText( wfMsg( 'patrol-nonefound' ) );
				}
			}
			
		}
		
		/**
		 * Produce a stub recent changes listing for a single diff.
		 *
		 * @param $edit Diff. to show the listing for
		 */
		function showDiffDetails( &$edit ) {
			global $wgUser, $wgOut;
			$edit->counter = 1;
			$edit->mAttribs['rc_patrolled'] = 1;
			$list = ChangesList::newFromUser( $wgUser );
			$wgOut->addHtml( $list->beginRecentChangesList() .
							 $list->recentChangesLine( $edit ) .
							 $list->endRecentChangesList() );
		}
		
		/**
		 * Produce a diff. of a specific change
		 *
		 * @param $edit Recent change to produce a diff. for
		 */
		function showDiff( &$edit ) {
			$diff = new DifferenceEngine( $edit->getTitle(), $edit->mAttribs['rc_last_oldid'], $edit->mAttribs['rc_this_oldid'] );
			$diff->showDiff( '', '' );
		}
		
		function showControls( &$edit ) {
			global $wgUser, $wgOut;
			$self = Title::makeTitle( NS_SPECIAL, 'Patrol' );
			$form = wfOpenElement( 'form', array( 'method' => 'post', 'action' => $self->getLocalUrl() ) );
			$form .= '<table>';
			$form .= '<tr><td align="right">' . wfSubmitButton( wfMsg( 'patrol-endorse' ), array( 'name' => 'wpPatrolEndorse' ) ) . '</td><td></td></tr>';
			$form .= '<tr><td align="right">' . wfSubmitButton( wfMsg( 'patrol-revert' ), array( 'name' => 'wpPatrolRevert' ) ) . '</td>';
			$form .= '<td>' . wfLabel( wfMsg( 'patrol-revert-reason' ), 'reason' ) . '&nbsp;';
			$form .= $this->revertReasonsDropdown() . ' / ' . wfInput( 'wpPatrolRevertReason' ) . '</td></tr>';
			$form .= '<tr><td align="right">' . wfSubmitButton( wfMsg( 'patrol-skip' ), array( 'name' => 'wpPatrolSkip' ) ) . '</td></tr></table>';
			$form .= wfHidden( 'wpRcId', $edit->mAttribs['rc_id'] );
			$form .= wfHidden( 'wpToken', $wgUser->editToken() );
			$form .= '</form>';
			$wgOut->addHtml( $form );
		}
		
		/**
		 * Fetch a recent change which
		 *   - the user doing the patrolling didn't cause
		 *   - wasn't due to a bot
		 *   - hasn't been patrolled
		 *   - isn't assigned to a user
		 *
		 */
		function fetchChange( &$user ) {
			$dbr =& wfGetDB( DB_SLAVE );
			$uid = $user->getId();
			extract( $dbr->tableNames( 'recentchanges', 'patrollers', 'page' ) );
			$sql = "SELECT * FROM $page, $recentchanges LEFT JOIN $patrollers ON rc_id = ptr_change
					WHERE rc_namespace = page_namespace AND rc_title = page_title
					AND rc_this_oldid = page_latest AND rc_bot = 0 AND rc_patrolled = 0 AND rc_type = 0
					AND rc_user != $uid AND ptr_timestamp IS NULL LIMIT 0,1";
			
			/*$sql = "SELECT * FROM $recentchanges LEFT JOIN $patrollers ON rc_id = ptr_change
					WHERE rc_bot = 0 AND rc_patrolled = 0 AND rc_type = 0 AND rc_user != $uid
					AND ptr_timestamp IS NULL LIMIT 0,1";*/
					
			$res = $dbr->query( $sql, 'Patroller::fetchChange' );
			if( $dbr->numRows( $res ) > 0 ) {
				$row = $dbr->fetchObject( $res );
				$dbr->freeResult( $res );
				return RecentChange::newFromRow( $row, $row->rc_last_oldid );
			} else {
				$dbr->freeResult( $res );
				return false;
			}
		}
		
		function loadChange( $rcid ) {
			$dbr =& wfGetDB( DB_SLAVE );
			$res = $dbr->select( 'recentchanges', '*', array( 'rc_id' => $rcid ), 'Patroller::loadChange' );
			if( $dbr->numRows( $res ) > 0 ) {
				$row = $dbr->fetchObject( $res );
				return RecentChange::newFromRow( $row );
			} else {
				return false;
			}
		}
		
		/**
		 * Assign the patrolling of a particular change, so
		 * other users don't pull it up, duplicating effort
		 *
		 * @param $edit RecentChange item to assign
		 * @return bool
		 */
		function assignChange( &$edit ) {
			$dbw =& wfGetDB( DB_MASTER );
			$val = array( 'ptr_change' => $edit->mAttribs['rc_id'], 'ptr_timestamp' => $dbw->timestamp() );
			$res = $dbw->insert( 'patrollers', $val, 'Patroller::assignChange', 'IGNORE' );
			return (bool)$dbw->affectedRows();
		}
		
		function unassignChange( $rcid ) {
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->delete( 'patrollers', array( 'ptr_change' => $rcid ), 'Patroller::unassignChange' );		
		}

		/**
		 * Prune old assignments from the table so edits aren't
		 * hidden forever because a user wandered off, and to
		 * keep the table size down as regards old assignments
		 */
		function pruneAssignments() {
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->delete( 'patrollers', array( 'ptr_timestamp < ' . $dbw->timestamp( time() - 120 ) ), 'Patroller::pruneAssignments' );
		}
		
		function revert( &$edit, $comment = '' ) {
			global $wgOut;
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->begin();
			$title = $edit->getTitle();
			# Prepare the comment
			$comment = wfMsgForContent( 'patrol-reverting' ) . ( $comment ? ' (' . $comment . ')' : '' );
			# Find the old revision
			$old = $edit->mAttribs['rc_last_oldid'];
			$oldRev = Revision::newFromId( $old );
			wfDebugLog( 'patroller', "Reverting " . $title->getPrefixedText() . " to r" . $oldRev->getId() );
			# Revert the edit; mark the reversion with a bot flag
			$article = new Article( $title );
			$article->updateArticle( $oldRev->getText(), $comment, 1, $title->userIsWatching(), 1 );
			$wgOut->mRedirect = ''; # HACK: Someone needs to fix Article::updateArticle
			Article::onArticleEdit( $title );
			$dbw->commit();
			# Mark the edit patrolled so it doesn't bother us again
			RecentChange::markPatrolled( $edit->mAttribs['rc_id'] );
		}
		
		function revertReasonsDropdown() {
			$msg = wfMsgForContent( 'patrol-reasons' );
			if( $msg == '-' || $msg == '&lt;patrol-reasons&gt;' ) {
				return '';
			} else {
				$reasons = array();
				$lines = explode( "\n", $msg );
				foreach( $lines as $line ) {
					if( substr( $line, 0, 1 ) == '*' )
						$reasons[] = trim( $line, '* ' );
				}
				if( count( $reasons ) > 0 ) {
					$box = wfOpenElement( 'select', array( 'name' => 'wpPatrolRevertReasonCommon' ) );
					foreach( $reasons as $reason )
						$box .= wfElement( 'option', array( 'value' => $reason ), $reason );
					$box .= wfCloseElement( 'select' );
					return $box;
				} else {
					return '';
				}
			}
		}
		
		function revertReason( &$request ) {
			$custom = $request->getText( 'wpPatrolRevertReason' );
			return trim( $custom ) != ''
					? $custom
					: $request->getText( 'wpPatrolRevertReasonCommon' );
		}
		
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

?>