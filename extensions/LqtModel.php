<?php

/**
* @package MediaWiki
* @subpackage LiquidThreads
* @author David McCabe <davemccabe@gmail.com>
* @licence GPL2
*/

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( -1 );
}

require_once('Article.php');

$wgHooks['TitleGetRestrictions'][] = array('Thread::getRestrictionsForTitle');

// TODO if we're gonna have a Date class we should really do it.
class Date {
	public $year, $month, $day, $hour, $minute, $second;

	// ex. "20070530033751"
	function __construct( $text ) {
		if ( !strlen( $text ) == 14 || !ctype_digit($text) ) {
			$this->isValid = false;
			return null;
		}
		$this->year = intval( substr( $text, 0, 4 ) );
		$this->month = intval( substr( $text, 4, 2 ) );
		$this->day = intval( substr( $text, 6, 2 ) );
		$this->hour = intval( substr( $text, 8, 2 ) );
		$this->minute = intval( substr( $text, 10, 2 ) );
		$this->second = intval( substr( $text, 12, 2 ) );
	}
	function lastMonth() {
		return $this->moved('-1 month');
	}
	function nextMonth() {
		return $this->moved('+1 month');
	}
	function moved($str) {
	  return new Date( date('YmdHis', strtotime($this->text() . ' ' . $str)) );
	}
	/*	function monthString() {
		return sprintf( '%04d%02d', $this->year, $this->month );
	}
	*/
	static function monthString($text) {
		return substr($text, 0, 6);
	}

	function delta( $o ) {
		$t = clone $this;
		$els = array('year', 'month', 'day', 'hour', 'minute', 'second');
		$deltas = array();
		foreach ($els as $e) {$deltas[$e] = $t->$e - $o->$e;
			$t->$e += $t->$e - $o->$e;
		}

		// format in style of date().
		$result = "";
		foreach( $deltas as $name => $val ) {
			$result .= "$val $name ";
		}
		return $result;
	}
	static function beginningOfMonth($yyyymm) { return $yyyymm . '00000000'; }
	static function endOfMonth($yyyymm) { return $yyyymm . '31235959'; }
	function text() {
		return sprintf( '%04d%02d%02d%02d%02d%02d', $this->year, $this->month, $this->day,
			$this->hour, $this->minute, $this->second );
	}
	static function now() {
		return new Date(wfTimestampNow());
	}
	function nDaysAgo($n) {
		return $this->moved("-$n days");
	}
	function midnight() {
		$d = clone $this;
		$d->hour = $d->minute = $d->second = 0;
		return $d;
	}
	function isBefore($d) {
		foreach(array('year', 'month', 'day', 'hour', 'minute', 'second') as $part) {
			if ( $this->$part < $d->$part ) return true;
			if ( $this->$part > $d->$part ) return false;
		}
		return true; // exactly the same time; arguable.
	}
}


// TODO get rid of this class. sheesh.
class Post extends Article {
	/**
	* Return the User object representing the author of the first revision
	* (or null, if the database is screwed up).
	*/
	function originalAuthor() {
		$dbr =& wfGetDB( DB_SLAVE );

		$line = $dbr->selectRow( array('revision', 'page'), 'rev_user_text',
			array('rev_page = page_id',
			'page_id' => $this->getID()),
			__METHOD__,
			array('ORDER BY'=> 'rev_timestamp',
			'LIMIT'   => '1') );
		if ( $line )
			return User::newFromName($line->rev_user_text, false);
		else
			return null;
	}
}


class ThreadHistoryIterator extends ArrayIterator {
	
	function __construct($thread, $limit, $offset) {
		$this->thread = $thread;
		$this->limit = $limit;
		$this->offset = $offset;
		$this->loadRows();
	}
	
	private function loadRows() {
		if( $this->offset == 0 ) {
			$this->append( $this->thread );
			$this->limit -= 1;
		} else {
			$this->offset -= 1;
		}
		
		$dbr =& wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			'historical_thread',
			'hthread_contents, hthread_revision',
			array('hthread_id' => $this->thread->id()),
			__METHOD__,
			array('ORDER BY' => 'hthread_revision DESC',
			      'LIMIT' =>  $this->limit,
			      'OFFSET' => $this->offset));
		while($l = $dbr->fetchObject($res)) {
			$this->append( HistoricalThread::fromTextRepresentation($l->hthread_contents) );
		}
	}
}


