<?php

/**
 * Class provides a special page to manage the bad image list
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence Copyright holder allows use of the code for any purpose
 */

class BadImageManipulator extends SpecialPage {

	function __construct() {
		parent::__construct( 'Badimages' );
	}
	
	function execute() {
		global $wgUser, $wgOut, $wgRequest;
		$this->setHeaders();
		
		# Check permissions
		if( $wgUser->isAllowed( 'badimages' ) ) {
			# Check for actions pending
			if( $wgRequest->getText( 'action' ) == 'remove' ) {
				if( $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getText( 'wpToken' ) ) ) {
					$this->attemptRemove( $wgRequest, $wgOut, $wgUser );
				} else {
					$this->showRemove( $wgOut, $wgRequest->getText( 'image' ), $wgUser );
				}
			} elseif( $wgRequest->getText( 'action' ) == 'add' ) {
				if( $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getText( 'wpToken' ) ) ) {
					$this->attemptAdd( $wgRequest, $wgOut, $wgUser );
				}
			} else {
				$this->showAdd( $wgOut, $wgUser );
			}
		} else {
			$wgOut->addWikiText( wfMsg( 'badimages-unprivileged' ) );
		}
		
		# List existing bad images
		$this->listExisting();
	}
	
	function showAdd( &$output, &$user ) {
		$self = Title::makeTitle( NS_SPECIAL, 'Badimages' );
		$form  = wfOpenElement( 'form', array( 'method' => 'post', 'action' => $self->getLocalUrl() ) );
		$form .= wfHidden( 'action', 'add' ) . wfHidden( 'wpToken', $user->editToken() );
		$form .= '<table><tr><td align="right">' . wfMsgHtml( 'badimages-name' ) . '</td>';
		$form .= '<td>' . wfInput( 'wpImage' ) . '</td></tr>';
		$form .= '<tr><td align="right">' . wfMsgHtml( 'badimages-reason' ) . '</td>';
		$form .= '<td>' . wfInput( 'wpReason', 40 ) . '</td><tr></tr><td></td><td>';
		$form .= wfSubmitButton( wfMsg( 'badimages-add-btn' ) ) . '</td></tr></table></form>';
		$output->addHtml( $form );
	}
	
	function attemptAdd( &$request, &$output, &$user ) {
		if( $user->isAllowed( 'badimages' ) ) {
			# TODO: Errors should be puked back up, not tucked out of sight
			# -- the user should be informed when providing dud titles, etc.
			$title = Title::makeTitleSafe( NS_IMAGE, $request->getText( 'wpImage' ) );
			if( is_object( $title ) ) {
				BadImageList::add( $title->getDBkey(), $user->getId(), $request->getText( 'wpReason' ) );
				$this->touch( $title );
				$this->log( 'add', $title, $request->getText( 'wpReason' ) );
				$skin =& $user->getSkin();
				$link = $skin->makeKnownLinkObj( $title, htmlspecialchars( $title->getText() ) );
				$output->setSubtitle( wfMsgHtml( 'badimages-added', $link ) );
			} else {
				# TODO: Tell the user it was a dud title
				$output->setSubtitle( wfMsgHtml( 'badimages-not-added' ) );
			}
		}
		$this->showAdd( $output, $user ); # FIXME: This hack sucks a bit
	}
	
	function showRemove( &$output, $name, &$user ) {
		$self = Title::makeTitle( NS_SPECIAL, 'Badimages' );
		$skin =& $user->getSkin();
		$title = Title::makeTitleSafe( NS_IMAGE, $name );
		$link = $skin->makeKnownLinkObj( $title, htmlspecialchars( $title->getText() ) );
		$output->addHtml( '<p>' . wfMsgHtml( 'badimages-remove-confirm', $link ) . '</p>' );
		$form  = wfOpenElement( 'form', array( 'method' => 'post', 'action' => $self->getLocalUrl() ) );
		$form .= wfHidden( 'action', 'remove' ) . wfHidden( 'wpToken', $user->editToken() );
		$form .= '<table><tr><td align="right">' . wfMsgHtml( 'badimages-name' ) . '</td>';
		$form .= '<td>' . wfInput( 'wpImage', false, $name ) . '</td></tr>';
		$form .= '<tr><td align="right">' . wfMsgHtml( 'badimages-reason' ) . '</td>';
		$form .= '<td>' . wfInput( 'wpReason', 40 ) . '</td><tr></tr><td></td><td>';
		$form .= wfSubmitButton( wfMsg( 'badimages-remove-btn' ) ) . '</td></tr></table></form>';
		$output->addHtml( $form );
	}

	function attemptRemove( &$request, &$output, &$user ) {
		if( $user->isAllowed( 'badimages' ) ) {
			$title = Title::makeTitleSafe( NS_IMAGE, $request->getText( 'wpImage' ) );
			if( is_object( $title ) ) {
				BadImageList::remove( $title->getDBkey() );
				$this->touch( $title );
				$this->log( 'remove', $title, $request->getText( 'wpReason' ) );
				$skin =& $user->getSkin();
				$link = $skin->makeKnownLinkObj( $title, htmlspecialchars( $title->getText() ) );
				$output->setSubtitle( wfMsgHtml( 'badimages-removed', $link ) );
			} else {
				# Shouldn't happen in normal (dumb user) usage
				$output->setSubtitle( wfMsgHtml( 'badimages-not-removed' ) );
			}
		}
		$this->showAdd( $output, $user );
	}
	
	function log( $action, &$target, $reason ) {
		$log = new LogPage( 'badimage' );
		$log->addEntry( $action, $target, $reason );
	}
	
	/**
	 * This won't have an effect until the parser is fixed; right now, it doesn't
	 * update the imagelinks table when it finds a blacklisted image during a parse
	 * run, so the link update job doesn't encounter it, and it isn't recorded
	 */
	function touch( &$title ) {
		wfDebug( 'BI_TOUCH: ' . $title->getPrefixedText() );
		$update = new HTMLCacheUpdate( $title, 'imagelinks' );
		$update->doUpdate();
	}

	function listExisting() {
		global $wgOut, $wgUser, $wgLang;
		$dbr =& wfGetDB( DB_SLAVE );
		extract( $dbr->tableNames( 'bad_images', 'user' ) );
		$sql = "SELECT * FROM {$bad_images} LEFT JOIN {$user} ON bil_user = user_id";
		$res = $dbr->query( $sql, __METHOD__ );
		$wgOut->addHtml( '<h2>' . wfMsgHtml( 'badimages-subheading' ) . '</h2>' );
		if( $res ) {
			$count = $wgLang->formatNum( $dbr->numRows( $res ) );
			$wgOut->addWikiText( wfMsg( 'badimages-count', $count ) );
			$skin =& $wgUser->getSkin();
			$wgOut->addHtml( '<ul>' );
			while( $row = $dbr->fetchObject( $res ) )
				$wgOut->addHtml( $this->makeListRow( $row, $skin, $wgLang, $wgUser->isAllowed( 'badimages' ) ) );
			$wgOut->addHtml( '</ul>' );
		}
	}
	
	function makeListRow( $result, &$skin, &$lang, $priv ) {
		$title = Title::makeTitleSafe( NS_IMAGE, $result->bil_name );
		$ilink = $skin->makeLinkObj( $title, htmlspecialchars( $title->getText() ) );
		if( $priv ) {
			$self = Title::makeTitle( NS_SPECIAL, 'Badimages' );
			$ilink .= ' ' . $skin->makeKnownLinkObj( $self, wfMsgHtml( 'badimages-remove' ), 'action=remove&image=' . $title->getPartialUrl() );
		}
		$ulink = $skin->userLink( $result->bil_user, $result->user_name ) . $skin->userToolLinks( $result->bil_user, $result->user_name );
		$time = $lang->timeAndDate( $result->bil_timestamp, true );
		$comment = $skin->commentBlock( $result->bil_reason );
		return "<li>{$ilink} . . {$time} . . {$ulink} {$comment}</li>";
	}

}

?>