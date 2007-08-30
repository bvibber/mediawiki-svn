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

function efVarDump($output, $value) {
	if ($output == null) {
		global $wgOut;
		$output = $wgOut;
	}
	ob_start();
	var_dump($value);
	$tmp=ob_get_contents();
	ob_end_clean();
	$output->addHTML('<pre>' . htmlspecialchars($tmp,ENT_QUOTES) . '</pre>');
}


require_once('LqtModel.php');
require_once('Pager.php');
require_once('PageHistory.php');

$wgHooks['MediaWikiPerformAction'][] = array('LqtDispatch::tryPage');
$wgHooks['SpecialMovepageAfterMove'][] = array('LqtDispatch::onPageMove');

class LqtDispatch {
	public static $views = array(
		'TalkpageArchiveView' => 'TalkpageArchiveView',
		'TalkpageHeaderView' => 'TalkpageHeaderView',
		'TalkpageView' => 'TalkpageView',
		'ThreadHistoryListingView' => 'ThreadHistoryListingView',
		'ThreadHistoricalRevisionView' => 'ThreadHistoricalRevisionView',
		'ThreadDiffView' => 'ThreadDiffView',
		'ThreadPermalinkView' => 'ThreadPermalinkView',
		'ThreadProtectionFormView' => 'ThreadProtectionFormView'
		);

	static function talkpageMain(&$output, &$talk_article, &$title, &$user, &$request) {
		// We are given a talkpage article and title. Find the associated
		// non-talk article and pass that to the view.
		$article = new Article($title->getSubjectPage());
		
		if( $title->getSubjectPage()->getNamespace() == NS_LQT_THREAD ) {
			// Threads don't have talk pages; redirect to the thread page.
			$output->redirect($title->getSubjectPage()->getFullUrl());
		}

		/* Certain actions apply to the "header", which is stored in the actual talkpage
		   in the database. Drop everything and behave like a normal page if those
		   actions come up, to avoid hacking the various history, editing, etc. code. */
		$action =  $request->getVal('action');
		$header_actions = array('history', 'edit', 'submit');
		if ($request->getVal('lqt_method', null) === null &&
				( in_array( $action, $header_actions ) ||
					$request->getVal('diff', null) !== null ) ) {
			$viewname = self::$views['TalkpageHeaderView'];
		} else if ( $action == 'protect' || $action == 'unprotect' ) {
			$viewname = self::$views['ThreadProtectionFormView'];
		} else if ( $request->getVal('lqt_method') == 'talkpage_archive' ) {
			$viewname = self::$views['TalkpageArchiveView'];
		} else {
			$viewname = self::$views['TalkpageView'];
		}
		$view = new $viewname( $output, $article, $title, $user, $request );
		return $view->show();
	}

	static function threadPermalinkMain(&$output, &$article, &$title, &$user, &$request) {
		/* breaking the lqt_method paradigm to make the history tab work. 
		  (just changing the href doesn't make the highlighting correct.) */
		$action =  $request->getVal('action');
		if( $action == 'history' ) {
			$viewname = self::$views['ThreadHistoryListingView'];
		} else if ( $action == 'protect' || $action == 'unprotect' ) {
			$viewname = self::$views['ThreadProtectionFormView'];
		} else if ( $request->getVal('lqt_method') == 'diff' ) {
			$viewname = self::$views['ThreadDiffView'];
		} else if ( $request->getVal('lqt_oldid', null) !== null ) {
			$viewname = self::$views['ThreadHistoricalRevisionView'];
		} else {
			$viewname = self::$views['ThreadPermalinkView'];
		}
		$view = new $viewname( $output, $article, $title, $user, $request );
		return $view->show();
	}
	
	/**
	* If the page we recieve is a Liquid Threads page of any kind, process it
	* as needed and return True. If it's a normal, non-liquid page, return false.
	*/
	static function tryPage( $output, $article, $title, $user, $request ) {
		if ( $title->isTalkPage() ) {
			return self::talkpageMain ($output, $article, $title, $user, $request);
		} else if ( $title->getNamespace() == NS_LQT_THREAD ) {
			return self::threadPermalinkMain($output, $article, $title, $user, $request);
		}
		return true;
	}
	
	static function onPageMove( $movepage, $ot, $nt ) {
		// We are being invoked on the subject page, not the talk page.
		
		$threads = Threads::where( array( Threads::articleClause(new Article($ot)),
		                                  Threads::topLevelClause() ));
		
		foreach ($threads as $t) {
			$t->moveToSubjectPage( $nt, false );
		}
		
		return true;
	}
}

 
class LqtView {
	protected $article;
	protected $output;
	protected $user;
	protected $title;
	protected $request;
	
