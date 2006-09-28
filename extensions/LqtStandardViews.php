<?php

require_once( 'LqtModel.php' );
require_once( 'LqtConstants.php' );

class LqtView {
	/** h1, h2, h3, etc. */
	var $headerLevel = 1;
	
	/** Title object representing the URL used to get to this view itself -- as opposed to
	 	the article that this is the discussion page of, if any, etc. */
	var $viewTitle = null;

	function __construct( $viewTitle ) {
		global $wgOut;
		
		$this->viewTitle = $viewTitle;
		
		$wgOut->setArticleRelated( false );
		$wgOut->setRobotPolicy( "noindex,nofollow" );
		$wgOut->setPageTitle( "LQT Thing" );
	}

	/*************************
	* Utlity functions       *
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
	function selflink( $vars ) {
		return $this->viewTitle->getFullURL( $this->queryStringFromArray($vars) );
	}
	
	/**
		@return true if the value of the give query variable name is equal to the given post's ID.
	*/
	function commandApplies( $command, $post ) {
		global $wgRequest;
		return $wgRequest->getInt($command) == $post->getID();
	}
	
	/*************************
	* Simple HTML Functions  *
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

	function showPostEditingForm() {
		global $wgOut;	
		$wgOut->addHTML("edit");
	}
	
	function showReplyForm( $post ) {
		global $wgOut;	
		$wgOut->addHTML("reply");
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
			// TODO: this is probably not the correct interface, but who the hell
			// knows what the correct one is?
			$post->loadContent();
			$wgOut->addWikiText($post->mContent);
		}
	}

	function showPostCommands( $post ) {
		global $wgOut;
		$commands = array( 'Edit' => $this->selflink( array( LQT_COMMAND_EDIT_POST => $post->getID() ) ),
						   'Reply' => $this->selflink( array( LQT_COMMAND_REPLY_TO_POST => $post->getID() ) ));
						
		$wgOut->addHTML(wfOpenElement('ul', array('class'=>'lqt_commands')));
		
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
			$this->showReplyForm( $post );
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

class ArchiveBrowser extends LqtView {
	protected function _showThreadTOC( $thread ) {
		global $wgOut;
		if ( $thread->hasSubject() ) {
			$wgOut->addHTML('<li>');
			$wgOut->addHTML( $thread->subject() );
			$wgOut->addHTML('</li>');
		}

		if ( $thread->hasSubthreads() ) {
			$wgOut->addHTML('<ul>');
			foreach ( $thread->subthreads() as $st ) {
				$this->_showThreadTOC( $st );
			}
			$wgOut->addHTML('</ul>');
		}
	}
	function showPartialBrowser() {
		$threads = Thread::latestNThreadsOfArticle( $this->article, 30 );
		foreach( $threads as $t ) {
			$this->_showThreadTOC( $t );
		}
	}
	function show() {
		$threads = Thread::allThreadsOfArticle( $this->article );
		foreach( $threads as $t ) {
			$this->_showThreadTOC( $t );
		}
	}
	function __construct($viewTitle) {
		parent::__construct($viewTitle); // TODO this is redundant with ChannelView::__construct.
		$this->articleTitle = Title::makeTitle( NS_MAIN, $viewTitle->getDBkey() );
		$this->article = new Article($this->articleTitle);
	}
}

class ChannelView extends LqtView {
	function show() {
		$this->showNewThreadForm();
		//	  $this->showPost( $this->article->headerPost() );
		foreach( $this->threads as $t ) {
			$this->showThread($t);
		}
		$b = new ArchiveBrowser( $this->article->getTitle() );
		$b->showPartialBrowser();
	}

	function __construct($viewTitle) {
		parent::__construct( $viewTitle );
		$this->articleTitle = Title::makeTitle( NS_MAIN, $viewTitle->getDBkey() );
		$this->article = new Article($this->articleTitle);
		$this->threads = Thread::latestNThreadsOfArticle( $this->article, 10 );
	}
}

class ArchiveView extends LqtView {
	function show() {
		$tihs->showLinkToLatest();
	}
}

?>
