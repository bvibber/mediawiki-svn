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

if( defined( 'MEDIAWIKI' ) ) {

	require_once( 'SpecialPage.php' );
	$wgExtensionFunctions[] = 'efCountEdits';
	$wgExtensionCredits['other'][] = array( 'name' => 'Count Edits', 'author' => 'Rob Church' );
	$wgCountEditsTopTen = true;

	function efCountEdits() {
		global $wgMessageCache;
		
		$wgMessageCache->addMessage( 'countedits', 'Count edits' );
		$wgMessageCache->addMessage( 'countedits-warning', 'Warning: Do not judge a book by it\'s cover. Do not judge a contributor by their edit count.' );
		$wgMessageCache->addMessage( 'countedits-username', 'Username' );
		$wgMessageCache->addMessage( 'countedits-ok', 'OK' );
		$wgMessageCache->addMessage( 'countedits-nosuchuser', 'There is no user with the name $1' );
		$wgMessageCache->addMessage( 'countedits-resultheader', 'Results for $1' );
		$wgMessageCache->addMessage( 'countedits-resulttext', '$1 has made $2 edits' );
		$wgMessageCache->addMessage( 'countedits-userpage', 'User page' );
		$wgMessageCache->addMessage( 'countedits-usertalk', 'Talk page' );
		$wgMessageCache->addMessage( 'countedits-contribs', 'Contributions' );
		$wgMessageCache->addMessage( 'countedits-mostactive', 'Top ten contributors' );
		
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
						$wgOut->addHTML( '<p>' . wfMsg( 'countedits-nosuchuser', htmlspecialchars( $this->target ) ) . '</p>' );
					}
				}
			}
			$this->showTopTen( $wgOut );
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
			$form .= '<input type="text" name="target" size="25" value="' . htmlspecialchars( $this->target ) . '" /> ';
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
			$page = $skin->makeLinkObj( Title::makeTitle( NS_USER, $user ), wfMsgHtml( 'countedits-userpage' ) );
			$talk = $skin->makeLinkObj( Title::makeTitle( NS_USER_TALK, $user ), wfMsgHtml( 'countedits-usertalk' ) );
			$cont = $skin->makeKnownLinkObj( Title::makeTitle( NS_SPECIAL, 'Contributions' ), wfMsgHtml( 'countedits-contribs' ), 'target=' . $user );
			return( array( 'page' => $page, 'talk' => $talk, 'cont' => $cont ) );
		}
		
		function showResults( $count ) {
			global $wgOut;
			$wgOut->addHTML( '<h2>' . wfMsgHtml( 'countedits-resultheader', $this->target ) . '</h2>' );
			$wgOut->addHTML( '<p><strong>' . wfMsgHtml( 'countedits-resulttext', $this->target, $count ) . '</strong></p>' );
			$wgOut->addHTML( '<ul>' );
			foreach( $this->MakeUserLinks( $this->target ) as $link ) { $wgOut->addHTML( '<li>' . $link . '</li>' ); }
			$wgOut->addHTML( '</ul>' );
			$wgOut->addHTML( '<p>' . wfMsgHtml( 'countedits-warning' ) . '</p>' );
		}
		
		function showTopTen( &$out ) {
			global $wgCountEditsTopTen;
			if( $wgCountEditsTopTen ) {
				$out->addHTML( '<h2>' . wfMsgHtml( 'countedits-mostactive' ) . '</h2>' );
				$out->addHTML( $this->getTopTen() );
			}
		}
		
		function getTopTen() {
			global $wgUser, $wgLang;
			$skin = $wgUser->getSkin();
			$out  = '<ul>';
			$dbr  =& wfGetDB( DB_SLAVE );
			$rev  = $dbr->tableName( 'revision' );
			# We fetch 11, even though we want 10, because we *don't* want MediaWiki default (and we might get it)
			$sql  = "SELECT COUNT(*) AS count, rev_user_text FROM $rev GROUP BY rev_user_text ORDER BY count DESC LIMIT 0,11";
			$res  = $dbr->query( $sql );
			while( $row = $dbr->fetchObject( $res ) ) {
				if( $row->rev_user_text != 'MediaWiki default' ) {
					$upt  = Title::makeTitle( NS_USER, $row->rev_user_text );
					$cpt  = Title::makeTitle( NS_SPECIAL, 'Contributions/' . $row->rev_user_text );
					$upl  = $skin->makeLinkObj( $upt, $upt->getText() );
					$tpl  = $skin->makeLinkObj( $upt->getTalkPage(), $wgLang->getNsText( NS_TALK ) );
					$cpl  = $skin->makeKnownLinkObj( $cpt, wfMsgHtml( 'contribslink' ) );
					$uec  = $row->count;
					$out .= "<li>$upl ($tpl | $cpl) [$uec]</li>";
				}
			}
			$out .= '</ul>';
			return( $out == '<ul></ul>' ? '' : $out );
		}

	}

} else {
	die( 'This file is an extension to the MediaWiki package, and cannot be executed separately.' );
}

?>