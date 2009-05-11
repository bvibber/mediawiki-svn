<?php

class Poll extends SpecialPage {
	function __construct() {
		parent::__construct( 'Poll' );
		wfLoadExtensionMessages( 'Poll' );
	}

	function execute( $par ) {
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
      
      $controll_create_right = $wgUser->isAllowed( 'poll-create' );
      $controll_create_blocked = $wgUser->isBlocked();
      if ( $controll_create_right != true ) {
          $wgOut->addWikiMsg( 'poll-create-right-error' );
      }
      elseif ( $controll_create_blocked == true ) {
          $wgOut->addWikiMsg( 'poll-create-block-error' );
      }
      else {
          $wgOut->addHtml( '<form action="index.php?title=Special:Poll&action=submit" method="post">' );
          $wgOut->addHtml( '<table style="border: 0px;">' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-question' ).':</td><td><input type="text" name="poll_name"></td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 1:</td><td><input type="text" name="poll_alternative_1"></td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 2:</td><td><input type="text" name="poll_alternative_2"></td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 3:</td><td><input type="text" name="poll_alternative_3"></td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 4:</td><td><input type="text" name="poll_alternative_4"></td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 5:</td><td><input type="text" name="poll_alternative_5"></td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 6:</td><td><input type="text" name="poll_alternative_6"></td></tr>' );
          $wgOut->addHtml( '<tr><td><input type="submit" value="'.wfMsg( 'poll-submit' ).'"></td></tr>' );
          $wgOut->addHtml( '</table>' );
          $wgOut->addHtml( '</form>' );
      }
  }
  
   public function vote() {
      global $wgRequest, $wgOut, $wgUser;
      
      $wgOut->setPagetitle( wfMsg( 'poll-title-vote' ) );
      
      $controll_create_right = $wgUser->isAllowed( 'poll-vote' );
      $controll_create_blocked = $wgUser->isBlocked();
      if ( $controll_create_right != true ) {
          $wgOut->addWikiMsg( 'poll-vote-right-error' );
      }
      elseif ( $controll_create_blocked == true ) {
          $wgOut->addWikiMsg( 'poll-vote-block-error' );
      }
      else {
          
      }
  }
  
  public function submit() {
      global $wgRequest, $wgOut, $wgUser;
  }
}
