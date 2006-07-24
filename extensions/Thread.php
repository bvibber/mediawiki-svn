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
     $wgExtensionFunctions[] = 'efThread';

     function threadSpecialCaseHook( &$title, &$output, $request ) {
	  if( $title->getNamespace() === LQT_NS_THREAD ) {
	       $t = new Thread($title);
	       $t->execute();
	       return false;
	  }
	  else {
	       return true;
	  }
     }
     
     function efThread() {
	  global $wgMessageCache, $wgHooks;
	  $wgHooks['SpecialCase'][] = 'threadSpecialCaseHook';
	  $wgMessageCache->addMessage( 'lq', 'LiquidThreads' );
	  SpecialPage::addPage( new Thread(null) );
     }
     
     class Thread extends SpecialPage {

	  /* TODO: these appearently are not used; they are from pre-Titles. */
	  static $article;
	  static $titleString;
	  static $moving;
	  
	  /** top-level post title. */
	  var $tlpTitle = null;
	  
	  function Thread($pageTitle) {
	       global $wgSkin;
	       if ( $pageTitle ) {
		    $this->pageTitle = $pageTitle;
		    $this->tlpTitle = Title::makeTitle( 100, $pageTitle->getDBkey() ); #FIMXE post namespace.
	       }
	       SpecialPage::SpecialPage( 'Thread', 'thread' );
	  }

	  function execute() {
	       global $wgUser, $wgRequest, $wgOut, $wgArticle;

	       // SpecialPage::setHeaders
	       $this->setHeaders();

               $first_post = new Post($this->tlpTitle);

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

	       // 'Show in Context' link:
	       $t = $this->tlpTitle->getPartialURL();
	       $channel_title = Title::makeTitle( LQT_NS_CHANNEL, $first_post->talkPage()->getTitle()->getDBkey() );
	       $context_href = $channel_title->getLocalURL( 'lqt_highlight='.$t.'#lqt_post_'.$t );
	       $wgOut->addHTML( wfElementClean('a', array('href'=>$context_href),'Show in Context') );

	       $moving = $wgRequest->getInt('lqt_moving_id');
	       $history_id = $wgRequest->getInt("lqt_show_history_id", null);
	       $editing_id = $wgRequest->getInt("lqt_editing", null);
	       $replying_to_id = $wgRequest->getInt("lqt_replying_to_id", null);
	       $highlighting_title = $wgRequest->getVal("lqt_highlight", null);

	       $view = new ThreadView($this->pageTitle, null, $editing_id, $replying_to_id, $history_id, $highlighting_title, $moving);

/* this is broken but will be un-broken probably when we do archiving
	       if ($first_post && $first_post->previousPost())
		    $is_top_level = !( $first_post->previousPost()->isPost() );
	       else
		    $is_top_level = false;
*/
	       if ($first_post) {
		    $view->renderThreadStartingFrom( $first_post, false, false );
	       } else {
		    $wgOut->addWikiText("No such thread exists.");
	       }

	       $wgOut->setPageTitle($this->pageTitle->getPrefixedText());
	  }

     }

}


?>