class HistoricalThread extends Thread {
	function __construct($t) {
		/* SCHEMA changes must be reflected here. */
		$this->rootId = $t->rootId;
		$this->rootRevision = $t->rootRevision;
		$this->articleId = $t->articleId;
		$this->summaryId = $t->summaryId;
		$this->articleNamespace = $t->articleNamespace;
		$this->articleTitle = $t->articleTitle;
		$this->timestamp = $t->timestamp;
		$this->path = $t->path;
		$this->id = $t->id;
		$this->revisionNumber = $t->revisionNumber;
		$this->changeType = $t->changeType;
		$this->changeObject = $t->changeObject;
		$this->changeComment = $t->changeComment;
		$this->changeUser = $t->changeUser;
		$this->changeUserText = $t->changeUserText;
		
		$this->replies = array();
		foreach ($t->replies as $r) {
			$this->replies[] = new HistoricalThread($r);
		}
	}
	static function textRepresentation($t) {
		$ht = new HistoricalThread($t);
		return serialize($ht);
	}
	static function fromTextRepresentation($r) {
		return unserialize($r);
	}
	static function create( $t, $change_type, $change_object ) {
		$tmt = $t->topmostThread();
		$contents = HistoricalThread::textRepresentation($tmt);
		$dbr =& wfGetDB( DB_MASTER );
		$res = $dbr->insert( 'historical_thread', array(
			'hthread_id'=>$tmt->id(),
			'hthread_revision'=>$tmt->revisionNumber(),
			'hthread_contents'=>$contents,
			'hthread_change_type'=>$tmt->changeType(),
			'hthread_change_object'=>$tmt->changeObject() ? $tmt->changeObject()->id() : null),
			__METHOD__ );
	}
	static function withIdAtRevision( $id, $rev ) {
		$dbr =& wfGetDB( DB_SLAVE );
		$line = $dbr->selectRow(
			'historical_thread',
			'hthread_contents',
			array('hthread_id' => $id, 'hthread_revision' => $rev),
			__METHOD__);
		if ( $line )
			return HistoricalThread::fromTextRepresentation($line->hthread_contents);
		else
			return null;
	}
	function isHistorical() {
		return true;
	}
}


class Thread {
	/* SCHEMA changes must be reflected here. */
	
	/* ID references to other objects that are loaded on demand: */
	protected $rootId;
	protected $articleId;
	protected $summaryId;
	
	/* These are only used in the case of a non-existant article. */
	protected $articleNamespace;
	protected $articleTitle;

	/* Actual objects loaded on demand from the above when accessors are called: */
	protected $root;
	protected $article;
	protected $summary;
	protected $superthread;

	/* Simple strings: */
	protected $timestamp;
	protected $path;
	
	protected $id;
	protected $revisionNumber;
	protected $type;
	
	protected $changeType;
	protected $changeObject;
	protected $changeComment;
	protected $changeUser;
	protected $changeUserText;
	
	/* Only used by $double to be saved into a historical thread. */
	protected $rootRevision;
	
	/* Copy of $this made when first loaded from database, to store the data
	   we will write to the history if a new revision is commited. */
	protected $double;
	
	protected $replies;
	
	function isHistorical() {
		return false;
	}
	
	function revisionNumber() {
		return $this->revisionNumber;
	}
	
	function atRevision($r) {
		if ( $r == $this->revisionNumber() )
			return $this;
		else
			return HistoricalThread::withIdAtRevision($this->id(), $r);
	}
	
