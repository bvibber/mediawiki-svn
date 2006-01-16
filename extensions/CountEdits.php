<?php

/**
 * Simple edit counter special page for small wikis
 *
 * @author Rob Church <robchur@gmail.com>
 * @package MediaWiki
 * @subpackage Extensions
 * @copyright Copyright © 2006 Rob Church
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0
 */

global $IP;
require_once( $IP . '/includes/SpecialPage.php' );

if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efCountEdits';
	$wgExtensionCredits['other'][] = array( 'name' => 'Count edits', 'description' => 'a simple special page to count user edits', 'author' => 'Rob Church' );

	function efCountEdits() {
		global $wgMessageCache;
		
		$wgMessageCache->addMessage( 'countedits', 'Count edits' );
		$wgMessageCache->addMessage( 'countedits-username', 'Username' );
		$wgMessageCache->addMessage( 'countedits-ok', 'OK' );
		$wgMessageCache->addMessage( 'countedits-nosuchuser', 'There is no user with the name $1' );
		$wgMessageCache->addMessage( 'countedits-resultheader', 'Results for $1' );
		$wgMessageCache->addMessage( 'countedits-resulttext', '$1 has made $2 edits' );
		$wgMessageCache->addMessage( 'countedits-userpage', 'User page' );
		$wgMessageCache->addMessage( 'countedits-usertalk', 'Talk page' );
		$wgMessageCache->addMessage( 'countedits-contribs', 'Contributions' );
		
		SpecialPage::addPage( new CountEdits() );
		return( true );
	}
	
	class CountEdits extends SpecialPage {
		
		var $target;

		function CountEdits() {
			SpecialPage::SpecialPage( 'CountEdits' );
		}
		
		function execute( $params ) {
			global $wgOut, $wgUser;
			$skin = $wgUser->getSkin();
			$this->setHeaders();
			$this->loadRequest( $params );
			$wgOut->addHTML( $this->makeForm() );
			if( $this->target ) {
				if( User::isIP( $this->target ) ) {
					$this->showResults( $this->countEditsReal( 0, $this->target ) );
				} else {
					$id = User::idFromName( $this->target );
					if( $id ) {
						$this->showResults( $this->countEditsReal( $id, false ) );
					} else {
						$wgOut->addHTML( '<p>' . wfMsg( 'countedits-nosuchuser', $this->target ) . '</p>' );
					}
				}
			}
			return( true );
		}
		
		function loadRequest( $params ) {
			global $wgRequest;
			if( $params ) {
				$this->target = $params;
			} else {
				$target = $wgRequest->getText( 'target' );
				$this->target = $target ? $target : '';
			}
		}
		
		function makeForm() {
			global $wgTitle;
			$form  = '<form method="post" action="'. $wgTitle->getLocalUrl() . '">';
			$form .= '<p><strong>' . wfMsgHtml( 'countedits-username' ) .': </strong>';
			$form .= '<input type="text" name="target" size="25" value="' . $this->target . '" /> ';
			$form .= '<input type="submit" name="countedits" value="' . wfMsgHtml( 'countedits-ok' ) . '" />';
			$form .= '</p></form>';
			return( $form );
		}
		
		function countEditsReal( $id, $text = false ) {
			$conds = array( 'rev_user' => $id );
			if( $text ) { $conds['rev_user_text'] = $text; }
			$dbr =& wfGetDB( DB_SLAVE );
			$res = $dbr->select( 'revision', 'COUNT(rev_id) AS count', $conds, 'CountEdits::countEditsReal' );
			$row = $dbr->fetchObject( $res );
			return( $row->count );
		}
		
		function makeUserLinks( $user ) {
			global $wgUser;
			$skin = $wgUser->getSkin();
			$page = $skin->makeKnownLinkObj( Title::makeTitle( NS_USER, $user ), wfMsgHtml( 'countedits-userpage' ) );
			$talk = $skin->makeKnownLinkObj( Title::makeTitle( NS_USER_TALK, $user ), wfMsgHtml( 'countedits-usertalk' ) );
			$cont = $skin->makeKnownLinkObj( Title::makeTitle( NS_SPECIAL, 'Contributions' ), wfMsgHtml( 'countedits-contribs' ), 'target=' . $user );
			return( array( 'page' => $page, 'talk' => $talk, 'cont' => $cont ) );
		}
		
		function showResults( $count ) {
			global $wgOut;
			$wgOut->addHTML( '<h2>' . wfMsg( 'countedits-resultheader', $this->target ) . '</h2>' );
			$wgOut->addHTML( '<p><strong>' . wfMsg( 'countedits-resulttext', $this->target, $count ) . '</strong></p>' );
			$wgOut->addHTML( '<ul>' );
			foreach( $this->MakeUserLinks( $this->target ) as $link ) { $wgOut->addHTML( '<li>' . $link . '</li>' ); }
			$wgOut->addHTML( '</ul>' );
		}

	}

} else {
	die( 'This file is an extension to the MediaWiki package, and cannot be executed separately.' );
}

?>