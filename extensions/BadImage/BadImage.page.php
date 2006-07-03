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
			# TODO: *cough...um, duh?*

		} else {
			$wgOut->addWikiText( wfMsg( 'badimages-unprivileged' ) );
		}
		
		# List existing bad images
		$this->listExisting();
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