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



function efThread() {
        global $wgMessageCache;
        $wgMessageCache->addMessage( 'lq', 'LiquidThreads' );
        SpecialPage::addPage( new Thread() );
}

class Thread extends SpecialPage {

        static $article;
        static $titleString;
        static $moving;
        
        function Thread() {
                SpecialPage::SpecialPage( 'Thread', 'thread' );
        }


	// FIXME need to find real way to do this.
	function baseURL() {
	  return "/wiki/index.php/Special:Thread/";
	}

        function execute() {
            global $wgUser, $wgRequest, $wgOut, $wgArticle;
                                         
            $this->setHeaders(); # not sure what this does.
            
            # Extract the 'title' part of the path (between slash and query string)
            $tmp1 = split( "Thread/", $wgRequest->getRequestURL() );
            $tmp2 = split('\?', $tmp1[1]);
            $pageTitle = $tmp2[0];
            $this->title = $title = Title::newFromText($pageTitle, 100);  #FIXME don't hardcore namespace.

            $first_post = new Post($title);

            if ($pageTitle == '') {
                    $wgOut->addWikiText("Try giving me the title of an article.");
                    return;
            }

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
                    $query = "?lqt_highlight={$posttitle->getPartialURL()}#lqt_post_{$posttitle->getPartialURL()}";
                    $wgOut->redirect( Thread::baseURL() . $pageTitle . $query );
                    return;
            }

	# 'Show in Context' link:
	     $t = $first_post->getTitle()->getPartialURL();
	     $context_href = LQ::baseURL() . $first_post->talkPage()->getTitle()->getPartialURL() .
	     '?lqt_highlight='.$t.'#lqt_post_'.$t;
	     $wgOut->addHTML(
		  wfElementClean('a', array('href'=>$context_href),'Show in Context')
		  );
	

            $moving = $wgRequest->getInt('lqt_moving_id');
	    $editing_id = $wgRequest->getInt("lqt_editing", null);
	    $replying_to_id = $wgRequest->getInt("lqt_replying_to_id", null);
	    $highlighting_title = $wgRequest->getVal("lqt_highlight", null);
	      
	    $view = new ThreadView(Thread::baseURL(), $pageTitle, $editing_id, $replying_to_id, $highlighting_title,
				   $moving);

	    if ($first_post) {
	      $view->renderThreadStartingFrom( $first_post, false );
	    } else {
		 $wgOut->addWikiText("This talk page is empty.");
	    }

	    $wgOut->setPageTitle('Thread:'.$first_post->getTitle()->getPartialURL());
	}

}

}


?>
