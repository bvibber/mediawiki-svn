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

		wfLoadExtensionMessages( 'Poll' );

		$this->setHeaders();

		# Get request data from, e.g.
		$action = htmlentities( $wgRequest->getText( 'action' ) );
		$id = htmlentities( $wgRequest->getText( 'id' ) );

		if ( $action == "" OR $action == "list" ) {
			$this->make_list();
		}

		if ( $action == "create" ) {
			$this->create();
		}

		if ( $action == "vote" ) {
			$this->vote( $id );
		}

		if ( $action == "score" ) {
			$this->score( $id );
		}

		if ( $action == "change" ) {
			$this->change( $id );
		}

		if ( $action == "delete" ) {
			$this->delete( $id );
		}

		if ( $action == "submit" ) {
			$this->submit( $id );
		}
	}

	public function make_list() {
		global $wgRequest, $wgOut, $wgUser, $wgTitle;
		$wgOut->setPagetitle( wfMsg( 'poll' ) );

		$dbr = wfGetDB( DB_SLAVE );
		$query = $dbr->select( 'poll', 'question, dis, id' );

		$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=create').'">'.wfMsg( 'poll-create-link' ).'</a>' );

		$wgOut->addWikiMsg( 'poll-list-current' );
		$wgOut->addHtml( Xml::openElement( 'table' ) );
		$wgOut->addHtml( '<tr><th>'.wfMsg( 'poll-question' ).'</th><th>'.wfMsg( 'poll-dis' ).'</th><th>&nbsp;</th></tr>' );

		while( $row = $dbr->fetchObject( $query ) ) {
			$wgOut->addHtml( '<tr><td><a href="'.$wgTitle->getFullURL( 'action=vote&id='.$row->id ).'">'.htmlentities( $row->question, ENT_QUOTES, "UTF-8" ).'</a></td>' );
			$wgOut->addHtml( '<td>'.$row->dis.'</td>' );
			$wgOut->addHtml( '<td><a href="'.$wgTitle->getFullURL( 'action=score&id='.$row->id ).'">'.wfMsg( 'poll-title-score' ).'</a></td></tr>' );
		}

		$wgOut->addHtml( Xml::closeElement( 'table' ) );

	}

	public function create() {
		global $wgRequest, $wgOut, $wgUser, $wgTitle;

		$wgOut->setPagetitle( wfMsg( 'poll-title-create' ) );

		$controll_create_right = $wgUser->isAllowed( 'poll-create' );
		$controll_create_blocked = $wgUser->isBlocked();
		if ( $controll_create_right != true ) {
			$wgOut->addWikiMsg( 'poll-create-right-error' );
			$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
		}
		elseif ( $controll_create_blocked == true ) {
			$wgOut->addWikiMsg( 'poll-create-block-error' );
			$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
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
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-dis' ).':</td><td>'.Xml::textarea('dis', '').'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.Xml::check('allow_more').' 6:</td><td>'.wfMsg( 'poll-create-allow-more' ).'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.Xml::submitButton(wfMsg( 'poll-submit' )).''.Xml::hidden('type', 'create').'</td></tr>' );
			$wgOut->addHtml( Xml::closeElement( 'table' ) );
			$wgOut->addHtml( Xml::closeElement( 'form' ) );
		}
	}

	public function vote( $vid ) {
		global $wgRequest, $wgOut, $wgUser, $wgTitle;

		$wgOut->setPagetitle( wfMsg( 'poll-title-vote' ) );

		$controll_vote_right = $wgUser->isAllowed( 'poll-vote' );
		$controll_vote_blocked = $wgUser->isBlocked();
		if ( $controll_vote_right != true ) {
			$wgOut->addWikiMsg( 'poll-vote-right-error' );
			$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
		}
		elseif ( $controll_vote_blocked == true ) {
			$wgOut->addWikiMsg( 'poll-vote-block-error' );
			$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
		}
		else {
			$dbr = wfGetDB( DB_SLAVE );
			$query = $dbr->select( 'poll', 'question, alternative_1, alternative_2, alternative_3, alternative_4, alternative_5, alternative_6, creater', array( 'id' => $vid ) );
			$poll_admin = $wgUser->isAllowed( 'poll-admin' );
			$user = $wgUser->getName();

			while( $row = $dbr->fetchObject( $query ) ) {
				$question = htmlentities( $row->question, ENT_QUOTES, 'UTF-8' );
				$alternative_1 = htmlentities( $row->alternative_1, ENT_QUOTES, 'UTF-8'  );
				$alternative_2 = htmlentities( $row->alternative_2, ENT_QUOTES, 'UTF-8'  );
				$alternative_3 = htmlentities( $row->alternative_3, ENT_QUOTES, 'UTF-8'  );
				$alternative_4 = htmlentities( $row->alternative_4, ENT_QUOTES, 'UTF-8'  );
				$alternative_5 = htmlentities( $row->alternative_5, ENT_QUOTES, 'UTF-8'  );
				$alternative_6 = htmlentities( $row->alternative_6, ENT_QUOTES, 'UTF-8'  );
				$creater = htmlentities( $row->creater, ENT_QUOTES, 'UTF-8'  );
			}

			$wgOut->addHtml( Xml::openElement( 'form', array('method'=> 'post', 'action' => $wgTitle->getFullURL('action=submit&id='.$vid) ) ) );
			$wgOut->addHtml( Xml::openElement( 'table' ) );
			$wgOut->addHtml( '<tr><th>'.$question.'</th></tr>' );
			$wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '1').' '.$alternative_1.'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '2').' '.$alternative_2.'</td></tr>' );
			if($alternative_3 != "") { $wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '3').' '.$alternative_3.'</td></tr>' ); }
			if($alternative_4 != "") { $wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '4').' '.$alternative_4.'</td></tr>' ); }
			if($alternative_5 != "") { $wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '5').' '.$alternative_5.'</td></tr>' ); }
			if($alternative_6 != "") { $wgOut->addHtml( '<tr><td>'.Xml::radio('vote', '6').' '.$alternative_6.'</td></tr>' ); }
			$wgOut->addHtml( '<tr><td>'.Xml::submitButton(wfMsg( 'poll-submit' )).''.Xml::hidden('type', 'vote').'</td><td><a href="'.$wgTitle->getFullURL( 'action=score&id='.$vid ).'">'.wfMsg( 'poll-title-score' ).'</a></td></tr>' );
			$wgOut->addHtml( '<tr><td>' );
			$wgOut->addWikiText( '<small>'.wfMsg( 'poll-score-created', $creater ).'</small>' );
			$wgOut->addHtml( '</td></tr>' );
			$wgOut->addHtml( Xml::closeElement( 'table' ) );
			if( ($poll_admin == true) OR ($creater == $user) ) {
				$wgOut->addHtml( wfMsg('poll-administration').' <a href="'.$wgTitle->getFullURL('action=change&id='.$vid).'">'.wfMsg('poll-change').'</a> · <a href="'.$wgTitle->getFullURL('action=delete&id='.$vid).'">'.wfMsg('poll-delete').'</a>' );
			}
			$wgOut->addHtml( Xml::closeElement( 'form' ) );
		}
	}

	public function score( $sid ) {
		global $wgRequest, $wgOut, $wgUser, $wgTitle;

		$wgOut->setPagetitle( wfMsg( 'poll-title-score' ) );

		$dbr = wfGetDB( DB_SLAVE );
		$query = $dbr->select( 'poll', 'question, alternative_1, alternative_2, alternative_3, alternative_4, alternative_5, alternative_6, creater', array( 'id' => $sid ) );
		$query_1 = $dbr->select( 'poll_answer', 'uid', array( 'vote' => '1', 'uid' => $sid ) );
		$query_2 = $dbr->select( 'poll_answer', 'uid', array( 'vote' => '2', 'uid' => $sid ) );
		$query_3 = $dbr->select( 'poll_answer', 'uid', array( 'vote' => '3', 'uid' => $sid ) );
		$query_4 = $dbr->select( 'poll_answer', 'uid', array( 'vote' => '4', 'uid' => $sid ) );
		$query_5 = $dbr->select( 'poll_answer', 'uid', array( 'vote' => '5', 'uid' => $sid ) );
		$query_6 = $dbr->select( 'poll_answer', 'uid', array( 'vote' => '6', 'uid' => $sid ) );

		while( $row = $dbr->fetchObject( $query ) ) {
			$question = htmlentities( $row->question, ENT_QUOTES, 'UTF-8' );
			$alternative_1 = htmlentities( $row->alternative_1, ENT_QUOTES, 'UTF-8'  );
			$alternative_2 = htmlentities( $row->alternative_2, ENT_QUOTES, 'UTF-8'  );
			$alternative_3 = htmlentities( $row->alternative_3, ENT_QUOTES, 'UTF-8'  );
			$alternative_4 = htmlentities( $row->alternative_4, ENT_QUOTES, 'UTF-8'  );
			$alternative_5 = htmlentities( $row->alternative_5, ENT_QUOTES, 'UTF-8'  );
			$alternative_6 = htmlentities( $row->alternative_6, ENT_QUOTES, 'UTF-8'  );
			$creater = htmlentities( $row->creater, ENT_QUOTES, 'UTF-8'  );
		}

		$query_num_1 = $dbr->numRows( $query_1 );
		$query_num_2 = $dbr->numRows( $query_2 );
		$query_num_3 = $dbr->numRows( $query_3 );
		$query_num_4 = $dbr->numRows( $query_4 );
		$query_num_5 = $dbr->numRows( $query_5 );
		$query_num_6 = $dbr->numRows( $query_6 );

		$wgOut->addHtml( Xml::openElement( 'table' ) );
		$wgOut->addHtml( '<tr><th><center>'.$question.'</center></th></tr>' );;
		$wgOut->addHtml( '<tr><td>'.$alternative_1.'</td><td>'.$query_num_1.'</td></tr>' );
		$wgOut->addHtml( '<tr><td>'.$alternative_2.'</td><td>'.$query_num_2.'</td></tr>' );
		if($alternative_3 != "") { $wgOut->addHtml( '<tr><td>'.$alternative_3.'</td><td>'.$query_num_3.'</td></tr>' ); }
		if($alternative_4 != "") { $wgOut->addHtml( '<tr><td>'.$alternative_4.'</td><td>'.$query_num_4.'</td></tr>' ); }
		if($alternative_5 != "") { $wgOut->addHtml( '<tr><td>'.$alternative_5.'</td><td>'.$query_num_5.'</td></tr>' ); }
		if($alternative_6 != "") { $wgOut->addHtml( '<tr><td>'.$alternative_6.'</td><td>'.$query_num_6.'</td></tr>' ); }
		$wgOut->addHtml( '<tr><td>' );
		$wgOut->addWikiText( '<small>'.wfMsg( 'poll-score-created', $creater ).'</small>' );
		$wgOut->addHtml( '</td></tr>' );
		$wgOut->addHtml( Xml::closeElement( 'table' ) );
		$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
	}

	public function delete( $did ) {
		global $wgRequest, $wgOut, $wgUser, $wgTitle;
		$wgOut->setPagetitle( wfMsg( 'poll-title-delete' ) );

		$dbr = wfGetDB( DB_SLAVE );
		$query = $dbr->select( 'poll', 'question', array( 'id' => $did ) );

		while( $row = $dbr->fetchObject( $query ) ) {
			$question = htmlentities( $row->question, ENT_QUOTES, 'UTF-8' );
		}

		$wgOut->addHtml( Xml::openElement( 'form', array('method'=> 'post', 'action' => $wgTitle->getFullURL('action=submit&id='.$did) ) ) );
		$wgOut->addHtml( Xml::check( 'controll_delete' ).' '.wfMsg('poll-delete-question', $question).'<br />' );
		$wgOut->addHtml( Xml::submitButton(wfMsg( 'poll-submit' )).' <a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>'.Xml::hidden('type', 'delete') );
		$wgOut->addHtml( Xml::closeElement( 'form' ) );
	}

	public function change($cid) {
		global $wgRequest, $wgOut, $wgUser, $wgTitle;

		$wgOut->setPagetitle( wfMsg( 'poll-title-change' ) );

		$dbr = wfGetDB( DB_SLAVE );
		$query = $dbr->select( 'poll', 'question, alternative_1, alternative_2, alternative_3, alternative_4, alternative_5, alternative_6, creater, dis', array( 'id' => $cid ) );
		$user = $wgUser->getName();

		while( $row = $dbr->fetchObject( $query ) ) {
			$question = $row->question;
			$alternative_1 = $row->alternative_1;
			$alternative_2 = $row->alternative_2;
			$alternative_3 = $row->alternative_3;
			$alternative_4 = $row->alternative_4;
			$alternative_5 = $row->alternative_5;
			$alternative_6 = $row->alternative_6;
			$creater = $row->creater;
			$dis = $row->dis;
		}

		$controll_create_blocked = $wgUser->isBlocked();
		if ( $user != $creater ) {
			$wgOut->addWikiMsg( 'poll-change-right-error' );
			$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
		}
		elseif ( $controll_create_blocked == true ) {
			$wgOut->addWikiMsg( 'poll-change-block-error' );
			$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
		}
		else {
			$wgOut->addHtml( Xml::openElement( 'form', array('method'=> 'post', 'action' => $wgTitle->getFullURL('action=submit&id='.$cid) ) ) );
			$wgOut->addHtml( Xml::openElement( 'table' ) );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-question' ).':</td><td>'.Xml::input('question', false, $question).'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 1:</td><td>'.Xml::input('poll_alternative_1', false, $alternative_1).'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 2:</td><td>'.Xml::input('poll_alternative_2', false, $alternative_2).'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 3:</td><td>'.Xml::input('poll_alternative_3', false, $alternative_3).'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 4:</td><td>'.Xml::input('poll_alternative_4', false, $alternative_4).'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 5:</td><td>'.Xml::input('poll_alternative_5', false, $alternative_5).'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-alternative' ).' 6:</td><td>'.Xml::input('poll_alternative_6', false, $alternative_6).'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.wfMsg( 'poll-dis' ).':</td><td>'.Xml::textarea('dis', $dis).'</td></tr>' );
			$wgOut->addHtml( '<tr><td>'.Xml::submitButton(wfMsg( 'poll-submit' )).''.Xml::hidden('type', 'change').'</td></tr>' );
			$wgOut->addHtml( Xml::closeElement( 'table' ) );
			$wgOut->addHtml( Xml::closeElement( 'form' ) );
		}
	}

	public function submit( $pid ) {
		global $wgRequest, $wgOut, $wgUser, $wgTitle;

		$type = $wgRequest->getVal('type');

		if($type == 'create') {
			$controll_create_right = $wgUser->isAllowed( 'poll-create' );
			$controll_create_blocked = $wgUser->isBlocked();
			if ( $controll_create_right != true ) {
				$wgOut->addWikiMsg( 'poll-create-right-error' );
				$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
			}
			elseif ( $controll_create_blocked == true ) {
				$wgOut->addWikiMsg( 'poll-create-block-error' );
				$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
			}

			else {
				$dbw = wfGetDB( DB_MASTER );
				$question = $wgRequest->getVal('question');
				$alternative_1 = $wgRequest->getVal('poll_alternative_1');
				$alternative_2 = $wgRequest->getVal('poll_alternative_2');
				$alternative_3 = ($wgRequest->getVal('poll_alternative_3') != "")? $wgRequest->getVal('poll_alternative_3') : "";
				$alternative_4 = ($wgRequest->getVal('poll_alternative_4') != "")? $wgRequest->getVal('poll_alternative_4') : "";
				$alternative_5 = ($wgRequest->getVal('poll_alternative_5') != "")? $wgRequest->getVal('poll_alternative_5') : "";
				$alternative_6 = ($wgRequest->getVal('poll_alternative_6') != "")? $wgRequest->getVal('poll_alternative_6') : "";
				$dis = ($wgRequest->getVal('dis') != "")? $wgRequest->getVal( 'dis' ) : wfMsg('poll-no-dis');
				$user = $wgUser->getName();

				if($question != "" && $alternative_1 != "" && $alternative_2 != "") {
					$dbw->insert( 'poll', array( 'question' => $question, 'alternative_1' => $alternative_1, 'alternative_2' => $alternative_2,
					'alternative_3' => $alternative_3, 'alternative_4' => $alternative_4, 'alternative_5' => $alternative_5,
					'alternative_6' => $alternative_6, 'creater' => $user, 'dis' => $dis ) );

					$log = new LogPage( "poll" );
					$title = $wgTitle;
					$log->addEntry( "poll", $title, wfMsg( 'poll-log-create', "[[User:".htmlentities( $user, ENT_QUOTES, 'UTF-8' )."]]", htmlentities( $question, ENT_QUOTES, 'UTF-8' ) ) );

					$wgOut->addWikiMsg( 'poll-create-pass' );
					$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
				}
				else {
					$wgOut->addWikiMsg( 'poll-create-fields-error' );
					$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
				}
			}
		}

		if($type == 'vote') {
			$controll_vote_right = $wgUser->isAllowed( 'poll-vote' );
			$controll_vote_blocked = $wgUser->isBlocked();
			if ( $controll_vote_right != true ) {
				$wgOut->addWikiMsg( 'poll-vote-right-error' );
				$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
			}
			elseif ( $controll_vote_blocked == true ) {
				$wgOut->addWikiMsg( 'poll-vote-block-error' );
				$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
			}

			else {
				$dbw = wfGetDB( DB_MASTER );
				$dbr = wfGetDB( DB_SLAVE );
				$vote = $wgRequest->getVal('vote');
				$user = $wgUser->getName();
				$uid = $wgUser->getId();

				$query = $dbr->select( 'poll_answer', 'uid', array( 'uid' => $uid ));
				$num = 0;

				while( $row = $dbr->fetchObject( $query ) ) {
					if($row->uid != "") {
						$num++;
					}
				}

				if( $num == 0 ) {
					$dbw->insert( 'poll_answer', array( 'pid' => $pid, 'uid' => $uid, 'vote' => $vote, 'user' => $user ) );

					$wgOut->addWikiMsg( 'poll-vote-pass' );
					$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
				}
				else {
					$wgOut->addWikiMsg( 'poll-vote-already-error' );
					$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
				}
			}
		}

		if($type == 'change') {
			$dbr = wfGetDB( DB_SLAVE );
			$query = $dbr->select( 'poll', 'creater', array( 'id' => $pid ) );
			$user = $wgUser->getName();

			while( $row = $dbr->fetchObject( $query ) ) {
				$creater = htmlentities( $row->creater );
			}

			$controll_change_right = $wgUser->isAllowed( 'poll-admin' );
			$controll_change_blocked = $wgUser->isBlocked();

			$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );

			if ( ( $creater != $user ) AND ( $controll_change_right == false ) ) {
				$wgOut->addWikiMsg( 'poll-change-right-error' );
				$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
			}

			if ( $controll_change_blocked == true ) {
				$wgOut->addWikiMsg( 'poll-change-block-error' );
				$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
			}

			if ( ( ( $creater == $user ) OR ( $controll_change_right == true ) ) AND ( $controll_change_blocked != true ) )  {
				$dbw = wfGetDB( DB_MASTER );
				$question = $wgRequest->getVal('question');
				$alternative_1 = $wgRequest->getVal('poll_alternative_1');
				$alternative_2 = $wgRequest->getVal('poll_alternative_2');
				$alternative_3 = ($wgRequest->getVal('poll_alternative_3') != "")? $wgRequest->getVal('poll_alternative_3') : "";
				$alternative_4 = ($wgRequest->getVal('poll_alternative_4') != "")? $wgRequest->getVal('poll_alternative_4') : "";
				$alternative_5 = ($wgRequest->getVal('poll_alternative_5') != "")? $wgRequest->getVal('poll_alternative_5') : "";
				$alternative_6 = ($wgRequest->getVal('poll_alternative_6') != "")? $wgRequest->getVal('poll_alternative_6') : "";
				$dis = ($wgRequest->getVal('dis') != "")? $wgRequest->getVal('dis') : wfMsg('poll-no-dis');
				$user = $wgUser->getName();

				$dbw->update( 'poll', array( 'question' => $question, 'alternative_1' => $alternative_1, 'alternative_2' => $alternative_2,
				'alternative_3' => $alternative_3, 'alternative_4' => $alternative_4, 'alternative_5' => $alternative_5,
				'alternative_6' => $alternative_6, 'creater' => $user, 'dis' => $dis ), array( 'id' => $pid ) );

				$log = new LogPage( "poll" );
				$title = $wgTitle;
				$log->addEntry( "poll", $title, wfMsg( 'poll-log-change', "[[User:".htmlentities( $user, ENT_QUOTES, 'UTF-8' )."]]", htmlentities( $question, ENT_QUOTES, 'UTF-8' ) ) );

				$wgOut->addWikiMsg( 'poll-change-pass' );
				$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
			}
		}

		if($type == 'delete') {
			$dbr = wfGetDB( DB_SLAVE );
			$query = $dbr->select( 'poll', 'creater, question', array( 'id' => $pid ) );
			$user = $wgUser->getName();

			while( $row = $dbr->fetchObject( $query ) ) {
				$creater = htmlentities( $row->creater );
				$question = $row->question;
			}

			$controll_delete_right = $wgUser->isAllowed( 'poll-admin' );
			$controll_delete_blocked = $wgUser->isBlocked();

			if ( ( $creater != $user ) AND ( $controll_delete_right == false ) ) {
				$wgOut->addWikiMsg( 'poll-delete-right-error' );
				$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
			}

			if ( $controll_delete_blocked == true ) {
				$wgOut->addWikiMsg( 'poll-delete-block-error' );
				$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
			}

			if ( ( ( $creater == $user ) OR ( $controll_delete_right == true ) ) AND ( $controll_delete_blocked != true ) )  {
				if( $wgRequest->getCheck('controll_delete') AND $wgRequest->getVal('controll_delete') === 1 ) {
					$dbw = wfGetDB( DB_MASTER );
					$user = $wgUser->getName();

					$dbw->delete( 'poll', array( 'id' => $pid ) );
					$dbw->delete( 'poll_answer', array( 'uid' => $pid ) );

					$log = new LogPage( "poll" );
					$title = $wgTitle;
					$log->addEntry( "poll", $title, wfMsg( 'poll-log-delete', "[[User:".htmlentities( $user, ENT_QUOTES, 'UTF-8' )."]]", htmlentities( $question, ENT_QUOTES, 'UTF-8' ) ) );

					$wgOut->addWikiMsg( 'poll-delete-pass' );
					$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
				}
				else {
					$wgOut->addWikiMsg( 'poll-delete-cancel' );
					$wgOut->addHtml( '<a href="'.$wgTitle->getFullURL('action=list').'">'.wfMsg('poll-back').'</a>' );
				}
			}
		}
	}
}