	function historicalRevisions() {
		$dbr =& wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			'historical_thread',
			'hthread_contents',
			array('hthread_id' => $this->id()),
			__METHOD__);
		$results = array();
		while($l = $dbr->fetchObject($res)) {
			$results[] = HistoricalThread::fromTextRepresentation($l->hthread_contents);
		}
		return $results;
	}
	
	function ancestors() {
 		$id_clauses = array();
		foreach( explode('.', $this->path) as $id ) {
			$id_clauses[] = "thread_id = $id";
		}
		$where = implode(' OR ', $id_clauses);
		return Threads::where($where);
	}
	
	private function bumpRevisionsOnAncestors($change_type, $change_object) {
		$this->revisionNumber += 1;	
		$this->setChangeType($change_type);
		$this->setChangeObject($change_object);
		
		if( $this->hasSuperthread() )
			$this->superthread()->bumpRevisionsOnAncestors($change_type, $change_object);
		$dbr =& wfGetDB( DB_MASTER );
		$res = $dbr->update( 'thread',
		     /* SET */ array('thread_revision' => $this->revisionNumber,
		                     'thread_change_type'=>$this->changeType,
		                     'thread_change_object'=>$this->changeObject),
		     /* WHERE */ array( 'thread_id' => $this->id ),
		     __METHOD__);
	}
	
	private static function setChangeOnDescendents($thread, $change_type, $change_object) {
		// TODO this is ludicrously inefficient.
		$thread->setChangeType($change_type);
		$thread->setChangeObject($change_object);
		$dbr =& wfGetDB( DB_MASTER );
		$res = $dbr->update( 'thread',
		     /* SET */ array('thread_revision' => $thread->revisionNumber,
		                     'thread_change_type'=>$thread->changeType,
		                     'thread_change_object'=>$thread->changeObject),
		     /* WHERE */ array( 'thread_id' => $thread->id ),
		     __METHOD__);
		foreach($thread->replies() as $r)
			self::setChangeOnDescendents($r, $change_type, $change_object);
		return $thread;
	}
	
	function commitRevision($change_type, $change_object = null, $reason = "") {
		global $wgUser; // TODO global.
		
		$this->changeComment = $reason;
		$this->changeUser = $wgUser->getID();
		$this->changeUserText = $wgUser->getName();
		
		// TODO open a transaction.
		HistoricalThread::create( $this->double, $change_type, $change_object );

		$this->bumpRevisionsOnAncestors($change_type, $change_object);
		self::setChangeOnDescendents($this->topmostThread(), $change_type, $change_object);
		
		/* SCHEMA changes must be reflected here. */
		
		$dbr =& wfGetDB( DB_MASTER );
		$res = $dbr->update( 'thread',
		     /* SET */array( 'thread_root' => $this->rootId,
					'thread_article' => $this->articleId,
					'thread_path' => $this->path,
					'thread_type' => $this->type,
					'thread_summary_page' => $this->summaryId,
					'thread_timestamp' => wfTimestampNow(),
					'thread_revision' => $this->revisionNumber,
					'thread_article_namespace' => $this->articleNamespace,
				    'thread_article_title' => $this->articleTitle,
					'thread_change_type' => $this->changeType,
					'thread_change_object' => $this->changeObject,
					'thread_change_comment' => $this->changeComment,
					'thread_change_user' => $this->changeUser,
					'thread_change_user_text' => $this->changeUserText,
					),
		     /* WHERE */ array( 'thread_id' => $this->id, ),
		     __METHOD__);
	
	//	RecentChange::notifyEdit( wfTimestampNow(), $this->root(), /*minor*/false, $wgUser, $summary,
	//		$lastRevision, $this->getTimestamp(), $bot, '', $oldsize, $newsize,
	//		$revisionId );
	}
	
	function delete($reason) {
		$this->type = Threads::TYPE_DELETED;
		$this->revisionNumber += 1;
		$this->commitRevision(Threads::CHANGE_DELETED, $this, $reason);
	}
	function undelete($reason) {
		$this->type = Threads::TYPE_NORMAL;
		$this->revisionNumber += 1;
		$this->commitRevision(Threads::CHANGE_UNDELETED, $this, $reason);
	}
	
	function moveToSubjectPage($title, $reason, $leave_trace) {
		$dbr =& wfGetDB( DB_MASTER );
		
		if( $title->exists() ) {
			$new_articleId = $title->getArticleId();
			$new_articleNamespace = null;
			$new_articleTitle = null;
		} else {
			$new_articleId = 0;
			$new_articleNamespace = $title->getNamespace();
			$new_articleTitle = $title->getDBKey();
		}
		
		foreach($this->replies as $r) {
			$res = $dbr->update( 'thread',
			     /* SET */array(
						'thread_revision' => $r->revisionNumber() + 1,
						'thread_article' => $new_articleId,
						'thread_article_namespace' => $new_articleNamespace,
					    'thread_article_title' => $new_articleTitle),
			     /* WHERE */ array( 'thread_id' => $r->id(), ),
			     __METHOD__);
		}
		
		$this->articleId = $new_articleId;
		$this->articleNamespace = $new_articleNamespace;
		$this->articleTitle = $new_articleTitle;
		$this->revisionNumber += 1;
		$this->commitRevision();
		
		if($leave_trace) {
			$this->leaveTrace($reason);
		}
	}
	
	function leaveTrace($reason) {
		/* Adapted from Title::moveToNewTitle. But now the new title exists on the old talkpage. */
		$dbw =& wfGetDB( DB_MASTER );
		
		$mwRedir = MagicWord::get( 'redirect' );
		$redirectText = $mwRedir->getSynonym( 0 ) . ' [[' . $this->title()->getPrefixedText() . "]]\n";
		$redirectArticle = new Article( LqtView::incrementedTitle( $this->subjectWithoutIncrement(),
		                                                           NS_LQT_THREAD) ); ## TODO move to model.
		$newid = $redirectArticle->insertOn( $dbw );
		$redirectRevision = new Revision( array(
			'page'    => $newid,
			'comment' => $reason,
			'text'    => $redirectText ) );
		$redirectRevision->insertOn( $dbw );
		$redirectArticle->updateRevisionOn( $dbw, $redirectRevision, 0 );

		# Log the move
		$log = new LogPage( 'move' );
		$log->addEntry( 'move', $this->double->title(), $reason, array( 1 => $this->title()->getPrefixedText()) );

		# Purge caches as per article creation
		Article::onArticleCreate( $redirectArticle->getTitle() );

		# Record the just-created redirect's linking to the page
		$dbw->insert( 'pagelinks',
			array(
				'pl_from'      => $newid,
				'pl_namespace' => $redirectArticle->getTitle()->getNamespace(),
				'pl_title'     => $redirectArticle->getTitle()->getDBkey() ),
			__METHOD__ );

		$thread = Threads::newThread( $redirectArticle, $this->double->article(), null,
		 	Threads::TYPE_MOVED, $log);

		# Purge old title from squid
		# The new title, and links to the new title, are purged in Article::onArticleCreate()
#		$this-->purgeSquid();
	}
	
	

	function __construct($line, $children) {
		/* SCHEMA changes must be reflected here. */
		
		$this->id = $line->thread_id;
		$this->rootId = $line->thread_root;
		$this->articleId = $line->thread_article;
		$this->articleNamespace = $line->thread_article_namespace;
		$this->articleTitle = $line->thread_article_title;
		$this->summaryId = $line->thread_summary_page;
		$this->path = $line->thread_path;
		$this->timestamp = $line->thread_timestamp;
		$this->revisionNumber = $line->thread_revision;
		$this->type = $line->thread_type;
		$this->changeType = $line->thread_change_type;
		$this->changeObject = $line->thread_change_object;
		$this->changeComment = $line->thread_change_comment;
		$this->changeUser = $line->thread_change_user;
		$this->changeUserText = $line->thread_change_user_text;

		$root_title = Title::makeTitle( $line->page_namespace, $line->page_title );
		$this->root = new Post($root_title);
		$this->root->loadPageData($line);
		$this->rootRevision = $this->root->mLatest;
	}
	
	function initWithReplies( $children ) {
		
		$this->replies = $children;
		
		$this->double = clone $this;
	}
	
	function __clone() {
		// Cloning does not normally create a new array (but the clone keyword doesn't
		// work on arrays -- go figure).
		
		// Update: this doesn't work for some reason, but why do we update the replies array
		// in the first place after creating a new reply?
		$new_array = array();
		foreach( $this->replies as $r )
			$new_array[] = $r;
		$this->replies = $new_array;
	}

	/*
	More evidence that the way I'm doing history is totally screwed.
	These methods do not alter the childrens' superthread field. All they do
	is make sure the latest info gets into any historicalthreads we commit.
	 */
	function addReply($thread) {
		// TODO: question for myself to ponder: We don't want the latest info in the
		// historical thread, duh. Why were we doing this?
//		$this->replies[] = $thread;
	}
	function removeReplyWithId($id) {
		$target = null;
		foreach($this->replies as $k=>$r) {
			if ($r->id() == $id) {
				$target = $k; break;
			}
		}
		if ($target) {
			unset($this->replies[$target]);
			return true;
		} else {
			return false;
		}
	}
	function replies() {
		return $this->replies;
	}
	
	function setSuperthread($thread) {
		$this->path = $thread->path . '.' . $this->id;
	}

	function superthread() {
		if( !$this->hasSuperthread() ) {
			return null;
		} else {
			preg_match("/(\d+)\.\d+$/", $this->path, $matches);
			$superthread_id = $matches[1];
			return Threads::withId( $superthread_id );
		}
	}

	function hasSuperthread() {
		if( false === strpos($this->path,'.') ) return false;
		else return true;
	}

	function topmostThread() {
		if( !$this->hasSuperthread() ) {
			return $this;
		} else {
			preg_match("/^(\d+)\..*/", $this->path, $matches);
			$superthread_id = $matches[1];
			return Threads::withId( $superthread_id );
		}
	}
	
	function setArticle($a) {
		$this->articleId = $a->getID();
		$this->articleNamespace = $a->getTitle()->getNamespace();
		$this->articleTitle = $a->getTitle()->getDBkey();
		$this->touch();
	}

	function article() {
		if ( $this->article ) return $this->article;
		$a = new Article(Title::newFromID($this->articleId));
		if ($a->exists()) {
			return $a;
		} else {
			return new Article( Title::makeTitle($this->articleNamespace, $this->articleTitle) );
		}
	}

	function id() {
		return $this->id;
	}
	
	function path() {
		return $this->path;
	}

	function root() {
		if ( !$this->rootId ) return null;
		if ( !$this->root ) $this->root = new Post( Title::newFromID( $this->rootId ),
		                                            $this->rootRevision() );
		return $this->root;
	}
	
	function setRootRevision($rr) {
		if( (is_object($rr)) ) {
			$this->rootRevision = $rr->getId();
		} else if (is_int($rr)) {
			$this->rootRevision = $rr;
		}
	}
	
	function rootRevision() {
		return $this->rootRevision;
	}
	
	function summary() {
		if ( !$this->summaryId ) return null;
		if ( !$this->summary ) $this->summary = new Post( Title::newFromID( $this->summaryId ) );
		return $this->summary;
	}
	
	function hasSummary() {
		return $this->summaryId != null;
	}
	
	function setSummary( $post ) {
		$this->summary = null;
		$this->summaryId = $post->getID();
	}
	
	function title() {
		return $this->root()->getTitle();
	}

	private function splitIncrementFromSubject($subject_string) {
		preg_match('/^(.*) \((\d+)\)$/', $subject_string, $matches);
		if( count($matches) != 3 )
			throw new MWException( __METHOD__ . ": thread subject has no increment: " . $subject_string );
		else
			return $matches;
	}
	
	function wikilink() {
		return $this->root()->getTitle()->getPrefixedText();
	}
	
	function subject() {
		return $this->root()->getTitle()->getText();
	}
	
	function wikilinkWithoutIncrement() {
		$tmp = $this->splitIncrementFromSubject($this->wikilink()); return $tmp[1];
	}

	function subjectWithoutIncrement() {
		$tmp = $this->splitIncrementFromSubject($this->subject()); return $tmp[1];
	}
	
	function increment() {
		$tmp = $this->splitIncrementFromSubject($this->subject()); return $tmp[2];
	}
	
	function hasDistinctSubject() {
		if( $this->hasSuperthread() ) {
			return $this->superthread()->subjectWithoutIncrement()
				!= $this->subjectWithoutIncrement();
		} else {
			return true;
		}
	}
	
	function hasSubthreads() {
		return count($replies) != 0;
	}

	function subthreads() {
		return $this->replies;
	}

	function timestamp() {
		return $this->timestamp;
	}
	
	function type() {
		return $this->type;
	}
	
	function changeType() {
		return $this->changeType;
	}
	
	private function replyWithId($id) {
		if( $this->id == $id ) return $this;
		foreach ( $this->replies as $r ) {
			if( $r->id() == $id ) return $r;
			else {
				$s = $r->replyWithId($id);
				if( $s ) return $s;
			}
		}
		return null;
	}
	function changeObject() {
		return $this->replyWithId( $this->changeObject );
	}
	
	function setChangeType($t) {
		if (in_array($t, Threads::$VALID_CHANGE_TYPES)) {
			$this->changeType = $t;
		} else {
			throw new MWException( __METHOD__ . ": invalid changeType $t." );
		}
	}
	
	function setChangeObject($o) {
		# we assume $o to be a Thread.
		if($o === null) {
			$this->changeObject = null;
		} else {
			$this->changeObject = $o->id();
		}
	}
	
	function changeUser() {
		if( $this->changeUser == 0 ) {
			return User::newFromName($this->changeUserText, false);
		} else {
			return User::newFromId($this->changeUser);
		}
	}
	
	function changeComment() {
		return $this->changeComment;
	}
	
	// Called from hook in Title::isProtected.
	static function getRestrictionsForTitle($title, $action, &$result) {
		$thread = Threads::withRoot(new Post($title));
		if ($thread)
			return $thread->getRestrictions($action, $result);
		else
			return true; // not a thread; do normal protection check.
	}
	
	// This only makes sense when called from the hook, because it uses the hook's
	// default behavior to check whether this thread itself is protected, so you'll
	// get false negatives if you use it from some other context.
	function getRestrictions($action, &$result) {
		if( $this->hasSuperthread() ) {
			$parent_restrictions = $this->superthread()->root()->getTitle()->getRestrictions($action);
		} else {
			$parent_restrictions = $this->article()->getTitle()->getTalkPage()->getRestrictions($action);
		}
		
		// TODO this may not be the same as asking "are the parent restrictions more restrictive than
		// our own restrictions?", which is what we really want.
		if( count($parent_restrictions) == 0 ) {
			return true; // go to normal protection check.
		} else {
			$result = $parent_restrictions;
			return false;
		}
			
	}
}


