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
require_once('Pager.php');
require_once('PageHistory.php');

class LqtDispatch {
	public static $views = array(
		'TalkpageArchiveView' => 'TalkpageArchiveView',
		'TalkpageView' => 'TalkpageView',
		'ThreadHistoryView' => 'ThreadHistoryView',
		'ThreadPermalinkView' => 'ThreadPermalinkView'
		);

	static function talkpageMain(&$output, &$talk_article, &$title, &$user, &$request) {
		// We are given a talkpage article and title. Find the associated
		// non-talk article and pass that to the view.
		$article_title = Title::makeTitle($title->getNamespace() - 1,
		                                  $title->getDBkey());
		$article = new Article($article_title);

		if ( $request->getVal('lqt_method') == 'talkpage_archive' ) {
			$viewname = self::$views['TalkpageArchiveView'];
		} else {
			$viewname = self::$views['TalkpageView'];
		}
		$view = new $viewname( $output, $article, $title, $user, $request );
		$view->show();
	}

	static function threadPermalinkMain(&$output, &$article, &$title, &$user, &$request) {
			/* breaking the lqt_method paradigm to make the history tab work. 
			  (just changing the href doesn't make the highlighting correct.) */
		if( $request->getVal('action') == 'history' ) {
			$viewname = self::$views['ThreadHistoryView'];
		} else {
			$viewname = self::$views['ThreadPermalinkView'];
		}
		$view = new $viewname( $output, $article, $title, $user, $request );
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

$wgHooks['MediaWikiPerformAction'][] = array('LqtDispatch::tryPage');

class LqtView {
	protected $article;
	protected $output;
	protected $user;
	protected $title;
	protected $request;
	
	protected $headerLevel = 1; 	/* h1, h2, h3, etc. */
	protected $user_colors;
	protected $user_color_index;
	const number_of_user_colors = 6;

	protected $queries;
	
	public $archive_start_days = 14;
	public $archive_recent_days = 5;
	
	function __construct(&$output, &$article, &$title, &$user, &$request) {
		$this->article = $article;
		$this->output = $output;
		$this->user = $user;
		$this->title = $title;
		$this->request = $request;
		$this->user_colors = array();
		$this->user_color_index = 1;
		$this->queries = $this->initializeQueries();
	}
	
	function initializeQueries() {
		$g = new QueryGroup();
		$startdate = Date::now()->nDaysAgo($this->archive_start_days)->midnight();
		$recentstartdate = $startdate->nDaysAgo($this->archive_recent_days);
		$article_clause = <<<SQL
		IF(thread.thread_article = 0,
				thread.thread_article_title = "{$this->article->getTitle()->getDBkey()}"
				AND thread.thread_article_namespace = {$this->article->getTitle()->getNamespace()}
			, thread.thread_article = {$this->article->getID()})
SQL;
		$g->addQuery('fresh',
		              array($article_clause,
		                   'instr(thread.thread_path, ".")' => '0',
		                    '(thread.thread_timestamp >= ' . $startdate->text() .
		 					'  OR thread.thread_summary_page is NULL)'),
		              array('ORDER BY thread.thread_timestamp DESC'));
		$g->addQuery('archived',
		             array($article_clause,
		                   'instr(thread.thread_path, ".")' => '0',
		                   'thread.thread_summary_page is not null',
		                   'thread.thread_timestamp < ' . $startdate->text()),
		             array('ORDER BY thread.thread_timestamp DESC'));
		$g->extendQuery('archived', 'recently-archived',
		                array('( thread.thread_timestamp >=' . $recentstartdate->text() .
				      '  OR  rev_timestamp >= ' . $recentstartdate->text() . ')',
				      'page_id = thread.thread_summary_page', 'page_latest = rev_id'),
				array(),
				array('page', 'revision'));
		return $g;
	}

	static protected $occupied_titles = array();
	
	/*************************
     * (1) linking to liquidthreads pages and
     * (2) figuring out what page you're on and what you need to do.
	*************************/
	
	static function queryStringFromArray( $vars ) {
		$q = '';
		if ( $vars && count( $vars ) != 0 ) {
			foreach( $vars as $name => $value )
				$q .= "$name=$value&";
		}
		return $q;
	}

	function methodAppliesToThread( $method, $thread ) {
		return $this->request->getVal('lqt_method') == $method &&
			$this->request->getVal('lqt_operand') == $thread->id();
	}
	function methodApplies( $method ) {
		return $this->request->getVal('lqt_method') == $method;
	}

	function permalinkUrl( $thread, $method = null, $operand = null ) {
		$query = $method ? "lqt_method=$method" : "";
		$query = $operand ? "$query&lqt_operand={$operand->id()}" : $query;
		return $thread->root()->getTitle()->getFullUrl($query);
	}

	/* This is used for action=history so that the history tab works, which is
	   why we break the lqt_method paradigm. */
	function permalinkUrlWithQuery( $thread, $query ) {
		if ( is_array($query) ) $query = self::queryStringFromArray($query);
		return $thread->root()->getTitle()->getFullUrl($query);
	}

	function talkpageUrl( $title, $method = null, $operand = null ) {
		$query = $method ? "lqt_method=$method" : "";
		$query = $operand ? "$query&lqt_operand={$operand->id()}" : $query;
		return $title->getFullURL( $query );
	}

	/*************************************************************
	* Editing methods (here be dragons)                          *
        * Forget dragons: This section distorts the rest of the code *
        * like a star bending spacetime around itself.               *
	*************************************************************/

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
			$article = $thread->root();
		}
		
		$e = new EditPage($article);
		$e->suppressIntro = true;
		
		$e->editFormTextBeforeContent .=
			$this->perpetuate('lqt_method', 'hidden') .
			$this->perpetuate('lqt_operand', 'hidden');
		
		if ( /*$thread == null*/ $edit_type=='new' || ($thread && !$thread->hasSuperthread()) ) {
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
			if ( $edit_type == 'reply' ) {
				$thread = Threads::newThread( $article, $this->article, $edit_applies_to );
			} else {
				$thread = Threads::newThread( $article, $this->article );
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
			// this is unrelated to the subject change and is for all edits:
			$thread->setRootRevision( Revision::newFromTitle($thread->root()->getTitle()) );
			$thread->commitRevision();
		}

/*		$subject = $this->request->getVal('lqt_subject_field', '');
		if ( $e->didSave && $subject != '' ) {
			$thread->setSubject( Sanitizer::stripAllTags($subject) );
		} else if ( $e->didSave && $edit_type !='summarize' && $subject == '' && !$thread->hasSuperthread() ) {
				$thread->setSubject( '«no subject»' );
		} */
	}
	
	function renameThread($t,$s) {
		$this->simplePageMove($t->root()->getTitle(),$s);
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
		return $this->incrementedTitle( $t->subject(), NS_LQT_SUMMARY );
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

	function showThreadFooter( $thread ) {
		global $wgLang; // TODO global.
		
		$color_number = $this->selectNewUserColor( $thread->root()->originalAuthor() );
		$this->output->addHTML(wfOpenElement('ul', array('class'=>"lqt_footer" )));

		$this->output->addHTML( wfOpenElement( 'li', array('class'=>"lqt_author_sig  lqt_post_color_$color_number") ) );
		$p = new Parser(); $sig = $p->getUserSig( $thread->root()->originalAuthor() );
		$this->output->addWikitext( $sig, false );
		$this->output->addHTML( wfCloseElement( 'li' ) );
		
		$this->output->addHTML( wfOpenElement( 'li' ) );
		$this->output->addHTML( $wgLang->timeanddate($thread->timestamp()) );
		$this->output->addHTML( wfCloseElement( 'li' ) );
		
		$commands = array( 'Edit' => $this->talkpageUrl( $this->title, 'edit', $thread ),
		 					'Reply' => $this->talkpageUrl( $this->title, 'reply', $thread ),
		 					'Permalink' => $this->permalinkUrl( $thread ) );
/*
		if( !$thread->hasSuperthread() ) {
			$commands['History'] = $this->permalinkUrl($thread, 'history_listing');
		}
*/
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
		$post = $thread->root();

/*		$color_number = $this->selectNewUserColor( $thread->root()->originalAuthor() );
		$this->openDiv( "lqt_post lqt_post_color_$color_number" );*/
		$this->openDiv( 'lqt_post' );
		
		if( $this->methodAppliesToThread( 'edit', $thread ) ) {
			$this->showPostEditingForm( $thread );
		} else{
			$this->showPostBody( $post );
			$this->showThreadFooter( $thread );
		}
		
		$this->closeDiv();
		
		if( $this->methodAppliesToThread( 'reply', $thread ) ) {
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
		}
	}

	function showThread( $thread ) {
		$this->showThreadHeading( $thread );

		$timestamp = new Date($thread->timestamp());
		if( $thread->summary() ) {
			$this->showSummary($thread);
		} else if ( $timestamp->isBefore(Date::now()->nDaysAgo($this->archive_start_days))
		            && !$thread->summary() && !$thread->hasSuperthread() ) {
			$this->output->addHTML("<p class=\"lqt_summary_notice\">If this discussion seems to be concluded, you are encouraged to <a href=\"{$this->permalinkUrl($thread, 'summarize')}\">write a summary</a>. There have been no changes here for at least $this->archive_start_days days.</p>");
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

	function openDiv( $class='', $id='' ) {
		$this->output->addHTML( wfOpenElement( 'div', array('class'=>$class, 'id'=>$id) ) );
	}

	function closeDiv() {
		$this->output->addHTML( wfCloseElement( 'div' ) );
	}
	
	function showSummary($t) {
		if ( !$t->summary() ) return;
		$this->output->addHTML(<<<HTML
			<span class="lqt_thread_permalink_summary_title">
			This thread has been summarized as follows:
			</span><span class="lqt_thread_permalink_summary_edit">
			[<a href="{$this->permalinkUrl($t,'summarize')}">edit</a>]
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
		unset($content_actions['move']);
		
		/*
		TODO: 
		We could make these tabs actually follow the tab metaphor if we repointed
		the 'history' and 'edit' tabs to the original subject page. That way 'discussion'
		would just be one of four ways to view the article. But then those other tabs, for
		logged-in users, don't really fit the metaphor. What to do, what to do?
		*/
		return true;
	}

	function permalinksForThreads($ts, $method = null, $operand = null) {
		$ps = array();
		foreach ($ts as $t) {
			$u = $this->permalinkUrl($t, $method, $operand);
			$l = $t->subjectWithoutIncrement();
			$ps[] = "<a href=\"$u\">$l</a>";
		}
		return $ps;
	}
	
	function showArchiveWidget() {
		$threads = $this->queries->query('recently-archived');
		$threadlinks = $this->permalinksForThreads($threads);
		
		if ( count($threadlinks) > 0 ) {
			$this->openDiv('lqt_archive_teaser');
			$this->output->addHTML('The following threads were archived recently:');
			$this->outputList('ul', '', '', $threadlinks);
		} else {
			$this->openDiv();
		}
		$url = $this->talkpageUrl($this->title, 'talkpage_archive');
		$this->output->addHTML(<<<HTML
			<a href="$url" class="lqt_browse_archive">Browse the Archive</a>
HTML
		);
		$this->closeDiv();
	}
	
	function addJSandCSS() {
		global $wgJsMimeType, $wgStylePath; // TODO globals.
		$s = "<script type=\"{$wgJsMimeType}\" src=\"{$wgStylePath}/common/lqt.js\"><!-- lqt js --></script>\n";
		$this->output->addScript($s);
	}
	
	function showHeader() {
		/*
		Right now the header wikitext is stored in the actual talkpage that we
        are masking with the LQT stuff. I'm not sure I really like this. But
        if we stored it in, say, Header:Namespace:Article, we would need to
        move that whenever either the namespace or article name moved. We
        might also store it as Header:namespace_id:article_id, but then that's
        more crap to hide. Still, Header:Namespace:Article would look better
        in RC than just Namespace_talk:Article. It's not really accurate to say
		you're editing the talkpage itself here.
		*/
		
		$action = $this->request->getVal('lqt_header_action');
		
		$article = new Article( $this->title );
		if( $action == 'edit' || $action=='submit' ) {
			// TODO this is scary and horrible.
			$e = new EditPage($article);
			$e->suppressIntro = true;
			$e->editFormTextBeforeContent .=
				$this->perpetuate('lqt_header_action', 'hidden');
			$e->edit();
		} else if ( $action == 'history' ) {
			$this->output->addHTML("Disclaimer: history doesn't really work yet.");
			$history = new PageHistory( $article );
			$history->history();
		} else if ( $article->exists() ) {
			$edit = $this->title->getFullURL( 'lqt_header_action=edit' );
			$history = $this->title->getFullURL( 'lqt_header_action=history' );
			$this->openDiv('lqt_header_content');
			$this->showPostBody($article);
			$this->outputList('ul', 'lqt_header_commands', null, array(
				"[<a href=\"$edit\">edit</a>]", 
				"[<a href=\"$history\">history</a>]"
				));
			$this->closeDiv();
		} else {
			$this->output->addHTML("<p class=\"lqt_header_notice\">[<a href=\"{$this->title->getFullURL('lqt_header_action=edit')}\">add header</a>]</p>");
		}
	}
	
	function outputList( $kind, $class, $id, $contents ) {
		$this->output->addHTML(wfOpenElement($kind, array('class'=>$class,'id'=>$id)));
		foreach ($contents as $li) {
			$this->output->addHTML( wfOpenElement('li') );
			$this->output->addHTML( $li );
			$this->output->addHTML( wfCloseElement('li') );
		}
		$this->output->addHTML(wfCloseElement($kind));
	}
	
	function show() {
		global $wgHooks;
		$wgHooks['SkinTemplateTabs'][] = array($this, 'customizeTabs');
		
		$this->output->setPageTitle( "Talk:" . $this->title->getText() ); // TODO non-main namespaces.
		$this->addJSandCSS();

		$this->showHeader();
		
		$this->showArchiveWidget();

		var_dump(HistoricalThread::withIdAtRevision(3,11));
		
		if( $this->methodApplies('talkpage_new_thread') ) {
			$this->showNewThreadForm();
		} else {
			$url = $this->talkpageUrl( $this->title, 'talkpage_new_thread' );
			$this->output->addHTML("<strong><a class=\"lqt_start_discussion\" href=\"$url\">Start&nbsp;a&nbsp;Discussion</a></strong>");
		}

		$threads = $this->queries->query('fresh');
		foreach($threads as $t) {
			$this->showThread($t);
		}
	}
}

class TalkpageArchiveView extends TalkpageView {
	function __construct(&$output, &$article, &$title, &$user, &$request) {
		parent::__construct($output, $article, $title, $user, $request);
		$this->loadQueryFromRequest();
	}
	
	function showThread($t) {
		$this->output->addHTML(<<<HTML
<tr>
	<td><a href="{$this->permalinkUrl($t)}">{$t->subjectWithoutIncrement()}</a></td>
	<td>
HTML
);		$this->showPostBody($t->summary());
		$this->output->addHTML(<<<HTML
	</td>
</tr>
HTML
);
	}
	
	function loadQueryFromRequest() {
		// Begin with with the requirements for being *in* the archive.
		$startdate = Date::now()->nDaysAgo($this->archive_start_days)->midnight();
		$where = array('thread.thread_article' => $this->article->getID(),
		                     'instr(thread.thread_path, ".")' => '0',
		                     'thread.thread_summary_page is not null',
		                     'thread.thread_timestamp < ' . $startdate->text());
		$options = array('ORDER BY thread.thread_timestamp DESC');
		
		$annotations = array("Searching for threads");

		$r = $this->request;

		/* START AND END DATES */
		// $this->start and $this->end are clipped into the range of available
		// months, for use in the actual query and the selects. $this->raw* are
		// as actually provided, for use by the 'older' and 'newer' buttons.
		$ignore_dates = ! $r->getVal('lqt_archive_filter_by_date', true);
		if ( !$ignore_dates ) {
			$months = Threads::monthsWhereArticleHasThreads($this->article);
		}
		$s = $r->getVal('lqt_archive_start');
		if ($s && ctype_digit($s) && strlen($s) == 6 && !$ignore_dates) {
			$this->selstart = new Date( "{$s}01000000" );
			$this->starti = array_search($s, $months);
			$where[] = 'thread_timestamp >= ' . $this->selstart->text();
		}
		$e = $r->getVal('lqt_archive_end');
		if ($e && ctype_digit($e) && strlen($e) == 6 && !$ignore_dates) {
			$this->selend = new Date("{$e}01000000");
			$this->endi = array_search($e, $months);
			$where[] = 'thread_timestamp < ' . $this->selend->nextMonth()->text();
		}
		if ( isset($this->selstart) && isset($this->selend) ) {

			$this->datespan = $this->starti - $this->endi;

			$annotations[] = "from {$this->selstart->text()} to {$this->selend->text()}";
		} else if (isset($this->selstart)) {
			$annotations[] = "after {$this->selstart->text()}";
		} else if (isset($this->selend)) {
			$annotations[] = "before {$this->selend->text()}";
		}

		$this->where = $where;
		$this->options = $options;
		$this->annotations = implode("<br>\n", $annotations);
	}

	function threads() {
		return Threads::where($this->where, $this->options);
	}

	function formattedMonth($yyyymm) {
		global $wgLang; // TODO global.
		return $wgLang->getMonthName( substr($yyyymm, 4, 2) ).' '.substr($yyyymm, 0, 4);
	}

	function monthSelect($months, $name) {
		$selection =  $this->request->getVal($name);

		// Silently adjust to stay in range.
		$selection = max( min( $selection, $months[0] ), $months[count($months)-1] );

		$options = array();
		foreach($months as $m) {
			$options[$this->formattedMonth($m)] = $m;
		}
		$result = "<select name=\"$name\" id=\"$name\">";
		foreach( $options as $label => $value ) {
			$selected = $selection == $value ? 'selected="true"' : '';
			$result .= "<option value=\"$value\" $selected>$label";
		}
		$result .= "</select>";
		return $result;
	}
	
	/**
     * Return a URL for the current page, including Title and query vars,
	 * with the given replacements made.
     * @param $repls array( 'name'=>new_value, ... )
	*/
	function queryReplace( $repls ) {
		$vs = $this->request->getValues();
		$rs = array();
		foreach ($vs as $k => $v) {
			if ( array_key_exists( $k, $repls ) ) {
				$rs[$k] = $repls[$k];
			} else {
				$rs[$k] = $vs[$k];
			}
		}
		return $this->title->getFullURL(self::queryStringFromArray($rs));
	}

	function clip( $vals, $min, $max ) {
		$res = array();
		foreach($vals as $val) $res[] =  max( min( $val, $max ), $min );
		return $res;
	}

	function showSearchForm() {
		$months = Threads::monthsWhereArticleHasThreads($this->article);
		
		$use_dates = $this->request->getVal('lqt_archive_filter_by_date', null);
		if ( $use_dates === null ) {
			$use_dates = $this->request->getBool('lqt_archive_start', false) ||
						 $this->request->getBool('lqt_archive_end', false);
		}
		$any_date_check    = !$use_dates ? 'checked="1"' : '';
		$these_dates_check =  $use_dates ? 'checked="1"' : '';

		if( isset($this->datespan) ) {
			$oatte = $this->starti + 1;
			$oatts = $this->starti + 1 + $this->datespan;

			$natts = $this->endi - 1;
			$natte = $this->endi - 1 - $this->datespan;

			list($oe, $os, $ns, $ne) =
				$this->clip( array($oatte, $oatts, $natts, $natte),
					     0, count($months)-1 );

			$older = '<a href="' . $this->queryReplace(array(
				     'lqt_archive_filter_by_date'=>'1',
				     'lqt_archive_start' => $months[$os],
				     'lqt_archive_end' => $months[$oe]))
				. '">«older</a>';
			$newer = '<a href="' . $this->queryReplace(array(
				     'lqt_archive_filter_by_date'=>'1',
				     'lqt_archive_start' => $months[$ns],
				     'lqt_archive_end' => $months[$ne]))
				. '">newer»</a>';
		}
		else {
			$older = '<span class="lqt_disabled_link" title="This link is disabled because you are viewing threads from all dates.">«older</span>';
			$newer = '<span class="lqt_disabled_link" title="This link is disabled because you are viewing threads from all dates.">newer»</span>';
		}
		
		$this->output->addHTML(<<<HTML
<form id="lqt_archive_search_form" action="{$this->title->getLocalURL()}">
	<input type="hidden" name="lqt_method" value="talkpage_archive">
        <input type="hidden" name="title" value="{$this->title->getPrefixedURL()}"	

	<input type="radio" id="lqt_archive_filter_by_date_no"
               name="lqt_archive_filter_by_date" value="0" {$any_date_check}>
	<label for="lqt_archive_filter_by_date_no">Any date</label>  <br>
	<input type="radio" id="lqt_archive_filter_by_date_yes"
               name="lqt_archive_filter_by_date" value="1" {$these_dates_check}>
	<label for="lqt_archive_filter_by_date_yes">Only these dates:</label> <br>

<table>	
<tr><td><label for="lqt_archive_start">From</label>
    <td>{$this->monthSelect($months, 'lqt_archive_start')} <br>
<tr><td><label for="lqt_archive_end">To</label>
    <td>{$this->monthSelect($months, 'lqt_archive_end')}
</table>
	<input type="submit">
        $older $newer
</table>
</form>
HTML
);
	}
	
	function show() {
		global $wgHooks;
		$wgHooks['SkinTemplateTabs'][] = array($this, 'customizeTabs');
		
		$this->output->setPageTitle( "Talk:" . $this->title->getText() ); // TODO non-main namespaces.
		$this->addJSandCSS();
		
		$this->showSearchForm();

		$this->output->addHTML(<<<HTML
<p class="lqt_search_annotations">{$this->annotations}</p>
<table class="lqt_archive_listing">
<col class="lqt_titles" />
<col class="lqt_summaries" />
<tr><th>Title<th>Summary</tr>
HTML
                );
		foreach ($this->threads() as $t) {
			$this->showThread($t);
		}
		$this->output->addHTML('</table>');
	}
}
/*
CREATE TABLE historical_thread (
  -- Note that many hthreads can share an id, which is the same as the id
  -- of the live thread. It is only the id/revision combo which must be unique.
  hthread_id int(8) unsigned NOT NULL,
  hthread_revision int(8) unsigned NOT NULL,
  hthread_contents BLOB NOT NULL,
  PRIMARY KEY hthread_id_revision (hthread_id, hthread_revision)
) TYPE=InnoDB;
*/
/**
 * @addtogroup Pager
 */
class ThreadHistoryPager extends PageHistoryPager {
	protected $thread;
	
	function __construct( $thread ) {
		// mPageHistory = this in the PageHistoryPager methods now.
		parent::__construct($this);
		$this->thread = $thread;
	}

	function getQueryInfo() {
		return array(
			'tables' => 'historical_thread',
			'fields' => 'hthread_id, hthread_revision, hthread_contents',
			'conds' => array('hthread_id' => $this->thread->id() ),
			'options' => array()
		);
	}

	function getIndexField() {
		return 'hthread_revision';
	}
	
	/**
	 * Returns a row from the history printout.
	 *
	 * @param object $row The database row corresponding to the line (or is it the previous line?).
	 * @param object $next The database row corresponding to the next line (or is it this one?).
	 * @param int $counter Apparently a counter of what row number we're at, counted from the top row = 1.
	 * @param $notificationtimestamp
	 * @param bool $latest Whether this row corresponds to the page's latest revision.
	 * @param bool $firstInList Whether this row corresponds to the first displayed on this history page.
	 * @return string HTML output for the row
	 */
	function historyLine( $row, $next, $counter = '', $notificationtimestamp = false, $latest = false, $firstInList = false ) {
		
	}
	
/*
	function formatRow( $row ) {
		return '<li>' . $row->hthread_revision;
	}
*/	
	function getStartBody() {
		$this->mLastRow = false;
		$this->mCounter = 1;
		return 'start';
	}

	function getEndBody() {
		return "";
	}
}

class ThreadHistoryView extends ThreadPermalinkView {
		
	function showHistoryListing($t) {
		$pager = new ThreadHistoryPager( $this->thread );
		$this->linesonpage = $pager->getNumRows();
		$this->output->addHTML(
			$pager->getNavigationBar() . 
//			$this->beginHistoryList() . 
			$pager->getBody() .
//			$this->endHistoryList() .
			$pager->getNavigationBar()
		);
	}
	
	function show() {
		$t = Threads::withRoot( $this->article );
		$this->thread = $t;

		// TODO this is a holdover from the special page; not sure what's correct here.
		// we now have a real true $this->article that makes some sense.
		// but we still want to know about $t->article.
		$this->article = $t->article(); # for creating reply threads.
		
		$this->output->setSubtitle("viewing a historical thread...");
				
		$this->showThreadHeading($t);
		$this->showHistoryListing($t);

		$this->showThread($t);
	}
}

class ThreadPermalinkView extends LqtView {
	protected $thread;
	
	function showThreadHeading( $thread ) {
		if ( $this->headerLevel == 1 ) {
			$this->output->setPageTitle( $thread->wikilink() );
		} else {
			parent::showThreadHeading($thread);
		}
	}

	function show() {
		$t = Threads::withRoot( $this->article );
		$this->thread = $t;

		// TODO this is a holdover from the special page; not sure what's correct here.
		// we now have a real true $this->article that makes some sense.
		// but we still want to know about $t->article.
		$this->article = $t->article(); # for creating reply threads.
		
		// Make a link back to the talk page, including the correct archive month.
		// TODO this is obsolete.
		if (Date::now()->nDaysAgo(30)->midnight()->isBefore( new Date($t->timestamp()) ))
			$query = '';
		else
			$query = 'lqt_archive_month=' . substr($t->timestamp(),0,6);
			
		$talkpage = $t->article()->getTitle()->getTalkpage();
		$talkpage_link = $this->user->getSkin()->makeKnownLinkObj($talkpage, '', $query);
		
		if ( $t->hasSuperthread() ) {
			$this->output->setSubtitle( "a fragment of <a href=\"{$this->permalinkUrl($t->topmostThread())}\">a discussion</a> from " . $talkpage_link );
		} else {
			$this->output->setSubtitle( "from " . $talkpage_link );
		}
		
		if( $this->methodApplies('summarize') )
			$this->showSummarizeForm($t);
		
		$this->showThread($t);
	}
}
