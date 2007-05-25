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

/** database ID of post to edit. */
define('LQT_COMMAND_EDIT_POST', 'lqt_edit_post');
/** database ID of post to reply to */
define('LQT_COMMAND_REPLY_TO_POST', 'lqt_reply_to');

require_once('LqtModel.php');

class LqtDispatch {
	/** Invoked from performAction() in Wiki.php if this is a discussion namespace. */
	static function talkpageMain(&$output, &$talk_article, &$title, &$user, &$request) {
		// We are given a talkpage article and title. Find the associated
		// non-talk article and pass that to the view.
		$article_title = Title::makeTitle($title->getNamespace() - 1,
		                                  $title->getDBkey());
		$article = new Article($article_title);

		// Here we would check for POST data of importance.
		
		$view = new TalkpageView( $output, $article, $title, $user, $request );
		$view->show();
	}
}

class LqtView {
	protected $article;
	protected $output;
	protected $user;
	protected $title;
	protected $request;
	
	function __construct(&$output, &$article, &$title, &$user, &$request) {
		$this->article = $article;
		$this->output = $output;
		$this->user = $user;
		$this->title = $title;
		$this->request = $request;
	}
	

	/** h1, h2, h3, etc. */
	var $headerLevel = 1;
	
	/*************************
	* Utlity methods         *
	*************************/
	
	function queryStringFromArray( $vars ) {
		$q = '';
		if ( $vars && count( $vars ) != 0 ) {
			foreach( $vars as $name => $value )
				$q .= "$name=$value&";
		}
		return $q;
	}
	
	/**
		@return href for a link to the same page as is being currently viewed, 
		        but with additional query variables.
		@param $vars array( 'query_variable_name' => 'value', ... ).
	*/
	function selflink( $vars = null ) {
		return $this->title->getFullURL( $this->queryStringFromArray($vars) );
	}
	
	/**
		@return true if the value of the give query variable name is equal to the given post's ID.
	*/
	function commandApplies( $command, $post ) {
		
		return $this->request->getInt($command) == $post->getID();
	}
	
	/*************************
	* Simple HTML methods    *
	*************************/

	function openDiv( $class = null ) {
		
		if ( $class )
			$this->output->addHTML( wfOpenElement( 'div', array('class'=>$class) ) );
		else
			$this->output->addHTML( wfOpenElement( 'div') );
	}

	function closeDiv() {
		
		$this->output->addHTML( wfCloseElement( 'div' ) );
	}

	/*******************************
	* Output methods with logic    *
	*******************************/

	function showNewThreadForm() {
		$this->output->addHTML('<p>new thread form</p>');
	}

	function showPostEditingForm( $post ) {
		$pp = new PostProxy( $post, $this->request );
		$this->showEditingFormInGeneral( $pp, 'editExisting', $post->getID() );
	}

	function showReplyForm( $post ) {
		$pp = new PostProxy( null, $this->request );
		$this->showEditingFormInGeneral( $pp, 'reply', $post->getID() );
	}

	function showEditingFormInGeneral( $post_proxy, $edit_type, $edit_applies_to ) {

		$pp = $post_proxy;
		
		$this->output->addHTML("<p>Doing an $edit_type to $edit_applies_to.");
		
		// this only works for editing because we refer to the article directly.
		
		$e = new EditPage($pp->article);
		$e->setAction( $this->title->getFullURL( "lqt_edit_post={$pp->article->getID()}" ) );

/*		if ( $p->thread()->firstPost()->getID() == $p->getID() ) {
			// This is the thread's root post; display topic field.
			ThreadView::$callbackpost = $p;
			ThreadView::$callbackeditpage = $e;
			$e->formCallback = array('ThreadView', 'topicCallback');
		}*/

		$e->edit();

		// Override what happens in EditPage::showEditForm, called from $e->edit():
//		$wgOut->setArticleRelated( false ); 
		$this->output->setArticleFlag( false );

		// Override editpage's redirect.
//		if ($e->didRedirect) {
		// I have lost track of where the redirect happens, so I can't set a flag there until I find it.
		// In the meantime, just check if somewhere somebody redirected. I'm afraid this might have
		// unwanted side-effects.
		if ( $this->output->getRedirect() != '' ) {
			$t = $pp->article->getTitle()->getPartialURL();
			$this->output->redirect( $this->title->getFullURL() );
		}

/*		// Save new topic line if there is one:
		if ( $e->mDidSave && $wgRequest->getVal('lqt_topic') ) {
			$v = Sanitizer::stripAllTags($wgRequest->getVal('lqt_topic'));
			$p->setSubject($v);
		}*/
	}


