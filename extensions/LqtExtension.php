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
	
	protected $user_colors;
	protected $user_color_index;
	const number_of_user_colors = 6;
	
	function __construct(&$output, &$article, &$title, &$user, &$request) {
		$this->article = $article;
		$this->output = $output;
		$this->user = $user;
		$this->title = $title;
		$this->request = $request;
		$this->user_colors = array();
		$this->user_color_index = 1;
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

	/**
	 * Return an HTML form element whose value is gotten from the request.
	 * TODO: figure out a clean way to expand this to other forms.
	 */
	function perpetuate( $name, $as ) {
		$value = $this->request->getVal($name, '');
		if ( $as == 'hidden' ) {
			return <<<HTML
			<input type="hidden" name="$name" id="$name" value="$value">
HTML;
		}
	}

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
		
		$e->editFormTextBeforeContent .=
			$this->perpetuate('lqt_edit_post', 'hidden') .
			$this->perpetuate('lqt_reply_to', 'hidden') .
			$this->perpetuate('lqt_new_thread_form', 'hidden');
		
		if ( /*$thread == null*/ $edit_type=='new' || ($thread && $thread->superthread() == null) ) {
			// This is a top-level post; show the subject line.
			$subject = $this->request->getVal('lqt_subject_field', $thread ? $thread->subject() : '');
			$e->editFormTextBeforeContent .= <<<HTML
			<label for="lqt_subject_field">Subject: </label>
			<input type="text" size="60" name="lqt_subject_field" id="lqt_subject_field" value="$subject"><br>
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
		
		if ($e->didSave) $thread->touch(); // TODO reduntent if above $thread->setX called.
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

	function lqtTalkpageUrl( $title, $operator = null, $operand = null ) {
		if ( $operator == 'lqt_reply_to' ) {
			$query = array( 'lqt_reply_to' => $operand ? $operand->id() : null );
		} else if ($operator == 'lqt_edit_post') {
			$query = array( 'lqt_edit_post' => $operand ? $operand->rootPost()->getTitle()->getPrefixedURL() : null );
		} else if ($operator == 'lqt_new_thread_form' ) {
			$query = array( 'lqt_new_thread_form' => '1' );
		} else {
			$query = array();
		}
		return $title->getFullURL( $this->queryStringFromArray($query) );
	}

	function permalinkUrl( $thread ) {
		return SpecialPage::getTitleFor('Thread', $thread->id())->getFullURL();
	}
	
	function showThreadFooter( $thread ) {

		$color_number = $this->selectNewUserColor( $thread->rootPost()->originalAuthor() );
		$this->output->addHTML(wfOpenElement('ul', array('class'=>"lqt_footer lqt_post_color_$color_number" )));

		$this->output->addHTML( wfOpenElement( 'li' ) );
		$p = new Parser(); $sig = $p->getUserSig( $thread->rootPost()->originalAuthor() );
		$this->output->addWikitext( $sig, false );
		$this->output->addHTML( wfCloseElement( 'li' ) );

		global $wgContLang;
		$this->output->addHTML(
			wfOpenElement( 'li' ) .
			$wgContLang->timeanddate( $thread->touched() ) .
			wfCloseElement( 'li' )
		);
		
		$commands = array( 'Edit' => $this->lqtTalkpageUrl( $this->title, 'lqt_edit_post', $thread ),
		 					'Reply' => $this->lqtTalkpageUrl( $this->title, 'lqt_reply_to', $thread ),
		 					'Permalink' => $this->permalinkUrl( $thread ) );

		foreach( $commands as $label => $href ) {
			$this->output->addHTML( wfOpenElement( 'li' ) );
			$this->output->addHTML( wfElement('a', array('href'=>$href), $label) );
			$this->output->addHTML( wfCloseElement( 'li' ) );
		}

		$this->output->addHTML(wfCloseELement('ul'));
	}

	function selectNewUserColor( $user ) {
		$userkey = $user->isAnon() ? "anon:" . $user->getName() : "user:" . $user->getId();
		
		if( !array_key_exists( $userkey, $this->user_colors ) ) {
			$this->user_colors[$userkey] = $this->user_color_index;
			$this->user_color_index += 1;
			if ( $this->user_color_index > self::number_of_user_colors ) {
				$this->user_color_index = 1;
			}
		}
		return $this->user_colors[$userkey];
	}

	function showRootPost( $thread ) {
		$post = $thread->rootPost();
		
/*		$color_number = $this->selectNewUserColor( $thread->rootPost()->originalAuthor() );
		$this->openDiv( "lqt_post lqt_post_color_$color_number" );*/
		$this->openDiv( 'lqt_post' );
		
		if( $this->commandApplies( 'lqt_edit_post', $post ) ) {
			$this->showPostEditingForm( $thread );
		} else{
			$this->showPostBody( $post );
			$this->showThreadFooter( $thread );
		}
		
		$this->closeDiv();
		
		if( $this->commandAppliesToThread( 'lqt_reply_to', $thread ) ) {
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
	/* Added to SkinTemplateTabs hook in TalkpageView::show(). */
	function customizeTabs( $skintemplate, $content_actions ) {
		// The arguments are passed in by reference.
		unset($content_actions['edit']);
		unset($content_actions['addsection']);
		unset($content_actions['history']);
		unset($content_actions['watch']);
		
		/*
		TODO: 
		We could make these tabs actually follow the tab metaphor if we repointed
		the 'history' and 'edit' tabs to the original subject page. That way 'discussion'
		would just be one of four ways to view the article. But then those other tabs, for
		logged-in users, don't really fit the metaphor. What to do, what to do?
		*/
	}

	function showArchive($month) {
		$threads = Thread::threadsOfArticleInMonth( $this->article, $month );
		foreach($threads as $t) {
			$this->showThread($t);
		}
	}

	function showLatest() {
		if( $this->request->getBool('lqt_new_thread_form') ) {
			$this->showNewThreadForm();
		} else {
			$url = $this->lqtTalkpageUrl( $this->title, 'lqt_new_thread_form' );
			$this->output->addHTML("<strong><a href=\"$url\">Start a Discussion</a></strong>");
		}

		$threads = Thread::latestNThreadsOfArticle($this->article, 10);		
		foreach($threads as $t) {
			$this->showThread($t);
		}
	}
	
	function showArchiveWidget($month) {
		global $wgLang; // TODO global.
		
		$options = Thread::monthsWhereArticleHasThreads($this->article);
		array_unshift($options, 'Last 30 days' ); # prepend.
		
		$this->openDiv('lqt_archive_widget');
		$this->output->addHTML('<form><select>');
		foreach( $options as $o ) {
			$this->output->addHTML("<option>$o</option>");
		}
		$this->output->addHTML('</select></form>');
		$this->closeDiv();
	}
	
	function show() {
		global $wgHooks;
		$wgHooks['SkinTemplateTabs'][] = array($this, 'customizeTabs');
		
		$this->output->setPageTitle( "Talk:" . $this->title->getText() );
		
		$month = $this->request->getVal('lqt_archive_month');
		$this->showArchiveWidget($month);
		if ( $month ) {
			$this->showArchive($month);
		} else {
			$this->showLatest();
		}
	}
}

class ThreadPermalinkView extends LqtView {
	function showThreadHeading( $thread ) {
		if ( $thread->hasSubject() && $this->headerLevel == 1 ) {
			$this->output->setPageTitle( "Thread: " . $thread->subject() );
		} else {
			parent::showThreadHeading($thread);
		}
	}
	
	function show() {
		/* Extract the numeric ID after the slash in the URL. */
		$title_string = $this->request->getVal('title');
		$a = explode('/', $title_string);
		if ( $a == false || count( $a ) < 2 || !ctype_digit($a[1])  ) {
			echo("bad request (TODO real error msg?)");
			die();
		}
		$thread_id = intval($a[1]);

		$t = Thread::newFromId( $thread_id );
		$this->article = $t->article(); # for creating reply threads.
		
		$this->output->setPageTitle( "Thread: #$thread_id" ); // Default if no subject line.

$talkpage_link = $this->user->getSkin()->makeKnownLinkObj($t->article()->getTitle()->getTalkpage());
		if ( $t->superthread() ) {
/*			$this->output->addHTML(<<<HTML
			<p class="lqt_context_message">You are viewing a reply to another post.
			<a href="{$this->permalinkUrl($t->topmostThread())}">View the entire discussion.</a></p>
HTML
);*/
			$this->output->setSubtitle( "a fragment of <a href=\"{$this->permalinkUrl($t->topmostThread())}\">a discussion</a> from " . $talkpage_link );
		} else {
			
			$this->output->setSubtitle( "from " . $talkpage_link );
		}
		
		$this->showThread($t);
	}
}

/*
 The Thread special page pseudo-namespace follows. We have to do this goofy wgExtensionFunctions
 run-around because the files required by SpecialPage aren't required_onced() yet by the time
 this file is. Don't ask me why.
*/

$wgExtensionFunctions[] = 'wfLqtSpecialThreadPage';

function wfLqtSpecialThreadPage() {
	global $wgMessageCache;

	require_once('SpecialPage.php');
	
    $wgMessageCache->addMessage( 'thread', 'Thread' );
	
	class SpecialThreadPage extends SpecialPage {

		function __construct() {
			SpecialPage::SpecialPage( 'Thread' );
			SpecialPage::$mStripSubpages = false;
			$this->includable( false );
		}

		function execute( $par = null ) {
			global $wgOut, $wgRequest, $wgTitle, $wgArticle, $wgUser;

			$this->setHeaders();
			
			$view = new ThreadPermalinkView( $wgOut, $wgArticle, $wgTitle, $wgUser, $wgRequest );
			$view->show();
		}
	}
	
	 SpecialPage::addPage( new SpecialThreadPage() );
}

}

