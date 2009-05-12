<?php
/**
 * Poll_body - Body for the Special Page Special:Poll
 *
 * @ingroup Extensions
 * @author Jan Luca <jan@toolserver.org>
 * @license http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported or later
 */


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
			$this->vote( $id );
		}

    	if ( $action == "submit" ) {
      		$this->submit();
    	}
	}

  public function create() {
      global $wgRequest, $wgOut, $wgUser, $wgTitle;
      
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
          $wgOut->addHtml( Xml::openElement( 'form', array('method'=> 'post', 'action' => $wgTitle->getFullURL('action=submit') ) ) );
          $wgOut->addHtml( Xml::openElement( 'table' ) );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-question' ).':</td><td>'.Xml::input('question').'</td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 1:</td><td>'.Xml::input('poll_alternative_1').'</td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 2:</td><td>'.Xml::input('poll_alternative_2').'</td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 3:</td><td>'.Xml::input('poll_alternative_3').'</td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 4:</td><td>'.Xml::input('poll_alternative_4').'</td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 5:</td><td>'.Xml::input('poll_alternative_5').'</td></tr>' );
          $wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 6:</td><td>'.Xml::input('poll_alternative_6').'</td></tr>' );
          $wgOut->addHtml( '<tr><td>'.Xml::submitButton(wfMsg( 'poll-submit' )).''.Xml::hidden('type', 'create').'</td></tr>' );
          $wgOut->addHtml( Xml::closeElement( 'table' ) );
          $wgOut->addHtml( Xml::closeElement( 'form' ) );
      }
  }
  
   public function vote( $vid ) {
      global $wgRequest, $wgOut, $wgUser, $wgTitle;
      
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
          $dbr = wfGetDB( DB_SLAVE );
		  $query = $dbr->select( 'poll', 'question, alternative_1, alternative_2, alternative_3, alternative_4, alternative_5, alternative_6', 'id = ' . $vid);
		  
		  while( $row = $dbr->fetchObject( $query ) ) {
		      $question = $row->question;
			  $alternative_1 = htmlentities( $row->alternative_1 );
			  $alternative_2 = htmlentities( $row->alternative_2 );
			  $alternative_3 = htmlentities( $row->alternative_3 );
			  $alternative_4 = htmlentities( $row->alternative_4 );
			  $alternative_5 = htmlentities( $row->alternative_5 );
			  $alternative_6 = htmlentities( $row->alternative_6 );
		  }
		  
		  $wgOut->addHtml( Xml::openElement( 'form', array('method'=> 'post', 'action' => $wgTitle->getFullURL('action=submit') ) ) );
          $wgOut->addHtml( Xml::openElement( 'table' ) );
		  $wgOut->addHtml( '<tr><th>'.$question.'</th></tr>' );
          $wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '1').':</td><td>'.$alternative_1.'</td></tr>' );
		  $wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '2').':</td><td>'.$alternative_2.'</td></tr>' );
		  if($alternative_3 != "") { $wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '3').':</td><td>'.$alternative_3.'</td></tr>' ) }
		  if($alternative_4 != "") { $wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '4').':</td><td>'.$alternative_4.'</td></tr>' ) }
		  if($alternative_5 != "") { $wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '5').':</td><td>'.$alternative_5.'</td></tr>' ) }
		  if($alternative_6 != "") { $wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '6').':</td><td>'.$alternative_6.'</td></tr>' ) }
          $wgOut->addHtml( '<tr><td>'.Xml::submitButton(wfMsg( 'poll-submit' )).''.Xml::hidden('type', 'vote').'</td></tr>' );
          $wgOut->addHtml( Xml::closeElement( 'table' ) );
          $wgOut->addHtml( Xml::closeElement( 'form' ) );
      }
  }
  
  public function submit() {
      global $wgRequest, $wgOut, $wgUser;
	  
	  $type = $_POST['type'];
	  
	  if($type == 'create') {
	    $controll_create_right = $wgUser->isAllowed( 'poll-create' );
        $controll_create_blocked = $wgUser->isBlocked();
        if ( $controll_create_right != true ) {
            $wgOut->addWikiMsg( 'poll-create-right-error' );
        }
        elseif ( $controll_create_blocked == true ) {
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
}