/** Module of factory methods. */
class Threads {

	const TYPE_NORMAL = 0;
	const TYPE_MOVED = 1;
	const TYPE_DELETED = 2;
	static $VALID_TYPES = array(self::TYPE_NORMAL, self::TYPE_MOVED, self::TYPE_DELETED);
	
	const CHANGE_NEW_THREAD = 0;
	const CHANGE_REPLY_CREATED = 1;
	const CHANGE_EDITED_ROOT = 2;
	const CHANGE_EDITED_SUMMARY = 3;
	const CHANGE_DELETED = 4;
	const CHANGE_UNDELETED = 5;
	static $VALID_CHANGE_TYPES = array(self::CHANGE_EDITED_SUMMARY, self::CHANGE_EDITED_ROOT,
		self::CHANGE_REPLY_CREATED, self::CHANGE_NEW_THREAD, self::CHANGE_DELETED, self::CHANGE_UNDELETED);

	static $cache_by_root = array();
	static $cache_by_id = array();
	
	static $thread_children = array();
	
    static function newThread( $root, $article, $superthread = null, $type = self::TYPE_NORMAL ) {

		// SCHEMA changes must be reflected here.
		// TODO: It's dumb that the commitRevision code isn't used here.

        $dbr =& wfGetDB( DB_MASTER );
		
		if ( !in_array($type, self::$VALID_TYPES) ) {
			throw new MWException(__METHOD__ . ": invalid type $type.");
		}
		
		if( $article->exists() ) {
			$aclause = array("thread_article" => $article->getID());
		} else {
			$aclause = array("thread_article_namespace" => $article->getTitle()->getNamespace(),
						     "thread_article_title" => $article->getTitle()->getDBkey());
		}
		
		if ($superthread) {
			$change_type = self::CHANGE_REPLY_CREATED;
		} else {
			$change_type = self::CHANGE_NEW_THREAD;
		}
		
		global $wgUser; // TODO global.

        $res = $dbr->insert('thread',
            array('thread_root' => $root->getID(),
                  'thread_timestamp' => wfTimestampNow(),
				  'thread_change_type' => $change_type,
				  'thread_change_comment' => "", // TODO
				  'thread_change_user' => $wgUser->getID(),
				  'thread_change_user_text' => $wgUser->getName(),
				  'thread_type' => $type) + $aclause,
            __METHOD__);
		
		$newid = $dbr->insertId();
		
		if( $superthread ) {
			$newpath = $superthread->path() . '.' . $newid;
			$change_object_clause = 'thread_change_object = ' . $newid;
		} else {
			$newpath = $newid;
			$change_object_clause = 'thread_change_object = null';
		}
		$res = $dbr->update( 'thread',
			     /* SET */   array( 'thread_path' => $newpath,
			                         $change_object_clause ),
			     /* WHERE */ array( 'thread_id' => $newid, ),
			     __METHOD__);
		
		// TODO we could avoid a query here.
        $newthread =  Threads::withId($newid);
		if($superthread) {
			$superthread->addReply( $newthread );
		}
		return $newthread;
     }
	
