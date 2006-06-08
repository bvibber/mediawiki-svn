<?php

/**
 * Extension adds improved patrolling interface
 *
 * THIS IS INCOMPLETE AT THE PRESENT TIME, SO DON'T GO OFF INSTALLING
 * IT AND THEN WHINING BECAUSE IT DOESN'T WORK
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
	$wgGroupPermissions['*']['patroller'] = true;
	
	function efPatroller() {
		global $wgMessageCache, $wgHooks;
		efPatrollerAddMessages( $wgMessageCache );
		SpecialPage::addPage( new Patroller() );
		efPatrollerPrune();
	}
	
	function efPatrollerPrune() {
		wfSeedRandom();
		if( 0 == mt_rand( 0, 499 ) ) # Might need to bump this
			Patroller::pruneAssignments();
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

			# Pop an edit off recentchanges
			$haveEdit = false;
			while( !$haveEdit ) {
				$edit = $this->fetchChange( $wgUser );
				if( $edit ) {
					# Attempt to assign it
					if( $this->assignChange( $edit ) ) {
						$haveEdit = true;
						# Show a stub changes list giving all the usual tools links
						# For the purposes of formatting, pretend that the change is patrolled		
						$edit->counter = 1;
						$edit->mAttribs['rc_patrolled'] = 1;
						$list = ChangesList::newFromUser( $wgUser );
						$html = $list->beginRecentChangesList();
						$html .= $list->recentChangesLine( $edit );
						$html .= $list->endRecentChangesList();
						$wgOut->addHtml( $html );
						# Now produce a diff. and show that
						$title = $edit->getTitle();
						$diff = new DifferenceEngine( $title, $edit->mAttribs['rc_last_oldid'], $edit->mAttribs['rc_this_oldid'] );
						$wgOut->addHtml( '<br /><hr />' );
						$diff->showDiff( '', '' );
					}
				} else {
					# Can't find a suitable edit
					$haveEdit = true; # Don't keep going, there's nothing to find
					$wgOut->addWikiText( wfMsg( 'patrol-nonefound' ) );
				}
			}
			
		}
		
		/**
		 * Fetch a recent change which
		 *   - the user doing the patrolling didn't cause
		 *   - wasn't due to a bot
		 *   - hasn't been patrolled
		 *   - isn't assigned to a user
		 */
		function fetchChange( &$user ) {
			$dbr =& wfGetDB( DB_SLAVE );
			$uid = $user->getId();
			extract( $dbr->tableNames( 'recentchanges', 'patrollers' ) );
			$sql = "SELECT * FROM $recentchanges LEFT JOIN $patrollers ON rc_id = ptr_change
					WHERE rc_bot = 0 AND rc_patrolled = 0 AND rc_type = 0 AND rc_user != $uid
					AND ptr_timestamp IS NULL LIMIT 0,1";
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
		
		/**
		 * Prune old assignments from the table so edits aren't
		 * hidden forever because a user wandered off, and to
		 * keep the table size down as regards old assignments
		 */
		function pruneAssignments() {
			$dbw =& wfGetDB( DB_MASTER );
			$dbw->delete( 'patrollers', array( 'ptr_timestamp > ' . $dbw->timestamp( time() - 120 ) ), 'Patroller::pruneAssignments' );
		}
				
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

?>