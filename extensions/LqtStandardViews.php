<?php

require_once( 'LqtModel.php' );

class LqtView {
     /** h1, h2, h3, etc. */
     var $headerLevel = 1;

     function __construct() {
	  global $wgOut;
	  $wgOut->setArticleRelated( false );
	  $wgOut->setRobotPolicy( "noindex,nofollow" );
	  $wgOut->setPageTitle( "LQT Thing" );
     }

     /*************************
      * Simple HTML Functions *
      *************************/

     function openDiv( $class = null ) {
	  global $wgOut;
	  if ( $class )
	       $wgOut->addHTML( wfOpenElement( 'div', array('class'=>$class) ) );
	  else
	       $wgOut->addHTML( wfOpenElement( 'div') );
     }

     function closeDiv() {
	  global $wgOut;
	  $wgOut->addHTML( wfCloseElement( 'div' ) );
     }

     /*******************************
      * Output functions with logic *
      *******************************/

     function showNewThreadForm() {
	  global $wgOut;
	  $wgOut->addHTML('<p>new thread form</p>');
     }

     function showPostBody( $post ) {
	  global $wgOut, $wgUser, $wgEnableParserCache;

          // Should the parser cache be used?
	  $pcache = $wgEnableParserCache &&
	       intval( $wgUser->getOption( 'stubthreshold' ) ) == 0 &&
	       $post->exists() &&
	       empty( $oldid ); // FIXME oldid
	  wfDebug( 'LqtView::showPostBody using parser cache: ' . ($pcache ? 'yes' : 'no' ) . "\n" );
	  if ( $wgUser->getOption( 'stubthreshold' ) ) {
	       wfIncrStats( 'pcache_miss_stub' );
	  }

	  $outputDone = false;
	  if ( $pcache ) {
	       $outputDone = $wgOut->tryParserCache( $post, $wgUser );
	  }

	  if (!$outputDone) {
	       // Cache miss; parse and output it.
	       $wgOut->addWikiText($post->mContent);
	  }
     }

     function showThreadHeading( $thread ) {
	  global $wgOut;
	  if ( $thread->hasSubject() )
	       $wgOut->addHTML( wfElement( "h{$this->headerLevel}", null, $thread->subject() ) );
     }

     function showPost( $post ) {
	  global $wgOut;
	  $this->openDiv( 'lqt_post' );
	  $this->showPostBody( $post );
	  $this->closeDiv();
     }
     function showThread( $thread ) {
	  global $wgOut;
	  
	  $this->showThreadHeading( $thread );
	  $this->showPost( $thread->rootPost() );
	  $this->indent();
	  foreach( $thread->subthreads() as $st ) {
	       $this->showThread($st);
	  }
	  $this->unindent();
     }
     function indent() {
	  global $wgOut;
	  $wgOut->addHTML( wfOpenElement( 'dl', array('class'=>'lqt_replies') ) );
	  $wgOut->addHTML( wfOpenElement( 'dd') );
	  $this->headerLevel += 1;
     }
     function unindent() {
	  global $wgOut;
	  $wgOut->addHTML( wfCloseElement( 'dd') );
	  $wgOut->addHTML( wfCloseElement( 'dl') );
	  $this->headerLevel -= 1;
     }

     /*******************
      * Archive browser *
      *******************/

     function _showPartialArchiveBrowser( $thread ) {
          global $wgOut;
	  if ( $thread->hasSubject() ) {
	       $wgOut->addHTML('<li>');
	       $wgOut->addHTML( $thread->subject() );
	       $wgOut->addHTML('</li>');
	  }

	  if ( $thread->hasSubthreads() ) {
	       $wgOut->addHTML('<ul>');
	       foreach ( $thread->subthreads() as $st ) {
		    $this->_showPartialArchiveBrowser( $st );
	       }
	       $wgOut->addHTML('</ul>');
	  }
     }
     function showArchiveBrowser() {
	  global $wgOut;
	  $threads = Thread::latestNThreadsOfArticle( $this->article, 30 );
	  foreach( $threads as $t ) {
	       $this->_showPartialArchiveBrowser( $t );
	  }
     }
  }

class ChannelView extends LqtView {
     function show() {
	  $this->showNewThreadForm();
//	  $this->showPost( $this->article->headerPost() );
	  foreach( $this->threads as $t ) {
	       $this->showThread($t);
	  }
	  $this->showArchiveBrowser();
     }

     function __construct($channelTitle) {
	  parent::__construct();
	  $this->channelTitle = $channelTitle;
	  $this->articleTitle = Title::makeTitle( NS_MAIN, $channelTitle->getDBkey() );
	  $this->article = new Article($this->articleTitle);
	  $this->threads = Thread::latestNThreadsOfArticle( $this->article, 10 );
     }
}

?>