	static function where( $where, $options = array(), $extra_tables = array(), $joins = "" ) {
		$dbr = wfGetDB( DB_SLAVE );
		if ( is_array($where) ) $where = $dbr->makeList( $where, LIST_AND );
		if ( is_array($options) ) $options = implode(',', $options);
		
		if( is_array($extra_tables) && count($extra_tables) != 0 ) {
			$tables = implode(',', $extra_tables) . ', ';
		} else if ( is_string( $extra_tables ) ) {
			$tables = $extra_tables . ', ';
		} else {
			$tables = "";
		}
				
		/* Select the client's threads, AND all their children.
		  The ones the client actually asked for are marked with root_test.
		  In theory we could also grab the page and revision data, to avoid having
		  to do an additional query for each page, but Article's prodedure for grabbing
		  its own data is complicated and it's just not my problem. Plus parser cache.
		*/

		$root_test = str_replace( 'thread.', 'children.', $where ); // TODO fragile?

		$sql = <<< SQL
SELECT children.*, child_page.*, ($root_test) as is_root FROM $tables thread, thread children, page child_page $joins
WHERE $where
AND children.thread_path LIKE CONCAT(thread.thread_path, "%")
AND child_page.page_id = children.thread_root
$options
SQL;

		$res = $dbr->query($sql); 

		$threads = array();
		$top_level_threads = array();

		while ( $line = $dbr->fetchObject($res) ) {
			$new_thread = new Thread($line, null);
			$threads[] = $new_thread;
			if( $line->is_root )
				// thread is one of those that was directly queried for.
				$top_level_threads[] = $new_thread;
			if( strstr( $line->thread_path, '.' ) !== false ) {
				// thread has a parent. extract second-to-last path element.
				preg_match( '/([^.]+)\.[^.]+$/', $line->thread_path, $path_matches );
				$parent_id = $path_matches[1];
				if( !array_key_exists( $parent_id, self::$thread_children ) )
					self::$thread_children[$parent_id] = array();
				self::$thread_children[$parent_id][] = $new_thread;
			}
		}

		foreach( $threads as $thread ) {
			if( array_key_exists( $thread->id(), self::$thread_children ) ) {
				$thread->initWithReplies( self::$thread_children[$thread->id()] );
			} else {
				$thread->initWithReplies( array() );
			}
			
			self::$cache_by_root[$thread->root()->getID()] = $thread;
			self::$cache_by_id[$thread->id()] = $thread;
		}
		return $top_level_threads;
	}

