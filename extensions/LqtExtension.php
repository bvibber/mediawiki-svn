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
	
	function __construct(&$output, &$article, &$title, &$user, &$request) {
		$this->article = $article;
		$this->output = $output;
		$this->user = $user;
		$this->title = $title;
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
		global $wgRequest;
		return $wgRequest->getInt($command) == $post->getID();
	}
	
	/*************************
	* Simple HTML methods    *
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
	* Output methods with logic    *
	*******************************/

	function showNewThreadForm() {
		global $wgOut;
		$wgOut->addHTML('<p>new thread form</p>');
	}

	function showPostEditingForm( $post ) {
		global $wgRequest;
		$pp = new PostProxy( $post, $wgRequest );
		$this->showEditingFormInGeneral( $pp, 'editExisting', $post->getID() );
	}

	function showReplyForm( $post ) {
		global $wgRequest;
		$pp = new PostProxy( null, $wgRequest );
		$this->showEditingFormInGeneral( $pp, 'reply', $post->getID() );
	}

	function showEditingFormInGeneral( $post_proxy, $edit_type, $edit_applies_to ) {
		global $wgOut, $wgRequest;

		$pp = $post_proxy;
		
		if ( $pp->submittedPreview() ) {
			$wgOut->addHTML("THIS IS ONLY A PREVIEW, FOO");
			$wgOut->addWikiText( $pp->content() );
		}
		
		$fields = array( array( 'type' => 'textarea',
		                        'name' => 'content',
		                        'value'=>$pp->content() ),
		 		         array( 'type'=>'input',
		                        'name'=>'summary',
		                        'value'=>$pp->summary() ),
		                 array( 'type'=>'hidden',
		                        'name'=>'editType',
								'value'=>$edit_type),
						 array( 'type'=>'hidden',
			                    'name'=>'editAppliesTo',
								'value'=>$edit_applies_to),
 						 array( 'type'=>'submit',
						        'name'=>'save',
								'label'=>'Save'),
		                 array( 'type'=>'submit',
						        'name'=>'preview',
								'label'=>'Preview')
						);
		$f = new Form( $fields, 'POST', $wgRequest->getFullRequestURL() );
		$wgOut->addHTML( $f->html() );
		
		$wgOut->addHTML( wfElement( 'a', array( 'href'=>$this->selflink()), 'Cancel' ) );
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
			$rev = Revision::newFromTitle( $post->getTitle() );
			$wgOut->addWikiText( $rev->getText() );
		}
	}

	function showPostCommands( $post ) {
		global $wgOut;
		$commands = array( 'Edit' => $this->selflink( array( LQT_COMMAND_EDIT_POST => $post->getID() ) ),
						   'Reply' => $this->selflink( array( LQT_COMMAND_REPLY_TO_POST => $post->getID() ) ));
						
		$wgOut->addHTML(wfOpenElement('ul', array('class'=>'lqt_footer')));
		
		foreach( $commands as $label => $href ) {
			$wgOut->addHTML( wfOpenElement( 'li' ) );
			$wgOut->addHTML( wfElement('a', array('href'=>$href), $label) );
			$wgOut->addHTML( wfCloseElement( 'li' ) );
		}
		
		$wgOut->addHTML(wfCloseELement('ul'));
	}

	function showPost( $post ) {
		global $wgOut;
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
		global $wgOut;
		if ( $thread->hasSubject() )
		$wgOut->addHTML( wfElement( "h{$this->headerLevel}", null, $thread->subject() ) );
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
}

class TalkpageView extends LqtView {
	
	function show() {
		$threads = Thread::allThreadsOfArticle($this->article);
		foreach($threads as $t) {
			Thread::walk( $t, array($this,'showThread'),
			                  array($this,'indent'),
			                  array($this,'unindent'));
		}
	}
	/*
	function showThread($t) {
		$this->output->addHTML($t->subject());
		$this->showPost($t->rootPost());
	}
	function indent() {}
	function unindent() {}*/
}


}

