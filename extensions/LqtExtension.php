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

require_once('LqtModel.php');

class LqtDispatch {
	/* Invoked from performAction() in Wiki.php if this is a discussion namespace. */
	static function talkpageMain(&$output, &$talk_article, &$title, &$user, &$request) {
		// We are given a talkpage article and title. Find the associated
		// non-talk article and pass that to the view.
		$article_title = Title::makeTitle($title->getNamespace() - 1,
		                                  $title->getDBkey());
		$article = new Article($article_title);

		$view = new TalkpageView( $output, $article, $title, $user, $request );
		$view->show();
	}

	static function threadPermalinkMain(&$output, &$article, &$title, &$user, &$request) {
		$view = new ThreadPermalinkView( $output, $article, $title, $user, $request );
		$view->show();
	}
	
	/**
	* If the page we recieve is a Liquid Threads page of any kind, process it
	* as needed and return True. If it's a normal, non-liquid page, return false.
	*/
	static function tryPage( $output, $article, $title, $user, $request ) {
		if ( $title->isTalkPage() ) {
			self::talkpageMain ($output, $article, $title, $user, $request);
			return true;
		} else if ( $title->getNamespace() == NS_LQT_THREAD ) {
			self::threadPermalinkMain($output, $article, $title, $user, $request);
			return true;
		}
		return false;
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

	static protected $occupied_titles = array();

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

	function permalinkUrl( $thread, $query ='' ) {
		return $thread->rootPost()->getTitle()->getFullURL($query);
		return SpecialPage::getTitleFor('Thread', $thread->id())->getFullURL($query);
	}
	
	/*************************
	* Simple HTML methods    *
	*************************/

	function openDiv( $class='', $id='' ) {
		$this->output->addHTML( wfOpenElement( 'div', array('class'=>$class, 'id'=>$id) ) );
	}

	function closeDiv() {
		$this->output->addHTML( wfCloseElement( 'div' ) );
	}

	/************************************
	* Editing methods (here be dragons) *
	*************************************/

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

	function showSummarizeForm( $thread ) {
		$this->showEditingFormInGeneral( null, 'summarize', $thread );
	}

	private function showEditingFormInGeneral( $thread, $edit_type, $edit_applies_to ) {		
		/*
		 EditPage needs an Article. If there isn't a real one, as for new posts,
		 replies, and new summaries, we need to generate a title. Auto-generated
		 titles are based on the subject line. If the subject line is blank, we
		 can temporarily use a random scratch title. It's fine if the title changes
		 throughout the edit cycle, since the article doesn't exist yet anyways.
		*/
		if ($edit_type == 'summarize' && $edit_applies_to->summary() ) {
			$article = $edit_applies_to->summary();
		} else if ($edit_type == 'summarize') {
			$t = $this->newSummaryTitle($edit_applies_to);
			$article = new Article($t);
		} else if ( $thread == null ) {
			$subject = $this->request->getVal('lqt_subject_field', '');
			if ($edit_type == 'new') {
				$t = $this->newScratchTitle($subject);
			} else if ($edit_type == 'reply') {
				$t = $this->newReplyTitle($subject, $edit_applies_to);
			}
			$article = new Article($t);
		} else {
			$article = $thread->rootPost();
		}
		
		$e = new EditPage($article);
		$e->suppressIntro = true;
		
		$e->editFormTextBeforeContent .=
			$this->perpetuate('lqt_edit_post', 'hidden') .
			$this->perpetuate('lqt_reply_to', 'hidden') .
			$this->perpetuate('lqt_new_thread_form', 'hidden') .
			$this->perpetuate('lqt_summarize', 'hidden');
		
		if ( /*$thread == null*/ $edit_type=='new' || ($thread && $thread->superthread() == null) ) {
			// This is a top-level post; show the subject line.
			$sbjtxt = $thread ? $thread->subjectWithoutIncrement() : '';
			$subject = $this->request->getVal('lqt_subject_field', $sbjtxt);
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
		if ($edit_type != 'editExisting' && $edit_type != 'summarize' && $e->didSave) {
			$thread = Thread::newThread( $article, $this->article );
			if ( $edit_type == 'reply' ) {
				$thread->setSuperthread( $edit_applies_to );
			} else {
				$thread->touch();
			}
		}
		
		if ($edit_type == 'summarize' && $e->didSave) {
			$edit_applies_to->setSummary( $article );
		}
		
		// Move the thread and replies if subject changed.
		if( $edit_type == 'editExisting' && $e->didSave ) {
			$subject = $this->request->getVal('lqt_subject_field', '');
			if ( $subject && $subject != $thread->subjectWithoutIncrement() ) {
				//$this->renameThread($thread, $subject);
			}
		}

/*		$subject = $this->request->getVal('lqt_subject_field', '');
		if ( $e->didSave && $subject != '' ) {
			$thread->setSubject( Sanitizer::stripAllTags($subject) );
		} else if ( $e->didSave && $edit_type !='summarize' && $subject == '' && !$thread->superthread() ) {
				$thread->setSubject( '«no subject»' );
		} */
	}
	
	function renameThread($t,$s) {
		$this->simplePageMove($t->rootPost()->getTitle(),$s);
		// TODO here create a redirect from old page to new.
		foreach( $t->subthreads() as $st ) {
			$this->renameThread($st, $s);
		}
	}
	
	function scratchTitle() {
		$token = md5(uniqid(rand(), true));
		return Title::newFromText( "Thread:$token" );
	}
	function newScratchTitle($subject) {
		return $this->incrementedTitle( $subject?$subject:"«no subject»", NS_LQT_THREAD );
	}
	function newSummaryTitle($t) {
		return $this->incrementedTitle( "Summary of " . $t->subject(), NS_LQT_SUMMARY );
	}
	function newReplyTitle($s, $t) {
		return $this->incrementedTitle( $t->subjectWithoutIncrement(), NS_LQT_THREAD );
	}
	/** Keep trying titles starting with $basename until one is unoccupied. */
	function incrementedTitle($basename, $namespace) {
		$i = 1; do {
			$t = Title::newFromText( $basename.'_'.$i, $namespace );
			$i++;
		} while ( $t->exists() || in_array($t->getPrefixedDBkey(), self::$occupied_titles) );
		return $t;
	}

	/* Adapted from MovePageForm::doSubmit in SpecialMovepage.php. */
	function simplePageMove( $old_title, $new_subject ) {
		if ( $this->user->pingLimiter( 'move' ) ) {
			$this->out->rateLimited();
			return false;
		}

		# Variables beginning with 'o' for old article 'n' for new article

		$ot = $old_title;
		$nt = $this->incrementedTitle($new_subject, $old_title->getNamespace());

		self::$occupied_titles[] = $nt->getPrefixedDBkey();

		# don't allow moving to pages with # in
		if ( !$nt || $nt->getFragment() != '' ) {
			echo "malformed title"; // TODO real error reporting.
			return false;
		}

		$error = $ot->moveTo( $nt, true, "changed thread subject" );
		if ( $error !== true ) {
			var_dump($error);
			echo "something bad happened trying to rename the thread."; // TODO
			return false;
		}

		# Move the talk page if relevant, if it exists, and if we've been told to
		 // TODO we need to implement correct moving of talk pages everywhere later.
		// Snipped.

		return true;
	}

	/*************************
	* Output methods         *
	*************************/

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
			$query = array( 'lqt_edit_post' => $operand ? $operand->id() : null );
//			$query = array( 'lqt_edit_post' => $operand ? $operand->rootPost()->getTitle()->getPrefixedURL() : null );
		} else if ($operator == 'lqt_new_thread_form' ) {
			$query = array( 'lqt_new_thread_form' => '1' );
		} else {
			$query = array();
		}
		return $title->getFullURL( $this->queryStringFromArray($query) );
	}