	private static function databaseError( $msg ) {
		// TODO tie into MW's error reporting facilities.
		echo("Corrupt liquidthreads database: $msg");
		die();
	}

	static function withRoot( $post ) {
		if( $post->getTitle()->getNamespace() != NS_LQT_THREAD ) {
			// No articles outside the thread namespace have threads associated with them;
			// avoiding the query saves time during the TitleGetRestrictions hook.
			return null;
		}
		if( array_key_exists( $post->getID(), self::$cache_by_root ) ) {
			return self::$cache_by_root[$post->getID()];
		}
		$ts = Threads::where( array('thread.thread_root' => $post->getID()) );
		if( count($ts) == 0 ) { return null; }
		if ( count($ts) >1 ) {
			Threads::databaseError("More than one thread with thread_root = {$post->getID()}.");
		}
		return $ts[0];
	}

	static function withId( $id ) {
		if( array_key_exists( $id, self::$cache_by_id ) ) {
			return self::$cache_by_id[$id];
		}
			
		$ts = Threads::where( array('thread.thread_id' => $id ) );
		if( count($ts) == 0 ) { return null; }
		if ( count($ts) >1 ) {
			Threads::databaseError("More than one thread with thread_id = {$id}.");
		}
		return $ts[0];
	}
	
	/**
	* Horrible, horrible!
	* List of months in which there are >0 threads, suitable for threadsOfArticleInMonth. */
	static function monthsWhereArticleHasThreads( $article ) {
		$threads = Threads::where( array('thread.thread_article' => $article->getID()) );
		$months = array();
		foreach( $threads as $t ) {
			$m = substr( $t->timestamp(), 0, 6 );
			if ( !array_key_exists( $m, $months ) ) {
				if (!in_array( $m, $months )) $months[] = $m;
			}
		}
		return $months;
	}
	
