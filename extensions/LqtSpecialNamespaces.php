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
     require_once('Thread.php');
     
     require_once( 'SpecialPage.php' );
     $wgExtensionFunctions[] = 'lqtInitialize';

     function lqtSpecialCaseHook( &$title, &$output, $request ) {
	  if( $title->getNamespace() === LQT_NS_THREAD ) {
	       $t = new ThreadPermalink($title);
	       $t->execute();
	       return false;
	  }
	  else if( $title->getNamespace() === LQT_NS_CHANNEL ) {
	       $lq = new Channel($title);
	       $lq->execute();
	       return false;
	  }
	  else {
	       return true;
	  }
     }
     
     function lqtInitialize() {
	  global $wgMessageCache, $wgHooks;
	  $wgMessageCache->addMessage( 'lq', 'LiquidThreads' );
	  $wgHooks['SpecialCase'][] = 'lqtSpecialCaseHook';
	  SpecialPage::addPage( new ThreadPermalink(null) );
	  SpecialPage::addPage( new Channel(null) );
     }

	class Channel extends SpecialPage {

	     function archiveBrowserForm( $month_now_shown ) {
		  global $wgOut, $wgLang;
		  
		  $article = new Post($this->title);
		  $months = Thread::monthsWithThreadsInArticle( $article );

		  $showing_recent = ($month_now_shown == 'recent') || !$month_now_shown;
		  $current_index = array_search($month_now_shown, $months);
		  
		  $wgOut->addHTML(
		       wfOpenElement('div', array('class'=>'lqt_archive_browser')) .
		       wfOpenElement('form', array('action'=>$this->talkTitle->getFUllURL())) .
		       wfOpenElement('select', array('name'=>'lqt_show_month')));

		  $recent_option_attribs = $showing_recent ? array('value'=>'recent', 'selected'=>'true')
		                                           : array('value'=>'recent');
		  $wgOut->addHTML(
		       wfElement( 'option', $recent_option_attribs, "Last 30 days" )
		       );

		  foreach( $months as $yyyymm ) {
		       $display_month = $wgLang->getMonthName( substr($yyyymm, 4, 2) ) . ' ' . substr($yyyymm, 0, 4);
		       $attribs = ($month_now_shown==$yyyymm) ? array('value'=>$yyyymm, 'selected'=>'true')
			                                      : array('value'=>$yyyymm);
		       $wgOut->addHTML(wfElement( 'option', $attribs, $display_month ));
		  }

		  $wgOut->addHTML(
		       wfCloseElement('select') .
		       wfSubmitButton('Go') .
		       wfCloseElement('form') .
		       wfOpenElement('form', array()) .
//		       wfInput('lqt_search', false, 'Search', array('style'=>'color: #999; margin-left: 1in;')) .
		       wfCloseElement('form') .
		       wfCloseElement('div')
		       );
		  

		  
	     }

		function Channel($talkTitle) {
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
			
			// Figure out what range of dates we're going to display:
			$month_now_shown = $wgRequest->getVal('lqt_show_month', null);
			if( !$month_now_shown || $month_now_shown == 'recent' ) {
			     $first_day = wfTimestamp(TS_MW, strtotime('-30 days'));
			     $first_day = substr($first_day, 0, 8) . '000000'; // start at midnight.
			     $last_day = wfTimestampNow();
			} else {
			     // Shouldn't matter that not all months have 31 days; it's just alphabetical sorting.
			     $first_day = $month_now_shown . '00000000';
			     $last_day = $month_now_shown . '31235959';
			}
			
			$threads = Thread::threadsOfArticle($article, $first_day, $last_day);

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

			$wgOut->addHTML( wfOpenElement( 'div', array('class'=>'lqt_channel_header') ) );

			if ( $wgRequest->getBool("lqt_post_new", false) ) {
			     ThreadView::newThreadForm( $article, $this->talkTitle );
//			     ThreadView::newPostEditingForm( $this->talkTitle, $article );
			}  else {
				 $wgOut->addHTML( wfElement('a',
							    array('href'=>$this->talkTitle->getLocalURL("lqt_post_new=1")),
							    "Post New Thread") );
			}

			$this->archiveBrowserForm($month_now_shown);

			$wgOut->addHTML( wfCloseElement( 'div' ) );

			$wgOut->addHTML( wfOpenElement( 'div', array('class'=>'lqt_channel_body') ) );

			if ( $moving ) {
			     if( $moving != $first_post->getID() ) {
                                  // Very first 'move here' button at top of page:
				  $wgOut->addHTML( wfOpenElement('p') );
				  $view->showMoveButton( 'next', $article->getID() );
				  $wgOut->addHTML( wfCloseElement('p') );
			     }
			}

			if ($threads ) {
			     foreach ( $threads as $t ) {
				  $view = new ThreadView( $this->talkTitle, $t, null, $editing_id, $replying_to_id, $history_id, $highlighting_title, $moving );
				  $view->render();
			     }
			} else {
			     $wgOut->addWikiText("This talk page is empty.");
			}
			
			$wgOut->addHTML( wfCloseElement( 'div' ) );			

			$wgOut->setPageTitle($this->talkTitle->getPrefixedText());
		}
	}

     
     class ThreadPermalink extends SpecialPage {

	  /* TODO: these appearently are not used; they are from pre-Titles. */
	  static $article;
	  static $titleString;
	  static $moving;
	  
	  /** top-level post title. */
	  var $tlpTitle = null;
	  
	  function ThreadPermalink($pageTitle) {
	       global $wgSkin;
	       if ( $pageTitle ) {
		    $this->pageTitle = $pageTitle;
		    $this->tlpTitle = Title::makeTitle( 100, $pageTitle->getDBkey() ); // FIMXE post namespace.
		    $this->tlpPost = new Post($this->tlpTitle);
		    $this->thread = $this->tlpPost->thread();
	       }
	       SpecialPage::SpecialPage( 'Thread', 'thread' );
	  }

	  function execute() {
	       global $wgUser, $wgRequest, $wgOut, $wgArticle;

	       // SpecialPage::setHeaders
	       $this->setHeaders();

	       // Execute move operations:
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
		    $wgOut->redirect( $this->pageTitle->getFullURL($query) );
		    return;
	       }

	       // Note if we're not viewing from the root of the thread:
	       if( $this->tlpPost->getID() != $this->thread->firstPost()->getID() ) {
		    $wgOut->addHTML( '<span class="lqt_not_entire_thread">This is not the entire thread.</span><br />' );
	       }

	       // 'Show in Context' link:
	       $month = substr( $this->thread->touched(), 0, 6 );
	       $t = $this->tlpTitle->getPartialURL();
	       $channel_title = Title::makeTitle( LQT_NS_CHANNEL, $this->thread->article()->getTitle()->getDBkey() );
	       $context_href = $channel_title->getLocalURL( 'lqt_show_month='.$month.'&lqt_highlight='.$t.'#lqt_post_'.$t );
	       $wgOut->addHTML( wfElementClean('a', array('href'=>$context_href),'Show in Context') );

	       $moving = $wgRequest->getInt('lqt_moving_id');
	       $history_id = $wgRequest->getInt("lqt_show_history_id", null);
	       $editing_id = $wgRequest->getInt("lqt_editing", null);
	       $replying_to_id = $wgRequest->getInt("lqt_replying_to_id", null);
	       $highlighting_title = $wgRequest->getVal("lqt_highlight", null);

	       if ($this->tlpPost) {
	       $view = new ThreadView( $this->pageTitle, $this->thread, $this->tlpPost, $editing_id, $replying_to_id, $history_id, $highlighting_title, $moving );
	       $view->render();
	       } else {
		    $wgOut->addWikiText("No such thread exists.");
	       }

	       $wgOut->setPageTitle($this->pageTitle->getPrefixedText());
	  }

     }

}


?>