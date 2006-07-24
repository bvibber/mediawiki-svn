<?php

/**
* @package MediaWiki
* @subpackage Extensions
* @author David McCabe <davemccabe@gmail.com>
* @licence GPL2
*/


if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( -1 );
}
else {

	require_once('ThreadView.php');
	require_once('Post.php');

	require_once( 'SpecialPage.php' );

	$wgExtensionFunctions[] = 'efLQ';

	function lqSpecialCaseHook( &$title, &$output, $request ) {
	     if( $title->getNamespace() === LQT_NS_CHANNEL ) {
		  $lq = new LQ($title);
		  $lq->execute();
		  return false;
	     }
	     else {
		  return true;
	     }
	}

	function efLQ() {
	     global $wgMessageCache, $wgHooks;
	     $wgHooks['SpecialCase'][] = 'lqSpecialCaseHook';
	     $wgMessageCache->addMessage( 'lq', 'LiquidThreads' );
	     SpecialPage::addPage( new LQ(null) );
	}


	class LQ extends SpecialPage {

		function LQ($talkTitle) {
		     global $wgSkin;
		     if ( $talkTitle ) {
			  $this->talkTitle = $talkTitle;
			  $this->title = Title::makeTitle( NS_MAIN, $talkTitle->getDBkey() );
		     }
		     SpecialPage::SpecialPage( 'LQ', 'lq' );
		}

		function execute() {
			global $wgUser, $wgRequest, $wgOut, $wgArticle;

			// SpecialPage::setHeaders
			$this->setHeaders();

			$article = new Post($this->title); // post so we can do firstPostOfArticle() etc.

			$first_post = Post::firstPostOfArticle($article);

			// Execute move operations:
			// TODO find a better home for this.
			$post_id     = $wgRequest->getInt( 'lqt_move_post_id',  false );
			$reply_to_id = $wgRequest->getInt( 'lqt_move_to_reply', false );
			$next_to_id  = $wgRequest->getInt( 'lqt_move_to_next',  false );
			$to_id = $reply_to_id ? $reply_to_id : $next_to_id;
			if ( $post_id && $to_id ) {
				$posttitle = Title::newFromID($post_id);
				$totitle = Title::newFromID($to_id);
				$post = new Post( $posttitle );
				$to = new Post( $totitle );
				$post->moveNextTo($to, $reply_to_id ? 'reply' : 'next');

				// Wipe out POST so user doesn't get the "Danger Will
				// Robinson there's POST data" message when refreshing the page.
				$query = "lqt_highlight={$posttitle->getPartialURL()}#lqt_post_{$posttitle->getPartialURL()}";
				$wgOut->redirect( $this->talkTitle->getFullURL($query) );
				return;
			}

			$moving = $wgRequest->getInt('lqt_moving_id');
			$editing_id = $wgRequest->getInt("lqt_editing", null);
			$history_id = $wgRequest->getInt("lqt_show_history_id", null);
			$replying_to_id = $wgRequest->getInt("lqt_replying_to_id", null);
			$highlighting_title = $wgRequest->getVal("lqt_highlight", null);

			$view = new ThreadView($this->talkTitle, $this->title, $editing_id, $replying_to_id, $history_id, $highlighting_title,
			$moving);


			if ( $wgRequest->getBool("lqt_post_new", false) ) {
				$view->newPostEditingForm(null);
			}  else {
				 $wgOut->addHTML( wfElement('a',
							    array('href'=>$this->talkTitle->getLocalURL("lqt_post_new=1")),
							    "Post New Thread") );
			}

			if ( $moving ) {
			     if( $moving != $first_post->getID() ) {
                                  // Very first 'move here' button at top of page:
				  $wgOut->addHTML( wfOpenElement('p') );
				  $view->showMoveButton( 'next', $article->getID() );
				  $wgOut->addHTML( wfCloseElement('p') );
			     }
			}
			
			if ($first_post) {
			     $view->renderThreadStartingFrom( $first_post );
			} else {
			     $wgOut->addWikiText("This talk page is empty.");
			}
			
			$wgOut->setPageTitle($this->talkTitle->getPrefixedText());
		}
	}
	
}
?>