	static function articleClause($article) {
		return <<<SQL
		IF(thread.thread_article = 0,
				thread.thread_article_title = "{$article->getTitle()->getDBkey()}"
				AND thread.thread_article_namespace = {$article->getTitle()->getNamespace()}
			, thread.thread_article = {$article->getID()})
SQL;
	}
	
	static function topLevelClause() {
		return 'instr(thread.thread_path, ".") = 0';
	}

}


class QueryGroup {
	protected $queries;
	
	function __construct() {
		$this->queries = array();
	}
	
	function addQuery( $name, $where, $options = array(), $extra_tables = array() ) {
		$this->queries[$name] = array($where, $options, $extra_tables);
	}
	
	function extendQuery( $original, $newname, $where, $options = array(), $extra_tables=array() ) {
		if (!array_key_exists($original,$this->queries)) return;
		$q = $this->queries[$original];
		$this->queries[$newname] = array( array_merge($q[0], $where),
						  array_merge($q[1], $options),
						  array_merge($q[2], $extra_tables) );
	}
	
	function deleteQuery( $name ) {
		unset ($this->queries[$name]);
	}
	
	function query($name) {
		if ( !array_key_exists($name,$this->queries) ) return array();
		list($where, $options, $extra_tables) = $this->queries[$name];
		return Threads::where($where, $options, $extra_tables);
	}
}