	protected $headerLevel = 2; 	/* h1, h2, h3, etc. */
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
		$article_clause = Threads::articleClause($this->article);
		$g->addQuery('fresh',
		              array($article_clause,
		                   'instr(thread.thread_path, ".")' => '0',
		                    '(thread.thread_timestamp >= ' . $startdate->text() .
		 					'  OR (thread.thread_summary_page is NULL' . 
								 ' AND thread.thread_type='.Threads::TYPE_NORMAL.'))'),
		              array('ORDER BY thread.thread_timestamp DESC'));
		$g->addQuery('archived',
		             array($article_clause,
		                   'instr(thread.thread_path, ".")' => '0',
		                   '(thread.thread_summary_page is not null' .
			                  ' OR thread.thread_type='.Threads::TYPE_NORMAL.')',
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

	static function permalinkUrl( $thread, $method = null, $operand = null ) {
		$query = $method ? "lqt_method=$method" : "";
		$query = $operand ? "$query&lqt_operand={$operand->id()}" : $query;
		return $thread->root()->getTitle()->getFullUrl($query);
	}

	/* This is used for action=history so that the history tab works, which is
	   why we break the lqt_method paradigm. */
	static function permalinkUrlWithQuery( $thread, $query ) {
		if ( is_array($query) ) $query = self::queryStringFromArray($query);
		return $thread->root()->getTitle()->getFullUrl($query);
	}
	
	static function permalinkUrlWithDiff( $thread ) {
		$changed_thread = $thread->changeObject();
		$curr_rev_id = $changed_thread->rootRevision();
		$curr_rev = Revision::newFromTitle( $changed_thread->root()->getTitle(), $curr_rev_id );
		$prev_rev = $curr_rev->getPrevious();
		$oldid = $prev_rev ? $prev_rev->getId() : "";
		return self::permalinkUrlWithQuery( $changed_thread, array('lqt_method'=>'diff', 'diff'=>$curr_rev_id, 'oldid'=>$oldid) );
	}

	static function talkpageUrl( $title, $method = null, $operand = null, $includeFragment = true ) {
		$query = $method ? "lqt_method=$method" : "";
		$query = $operand ? "$query&lqt_operand={$operand->id()}" : $query;
		return $title->getFullURL( $query ) . ($operand && $includeFragment ? "#lqt_thread_{$operand->id()}" : "");
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
		
		if ( $edit_type=='new' || ($thread && !$thread->hasSuperthread()) ) {
			// This is a top-level post; show the subject line.
			$sbjtxt = $thread ? $thread->subjectWithoutIncrement() : '';
			$subject = $this->request->getVal('lqt_subject_field', $sbjtxt);
			$e->editFormTextBeforeContent .= <<<HTML
			<label for="lqt_subject_field">Subject: </label>
			<input type="text" size="60" name="lqt_subject_field" id="lqt_subject_field" value="$subject" tabindex="1"><br>
HTML;
		}

		$e->edit();

		// Override what happens in EditPage::showEditForm, called from $e->edit():
//		$wgOut->setArticleRelated( false ); 
		$this->output->setArticleFlag( false );

		// For replies and new posts, insert the associated thread object into the DB.
		if ($edit_type != 'editExisting' && $edit_type != 'summarize' && $e->didSave) {
			if ( $edit_type == 'reply' ) {
				$thread = Threads::newThread( $article, $this->article, $edit_applies_to, $e->summary );
				$edit_applies_to->commitRevision(Threads::CHANGE_REPLY_CREATED, $thread, $e->summary);
			} else {
				$thread = Threads::newThread( $article, $this->article, null, $e->summary );
			}
		}
		
		if ($edit_type == 'summarize' && $e->didSave) {
			$edit_applies_to->setSummary( $article );
			$edit_applies_to->commitRevision(Threads::CHANGE_EDITED_SUMMARY, $edit_applies_to, $e->summary);
		}
		
		// Move the thread and replies if subject changed.
		if( $edit_type == 'editExisting' && $e->didSave ) {
			$subject = $this->request->getVal('lqt_subject_field', '');
			if ( $subject && $subject != $thread->subjectWithoutIncrement() ) {
//				$reason = $this->request->getVal("wpSummary", "");
				$this->renameThread($thread, $subject, $e->summary);
			}
			// this is unrelated to the subject change and is for all edits:
			$thread->setRootRevision( Revision::newFromTitle($thread->root()->getTitle()) );
			$thread->commitRevision( Threads::CHANGE_EDITED_ROOT, $thread, $e->summary );
		}
				
		// I have lost track of where the redirect happens, so I can't set a flag there until I find it.
		// In the meantime, just check if somewhere somebody redirected. I'm afraid this might have
		// unwanted side-effects.
		if ( $this->output->getRedirect() != '' ) {
			$this->output->redirect( $this->title->getFullURL() . '#' . 'lqt_thread_' . $thread->id() );
		}
	}
	
	function renameThread($t,$s,$reason) {
		$this->simplePageMove($t->root()->getTitle(),$s, $reason);
		// TODO here create a redirect from old page to new.
		foreach( $t->subthreads() as $st ) {
			$this->renameThread($st, $s, $reason);
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
	function simplePageMove( $old_title, $new_subject, $reason ) {
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

		$error = $ot->moveTo( $nt, true, "Changed thread subject: $reason" );
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

	/* @return False if the article and revision do not exist and we didn't show it, true if we did. */
	function showPostBody( $post, $oldid = null ) {
		/* Why isn't this all encapsulated in Article somewhere? TODO */
		global $wgEnableParserCache;

		// Should the parser cache be used?
		$pcache = $wgEnableParserCache &&
		          intval( $this->user->getOption( 'stubthreshold' ) ) == 0 &&
		          $post->exists() &&
				  $oldid === null;
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
			$rev = Revision::newFromTitle( $post->getTitle(), $oldid );
			if ($rev) {
				$this->output->addWikiText( $rev->getText() );
				return true;
			} else {
				return false;
			}
		} else {
			return true;
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

		$edit_label = $thread->root()->getTitle()->quickUserCan( 'edit' ) ? 'Edit' : 'View source';
		
		$commands = array( $edit_label => $this->talkpageUrl( $this->title, 'edit', $thread ),
		 					'Reply' => $this->talkpageUrl( $this->title, 'reply', $thread ),
		 					'Permalink' => $this->permalinkUrl( $thread ) );

		if( !$thread->hasSuperthread() ) {
			$commands['History'] = $this->permalinkUrlWithQuery($thread, 'action=history');
		}
		
		if ( in_array('delete',  $this->user->getRights()) ) {
			if( $thread->type() == Threads::TYPE_DELETED )
				$delete_command_label = 'Undelete';
			else
				$delete_command_label = 'Delete';
			
			$commands[$delete_command_label] = SpecialPage::getPage('Deletethread')->getTitle()->getFullURL()
				. '/' . $thread->title()->getPrefixedURL();
		}

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

		$oldid = $thread->isHistorical() ? $thread->rootRevision() : null;

/*		$color_number = $this->selectNewUserColor( $thread->root()->originalAuthor() );
		$this->openDiv( "lqt_post lqt_post_color_$color_number" );*/
		$this->openDiv( 'lqt_post' );
		
		if( $this->methodAppliesToThread( 'edit', $thread ) ) {
			$this->showPostEditingForm( $thread );
		} else{
			$this->showPostBody( $post, $oldid );
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
	
	function threadDivClass( $thread ) {
		return 'lqt_thread';
	}

	function showThread( $thread ) {
		global $wgLang; # TODO global.

		$this->showThreadHeading( $thread );
		
		$this->output->addHTML( "<a name=\"lqt_thread_{$thread->id()}\" ></a>" );

		if ($thread->type() == Threads::TYPE_MOVED) {
			$revision = Revision::newFromTitle( $thread->title() );
			$target = Title::newFromRedirect( $revision->getText() );
			$t_thread = Threads::withRoot( new Article( $target ) );
			$p = new Parser(); $sig = $p->getUserSig( $thread->root()->originalAuthor() );

			$this->output->addHTML( "This thread is a placeholder indicating that a thread, <a href=\"{$target->getFullURL()}\">{$target->getText()}</a>, was removed from this page to another talk page. This move was made by " );
			$this->output->addWikitext( $sig, false );
			$this->output->addHTML( " at " );
			$this->output->addHTML( $wgLang->timeanddate($thread->timestamp()) );
			$this->output->addHTML( "." );
			
			return;
		}

		
		if ( $thread->type() == Threads::TYPE_DELETED ) {
			if ( in_array('deletedhistory',  $this->user->getRights()) ) {
				$this->output->addHTML("<p>The following thread has been <b>deleted</b> and is invisible to non-sysops.</p>");
			}
			else {
				$this->output->addHTML("<p><em>This thread was deleted.</em></p>");
				return;
			}
		}

		$timestamp = new Date($thread->timestamp());
		if( $thread->summary() ) {
			$this->showSummary($thread);
		} else if ( $timestamp->isBefore(Date::now()->nDaysAgo($this->archive_start_days))
		            && !$thread->summary() && !$thread->hasSuperthread() && !$thread->isHistorical() ) {
			$this->output->addHTML("<p class=\"lqt_summary_notice\">If this discussion seems to be concluded, you are encouraged to <a href=\"{$this->permalinkUrl($thread, 'summarize')}\">write a summary</a>. There have been no changes here for at least $this->archive_start_days days.</p>");
		}

		$this->openDiv($this->threadDivClass($thread), "lqt_thread_id_{$thread->id()}");
		
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
			<div class='lqt_thread_permalink_summary'>
			<span class="lqt_thread_permalink_summary_title">
			This thread has been summarized as follows:
			</span><span class="lqt_thread_permalink_summary_edit">
			[<a href="{$this->permalinkUrl($t,'summarize')}">edit</a>]
			</span>
HTML
		);
		$this->openDiv('lqt_thread_permalink_summary_body');
		$this->showPostBody($t->summary());
		$this->closeDiv();
		$this->closeDiv();
	}

}

?>