	function showPostBody( $post ) {
		global $wgEnableParserCache;

		// Should the parser cache be used?
		$pcache = $wgEnableParserCache &&
		          intval( $this->user->getOption( 'stubthreshold' ) ) == 0 &&
		          $post->exists() &&
		          empty( $oldid ); // FIXME oldid
		wfDebug( 'LqtView::showPostBody using parser cache: ' . ($pcache ? 'yes' : 'no' ) . "\n" );
		if ( $this->user->getOption( 'stubthreshold' ) ) {
			wfIncrStats( 'pcache_miss_stub' );
		}

		$outputDone = false;
		if ( $pcache ) {
			$outputDone = $this->output->tryParserCache( $post, $this->user );
		}

		if (!$outputDone) {
			// Cache miss; parse and output it.
			$rev = Revision::newFromTitle( $post->getTitle() );
			$this->output->addWikiText( $rev->getText() );
		}
	}

	function showPostCommands( $post ) {
		
		$commands = array( 'Edit' => $this->selflink( array( LQT_COMMAND_EDIT_POST => $post->getID() ) ),
						   'Reply' => $this->selflink( array( LQT_COMMAND_REPLY_TO_POST => $post->getID() ) ));
						
		$this->output->addHTML(wfOpenElement('ul', array('class'=>'lqt_footer')));
		
		foreach( $commands as $label => $href ) {
			$this->output->addHTML( wfOpenElement( 'li' ) );
			$this->output->addHTML( wfElement('a', array('href'=>$href), $label) );
			$this->output->addHTML( wfCloseElement( 'li' ) );
		}
		
		$this->output->addHTML(wfCloseELement('ul'));
	}

	function showPost( $post ) {
		
		$this->openDiv( 'lqt_post' );
		
		if( $this->commandApplies( LQT_COMMAND_EDIT_POST, $post ) ) {
			$this->showPostEditingForm( $post );
		} else{
			$this->showPostBody( $post );
			$this->showPostCommands( $post );
		}
		
		$this->closeDiv();
		
		if( $this->commandApplies( LQT_COMMAND_REPLY_TO_POST, $post ) ) {
			$this->indent();
			$this->showReplyForm( $post );
			$this->unindent();
		}
	}

	function showThreadHeading( $thread ) {
		
		if ( $thread->hasSubject() )
			$this->output->addHTML( wfElement( "h{$this->headerLevel}", null, $thread->subject() ) );
	}

	function showThread( $thread ) {
		

		$this->showThreadHeading( $thread );
		$this->showPost( $thread->rootPost() );
		$this->indent();
		foreach( $thread->subthreads() as $st ) {
			$this->showThread($st);
		}
		$this->unindent();
	}

	function indent() {
		
		$this->output->addHTML( wfOpenElement( 'dl', array('class'=>'lqt_replies') ) );
		$this->output->addHTML( wfOpenElement( 'dd') );
		$this->headerLevel += 1;
	}
	function unindent() {
		
		$this->output->addHTML( wfCloseElement( 'dd') );
		$this->output->addHTML( wfCloseElement( 'dl') );
		$this->headerLevel -= 1;
	}
}

class TalkpageView extends LqtView {
	function show() {
		$this->output->setPageTitle( "Talk:" . $this->title->getText() );
		
		$threads = Thread::allThreadsOfArticle($this->article);
		foreach($threads as $t) {
			$this->showThread($t);
/*			Thread::walk( $t, array($this,'showThread'),
			                  array($this,'indent'),
			                  array($this,'unindent'));*/
		}
	}
}


}