class NewMessages {
	
	static function markThreadAsUnreadByUser($thread, $user) {
		self::writeUserMessageState($thread, $user, null);
	}
	
	static function markThreadAsReadByUser($thread, $user) {
		self::writeUserMessageState($thread, $user, wfTimestampNow());		
	}
	
	private static function writeUserMessageState($thread, $user, $timestamp) {
		if( is_object($thread) ) $thread_id = $thread->id();
		else if( is_integer($thread) ) $thread_id = $thread;
		else throw new MWException("writeUserMessageState expected Thread or integer but got $thread");
		
		if( is_object($user) ) $user_id = $user->getID();
		else if( is_integer($user) ) $user_id = $user;
		else throw new MWException("writeUserMessageState expected User or integer but got $user");
		
		if ( $timestamp === null ) $timestamp = "NULL";
		
		// use query() directly to pass in 'true' for don't-die-on-errors.
		$dbr =& wfGetDB( DB_MASTER );
        $success = $dbr->query("insert into user_message_state values ($user_id, $thread_id, $timestamp)",
            __METHOD__, true);

		if( !$success ) {
			// duplicate key; update.
			$dbr->query("update user_message_state set ums_read_timestamp = $timestamp" .
						" where ums_thread = $thread_id and ums_user = $user_id",
	            		__METHOD__);
		}
	}
	
	static function newUserMessages($user) {

		$ts = Threads::where( array('ums_read_timestamp is null',
									Threads::articleClause(new Article($user->getUserPage()))),
							 array(), array(), "left outer join user_message_state on ums_user is null or (ums_user = {$user->getID()} and ums_thread = thread.thread_id)" );
		return $ts;
	}
	

	
}

?>
