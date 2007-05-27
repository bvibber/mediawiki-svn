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
	/* Invoked from performAction() in Wiki.php if this is a discussion namespace. */
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
	 *	@return href for a link to the same page as is being currently viewed, 
	 *	        but with additional query variables.
	 *	@param $vars array( 'query_variable_name' => 'value', ... ).
	*/
	function selflink( $vars = null ) {
		return $this->title->getFullURL( $this->queryStringFromArray($vars) );
	}
	
	/**
	 *	@return true if the value of the give query variable name is equal to the given post's ID.
	*/
	function commandApplies( $command, $post ) {
		return $this->request->getVal($command) == $post->getTitle()->getPrefixedURL();
	}
	function commandAppliesToThread( $command, $thread ) {
		return $this->request->getVal($command) == $thread->id();
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
		$this->showEditingFormInGeneral( null, 'new', null );
	}

	function showPostEditingForm( $thread ) {
		$this->showEditingFormInGeneral( $thread, 'editExisting', null );
	}

	function showReplyForm( $thread ) {
		$this->showEditingFormInGeneral( null, 'reply', $thread );
	}

	function showEditingFormInGeneral( $thread, $edit_type, $edit_applies_to ) {
		
		// If there is no article (reply or new), we need a randomly-generated title.
		// On the first pass, we generate one. After that, we find it in the request.
		if ( $thread == null ) {
			$rt = Title::newFromURL( $this->request->getVal('lqt_edit_post') );
			$t = $rt ? $rt : $this->scratchTitle();
			$article = new Article( $t );
		} else {
			$article = $thread->rootPost();
		}
		
		$e = new EditPage($article);
		$e->suppressIntro = true;
		
		$e->editFormTextBottom .= "<input type=\"hidden\" name=\"lqt_edit_post\" value=\"{$article->getTitle()->getPrefixedURL()}\">";
		
		if ( $edit_type == 'reply' ) {
			$e->editFormTextBottom .= "<input type=\"hidden\" name=\"lqt_reply_to\" value=\"{$edit_applies_to->id()}\">";
		}
		
		if ( $edit_type == 'new' ) {
			$e->editFormTextBottom .= "<input type=\"hidden\" name=\"lqt_new_thread_form\" value=\"1\">";
		}
		
		if ( $thread == null || $thread->superthread() == null ) {
			// This is a top-level post; show the subject line.
			$subject = $this->request->getVal('lqt_subject_field', $thread ? $thread->subject() : '');
			$e->editFormTextBeforeContent .= <<<HTML
			<label for="lqt_subject_field">Subject: </label>
			<input type="text" size="50" name="lqt_subject_field" id="lqt_subject_field" value="$subject"><br>
HTML;
		}

		$e->edit();

		// Override what happens in EditPage::showEditForm, called from $e->edit():
//		$wgOut->setArticleRelated( false ); 
		$this->output->setArticleFlag( false );

		// I have lost track of where the redirect happens, so I can't set a flag there until I find it.
		// In the meantime, just check if somewhere somebody redirected. I'm afraid this might have
		// unwanted side-effects.
		if ( $this->output->getRedirect() != '' ) {
			$this->output->redirect( $this->title->getFullURL() );
		}
		
		// For replies and new posts, insert the associated thread object into the DB.
		if ($edit_type != 'editExisting' && $e->didSave) {
			$thread = Thread::newThread( $article, $this->article );
			if ( $edit_type == 'reply' ) {
				$thread->setSuperthread( $edit_applies_to );
			}
		}

		$subject = $this->request->getVal('lqt_subject_field', '');
		if ( $e->didSave && $subject != '' ) {
			$thread->setSubject( Sanitizer::stripAllTags($subject) );
		}
	}
	
	function scratchTitle() {
		$token = md5(uniqid(rand(), true));
		return Title::newFromText( "Post:$token" );
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

	function showThreadFooter( $thread ) {

		$this->output->addHTML(wfOpenElement('ul', array('class'=>'lqt_footer')));

		$this->output->addHTML( wfOpenElement( 'li' ) );
		$p = new Parser(); $sig = $p->getUserSig( $thread->rootPost()->originalAuthor() );
		$this->output->addWikitext( $sig, false );
		$this->output->addHTML( wfCloseElement( 'li' ) );
			
		$commands = array( 'Edit' => $this->selflink( array( LQT_COMMAND_EDIT_POST => $thread->rootPost()->getTitle()->getPrefixedURL() ) ),
						   'Reply' => $this->selflink( array( LQT_COMMAND_REPLY_TO_POST => $thread->id() ) ));
						
		foreach( $commands as $label => $href ) {
			$this->output->addHTML( wfOpenElement( 'li' ) );
			$this->output->addHTML( wfElement('a', array('href'=>$href), $label) );
			$this->output->addHTML( wfCloseElement( 'li' ) );
		}
		
		$this->output->addHTML(wfCloseELement('ul'));
	}

	function showRootPost( $thread ) {
		$post = $thread->rootPost();
		
		$this->openDiv( 'lqt_post' );
		
		if( $this->commandApplies( LQT_COMMAND_EDIT_POST, $post ) ) {
			$this->showPostEditingForm( $thread );
		} else{
			$this->showPostBody( $post );
			$this->showThreadFooter( $thread );
		}
		
		$this->closeDiv();
		
		if( $this->commandAppliesToThread( LQT_COMMAND_REPLY_TO_POST, $thread ) ) {
			$this->indent();
			$this->showReplyForm( $thread );
			$this->unindent();
		}
	}

	function showThreadHeading( $thread ) {
		if ( $thread->hasSubject() )
			$this->output->addHTML( wfElement( "h{$this->headerLevel}", array('class'=>'lqt_header'), $thread->subject() ) );
	}

	function showThread( $thread ) {
		$this->showThreadHeading( $thread );
		$this->showRootPost( $thread );
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
		
		if( $this->request->getBool('lqt_new_thread_form') ) {
			$this->showNewThreadForm();
		} else {
			$this->output->addHTML("<strong><a href=\"{$this->title->getFullURL('lqt_new_thread_form=1')}\">Start a Discussion</a></strong>");
		}
		$threads = Thread::allThreadsOfArticle($this->article);
		foreach($threads as $t) {
			$this->showThread($t);
		}
	}
}


}

