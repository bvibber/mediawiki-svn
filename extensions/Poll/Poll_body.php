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

    if($action == "create") {
        $this->create();
    }
    
    if($action == "vote") {
        $this->vote();
    }
	}
  
  public function create() {
      global $wgRequest, $wgOut, $wgUser;
      
      $wgOut->setPagetitle( wfMsg( 'poll-title-create' ) );
      
      $controll_create_right = $wgUser->isAllowed('poll-create');
      $controll_create_blocked = $wgUser->isBlocked();
      if( $controll_create_right != true ) {
          $wgOut->addWikiMsg( 'poll-create-right-error' );
      }
      elseif( $controll_create_blocked == true ) {
          $wgOut->addWikiMsg( 'poll-create-block-error' );
      }
      else {
          
      }
  }
  
   public function vote() {
      global $wgRequest, $wgOut, $wgUser;
      
      $wgOut->setPagetitle( wfMsg( 'poll-title-vote' ) );
      
      $controll_create_right = $wgUser->isAllowed('poll-vote');
      $controll_create_blocked = $wgUser->isBlocked();
      if( $controll_create_right != true ) {
          $wgOut->addWikiMsg( 'poll-vote-right-error' );
      }
      elseif( $controll_create_blocked == true ) {
          $wgOut->addWikiMsg( 'poll-vote-block-error' );
      }
      else {
          
      }
  }
}
