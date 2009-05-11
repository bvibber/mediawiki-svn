<?php

class Poll extends SpecialPage {
	
	public function __construct() {
		parent::__construct( 'Poll' );
	}

	public function execute( $par ) {
		global $wgRequest, $wgOut;

		$this->setHeaders();

		# Get request data from, e.g.
		$action = htmlentities( $wgRequest->getText( 'action' ) );
		$id = htmlentities( $wgRequest->getText( 'id' ) );

		if ( $action == "create" ) {
			$this->create();
		}

		if ( $action == "vote" ) {
			$this->vote();
		}

    	if ( $action == "submit" ) {
      		$this->submit();
    	}
	}
	public function create() {
		global $wgRequest, $wgOut, $wgUser;

		$wgOut->setPagetitle( wfMsg( 'poll-title-create' ) );

		if ( !$wgUser->isAllowed( 'poll-create' ) ) {
			$wgOut->addWikiMsg( 'poll-create-right-error' );
		}
		elseif ( $wgUser->isBlocked() ) {
			$wgOut->addWikiMsg( 'poll-create-block-error' );
		}
		else {
			$wgOut->addHtml( Xml::openElement( 'form', array('method'=> 'post', 'action' => 'index.php?title=Special:Poll&action=submit' ) ) );
			$wgOut->addHtml( Xml::openElement( 'table' ) );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-question' ).':</td><td>'.Xml::input('question').'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 1:</td><td>'.Xml::input('poll_alternative_1').'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 2:</td><td>'.Xml::input('poll_alternative_2').'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 3:</td><td>'.Xml::input('poll_alternative_3').'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 4:</td><td>'.Xml::input('poll_alternative_4').'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 5:</td><td>'.Xml::input('poll_alternative_5').'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 6:</td><td>'.Xml::input('poll_alternative_6').'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.Xml::submitButton(wfMsg( 'poll-submit' )).''.Xml::hidden('action', 'create').'</td></tr>' );
			$wgOut->addHtml( Xml::closeElement( 'table' ) );
			$wgOut->addHtml( Xml::closeElement( 'form' ) );
		}
	}
   
	public function vote() {
		global $wgOut, $wgUser;

		$wgOut->setPagetitle( wfMsg( 'poll-title-vote' ) );

		if ( !$wgUser->isAllowed( 'poll-vote' ); ) {
			$wgOut->addWikiMsg( 'poll-vote-right-error' );
		}
		elseif ( $wgUser-isBlocked() ) {
			$wgOut->addWikiMsg( 'poll-vote-block-error' );
		}
		else {
		}
	}

	public function submit() {
		global $wgRequest, $wgOut, $wgUser;

		$type = htmlenttitles($_POST['action']);
		
		if ( $type == 'create' && !$wgUser->isAllowed( 'poll-create' ) ) {
			$wgOut->addWikiMsg( 'poll-create-right-error' );
		}
		elseif ( $type == 'create' && $wgUser->isBlocked() ) {
			$wgOut->addWikiMsg( 'poll-create-block-error' );
		}
		else {
			$dbw = wfGetDB( DB_MASTER );
			$question = $_POST['question'];
			$alternative_1 = $_POST['poll_alternative_1'];
		    $alternative_2 = $_POST['poll_alternative_2'];
			$alternative_3 = ($_POST['poll_alternative_3'] != "")? $_POST['poll_alternative_3'] : "";
			$alternative_4 = ($_POST['poll_alternative_4'] != "")? $_POST['poll_alternative_4'] : "";
			$alternative_5 = ($_POST['poll_alternative_5'] != "")? $_POST['poll_alternative_5'] : "";
			$alternative_6 = ($_POST['poll_alternative_6'] != "")? $_POST['poll_alternative_6'] : "";
			
			if($question != "" && $alternative_1 != "" && $alternative_2 != "") {
            	$dbw->insert( 'poll', array( 'question' => $question, 'alternative_1' => $alternative_1, 'alternative_2' => $alternative_2,
				'alternative_3' => $alternative_3, 'alternative_4' => $alternative_4, 'alternative_5' => $alternative_5,
				'alternative_6' => $alternative_6 ) );
			}
			else {
				$wgOut->addWikiMsg( 'poll-create-fields-error' );
			}
		}
	}
}