	function showThreadFooter( $thread ) {
		$color_number = $this->selectNewUserColor( $thread->rootPost()->originalAuthor() );
		$this->output->addHTML(wfOpenElement('ul', array('class'=>"lqt_footer" )));

		$this->output->addHTML( wfOpenElement( 'li', array('class'=>"lqt_author_sig  lqt_post_color_$color_number") ) );
		$p = new Parser(); $sig = $p->getUserSig( $thread->rootPost()->originalAuthor() );
		$this->output->addWikitext( $sig, false );
		$this->output->addHTML( wfCloseElement( 'li' ) );
		
		$this->output->addHTML( wfOpenElement( 'li' ) );
		$d = new Date($thread->touched());
		$this->output->addHTML( $d->lastMonth()->text() );
		$this->output->addHTML( wfCloseElement( 'li' ) );
		
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
		
		if( $this->commandAppliesToThread( 'lqt_edit_post', $thread ) ) {
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
		if ( $thread->hasDistinctSubject() ) {
			$html = $thread->subjectWithoutIncrement() .
			        ' <span class="lqt_subject_increment">' .
			        $thread->increment() . '</span>';
			$this->output->addHTML( wfOpenElement( "h{$this->headerLevel}", array('class'=>'lqt_header') ) .
			                        $html . wfCloseElement("h{$this->headerLevel}") );
//			$this->output->addHTML( wfElement( "h{$this->headerLevel}",
//				                               array('class'=>'lqt_header'),
//				                               $html) );
		}
		if ( !$thread->superthread() && !$thread->summary() ) {
			$url = $this->permalinkUrl( $thread, 'lqt_summarize=1' );
			$this->output->addHTML( <<<HTML
			<span class="lqt_summarize_command">[<a href="{$url}">Summarize</a>]</span>
HTML
			);
		}
	}

	function showThread( $thread, $suppress_summaries = false ) {
		$this->showThreadHeading( $thread );
		
		if( $thread->summary() && !$suppress_summaries ) {
			$this->showSummary($thread);
			$this->output->addHTML( "<a href=\"{$this->permalinkUrl($thread)}\" class=\"lqt_thread_show_summary\">Show this thread</a>" );
			return;
		}
		
		$this->openDiv('lqt_thread', "lqt_thread_id_{$thread->id()}");			
		
		$this->showRootPost( $thread );
		$this->indent();
		foreach( $thread->subthreads() as $st ) {
			$this->showThread($st);
		}
		$this->unindent();
		
		$this->closeDiv();
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
	
	function showSummary($t) {
		if ( !$t->summary() ) return;
		$this->output->addHTML(<<<HTML
			<span class="lqt_thread_permalink_summary_title">
			This thread has been summarized as follows:
			</span><span class="lqt_thread_permalink_summary_edit">
			[<a href="{$this->permalinkUrl($t,'lqt_summarize=1')}">edit</a>]
			</span>
HTML
		);
		$this->openDiv('lqt_thread_permalink_summary');
		$this->showPostBody($t->summary());
		$this->closeDiv();
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
		// TODO having a subtitle screws up our relative positioning on the widget.
//		$this->output->setSubtitle("Archived threads from {$this->formattedMonth($month)}.");
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

		$threads = Thread::threadsOfArticleInLastNDays($this->article, 30);		
		foreach($threads as $t) {
			$this->showThread($t);
		}
	}
	
	function formattedMonth($yyyymm) {
		global $wgLang; // TODO global.
		return $wgLang->getMonthName( substr($yyyymm, 4, 2) ).' '.substr($yyyymm, 0, 4);
	}
	
	function showArchiveWidget($month) {
		global $wgLang; // TODO global.
		
		$sel = $this->request->getVal('lqt_archive_month', 'recent');
		
		$months = Thread::monthsWhereArticleHasThreads($this->article);

		$options = array( 'Last 30 days' => 'recent' );
		foreach($months as $m) {
			$options[$this->formattedMonth($m)] = $m;
		}
		
		$this->openDiv('lqt_archive_widget');
		$this->output->addHTML(<<<HTML
		<form id="lqt_archive_browser_form" action="{$this->title->getLocalURL()}"><select name="lqt_archive_month" id="lqt_archive_month">
HTML
);
		foreach( $options as $label => $value ) {
			$selected = $sel == $value ? 'selected="true"' : '';
			$this->output->addHTML("<option value=\"$value\" $selected>$label");
		}
		$this->output->addHTML(<<<HTML
		</select><input type="submit" id="lqt_archive_go_button" value="Go"></form>
HTML
		);
		$this->closeDiv();
	}
	
	function addJSandCSS() {
		global $wgJsMimeType, $wgStylePath; // TODO globals.
		$s = "<script type=\"{$wgJsMimeType}\" src=\"{$wgStylePath}/common/lqt.js\"><!-- lqt js --></script>\n";
		$this->output->addScript($s);
	}
	
	function show() {
		global $wgHooks;
		$wgHooks['SkinTemplateTabs'][] = array($this, 'customizeTabs');
		
		$this->output->setPageTitle( "Talk:" . $this->title->getText() ); // TODO non-main namespaces.
		$this->addJSandCSS();
		
		$month = $this->request->getVal('lqt_archive_month');
		$this->showArchiveWidget($month);
		if ( $month && $month != 'recent' ) {
			$this->showArchive($month);
		} else {
			$this->showLatest();
		}
	}
}

class ThreadPermalinkView extends LqtView {
	function showThreadHeading( $thread ) {
		if ( $this->headerLevel == 1 ) {
			$this->output->setPageTitle( $thread->wikilink() );
		} else {
			parent::showThreadHeading($thread);
		}
	}

	function show() {
		$ts = Thread::threadsWhoseRootPostIs( $this->article );
		if( count($ts) == 0 ) {echo "no such thread"; die();}
		if ( count($ts) >1 ) {die();} // TODO handle this screwy situation.
		$t = $ts[0];

		// TODO this is a holdover from the special page; not sure what's correct here.
		// we now have a real true $this->article that makes some sense.
		// but we still want to know about $t->article.
		$this->article = $t->article(); # for creating reply threads.
		
		// Make a link back to the talk page, including the correct archive month.
		if (Date::now()->nDaysAgo(30)->midnight()->isBefore( new Date($t->touched()) ))
			$query = '';
		else
			$query = 'lqt_archive_month=' . substr($t->touched(),0,6);
			
		$talkpage = $t->article()->getTitle()->getTalkpage();
		$talkpage_link = $this->user->getSkin()->makeKnownLinkObj($talkpage, '', $query);
		
		if ( $t->superthread() ) {
			$this->output->setSubtitle( "a fragment of <a href=\"{$this->permalinkUrl($t->topmostThread())}\">a discussion</a> from " . $talkpage_link );
		} else {
			
			$this->output->setSubtitle( "from " . $talkpage_link );
		}
		
		if( $this->request->getBool('lqt_summarize') ) {
			$this->showSummarizeForm($t);
		} else if ( $t->summary() ) {
			$this->showSummary($t);
		} else if ( !$t->superthread() ) {
			$this->output->addHTML("<p class=\"lqt_summary_notice\">If this discussion seems to be concluded, you are encouraged to <a href=\"{$this->permalinkUrl($t, 'lqt_summarize=1')}\">write a summary</a>.</p>");
		}
		
		$this->showThread($t, true);
	}

}

