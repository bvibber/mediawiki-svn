<?php

require_once('Article.php');

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
		$d = clone $this;
		$d->month -= 1;
		return $d;
	}
/*	function monthString() {
		return sprintf( '%04d%02d', $this->year, $this->month );
	}*/
	static function monthString($text) {
		return substr($text, 0, 6);
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
		$d = new DateTime($this->text());
		$d->modify("-$n days");
		return new Date( $d->format('YmdHis') );
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

class Thread {

	/* ID references to other objects that are loaded on demand: */
	protected $rootPostId;
	protected $articleId;
	protected $summaryId;
	protected $superthreadId;

	/* Actual objects loaded on demand from the above when accessors are called: */
	protected $rootPost;
	protected $article;
	protected $summary;
	protected $superthread;

	/* Simple strings: */
	protected $subject;
	protected $touched;
	
	/* Identity */
	protected $id;

	function setSuperthread($thread) {
		$this->superthreadId = $thread->id();
		$this->updateRecord();
	}
	
	function superthread() {
		if ( !$this->superthreadId ) return null;
		if ( !$this->superthread ) $this->superthread = Thread::newFromId($this->superthreadId);
		return $this->superthread;
	}
	
	function topmostThread() {
		if ( !$this->superthread() ) return $this;
		else return $this->superthread()->topmostThread();
	}
	
	function setArticle($a) {
		$this->articleId = $a->getID();
		$this->updateRecord();
	}
	
	function article() {
		if ( !$this->articleId ) return null;
		if ( !$this->article ) $this->article = new Article(Title::newFromID($this->articleId));
		return $this->article;
	}
	
	function id() {
		return $this->id;
	}

	function rootPost() {
		if ( !$this->rootPostId ) return null;
		if ( !$this->rootPost ) $this->rootPost = new Post( Title::newFromID( $this->rootPostId ) );
		return $this->rootPost;
	}
	
	function hasSubject() {
		return $this->subject != null;
	}

	function subject() {
		return $this->subject;
	}
	
	function setSubject($s) {
		$this->subject = $s;
		$this->updateRecord();
	}

	function hasSubthreads() {
		// TODO inefficient.
		return count( $this->subthreads() ) != 0;
	}

	function subthreads() {
		return Thread::threadsWhere( array('thread_subthread_of' => $this->id),
		                             array('ORDER BY' => 'thread_touched') );
	}
	
	function touch() {
		$this->updateRecord(); // TODO side-effect, ugly, etc.
		if ( $this->superthread() ) {
			$this->superthread()->touch();
		}
	}
	
	function touched() {
		return $this->touched;
	}
	
	protected function updateRecord() {
		$dbr =& wfGetDB( DB_MASTER );
        $res = $dbr->update( 'lqt_thread',
                             /* SET */   array( 'thread_root_post' => $this->rootPostId,
                       							'thread_article' => $this->articleId,
												'thread_subthread_of' => $this->superthreadId,
												'thread_summary_page' => $this->summaryId,
												'thread_subject' => $this->subject,
												'thread_touched' => wfTimestampNow() ),
                             /* WHERE */ array( 'thread_id' => $this->id, ),
                             __METHOD__);
	}

	static function newFromDBLine( $line ) {
		$t = new Thread();
		$t->id = $line->thread_id;
		$t->rootPostId = $line->thread_root_post;
		$t->articleId = $line->thread_article;
		$t->summaryId = $line->thread_summary_page;
		$t->superthreadId = $line->thread_subthread_of;
		$t->touched = $line->thread_touched;
		$t->subject = $line->thread_subject;
		return $t;
	}
	
	static function newFromId( $id ) {
		$foo = Thread::threadsWhere( array('thread_id' => $id) );
		return count($foo) > 0 ? $foo[0] : null;
	}

	static function newThread( $root_post, $article ) {
		$dbr =& wfGetDB( DB_MASTER );
		$res = $dbr->insert('lqt_thread',
			array('thread_article' => $article->getID(),
			      'thread_root_post' => $root_post->getID(),
			      'thread_touched' => wfTimestampNow()),
			__METHOD__);
		// TODO we could avoid a query here.
		return Thread::newFromId( $dbr->insertId() );
	}

	/** List of months in which there are >0 threads, suitable for threadsOfArticleInMonth. */
	static function monthsWhereArticleHasThreads( $article ) {
		$threads = Thread::allThreadsOfArticle( $article );
		$months = array();
		foreach( $threads as $t ) {
			$m = substr( $t->touched(), 0, 6 );
			if ( !array_key_exists( $m, $months ) ) {
				if (!in_array( $m, $months )) $months[] = $m;
			}
		}
		return $months;
	}

	static function latestNThreadsOfArticle( $article, $n ) {
		return Thread::threadsWhere( array('thread_article' => $article->getID(),
		                                   'thread_subthread_of is null'),
		                             array('ORDER BY' => 'thread_touched DESC',
		                                   'LIMIT' => $n) );
	}

	static function allThreadsOfArticle( $article ) {
		return Thread::threadsWhere( array('thread_article' => $article->getID(),
		                                   'thread_subthread_of is null'),
		                             array('ORDER BY' => 'thread_touched DESC') );
	}
	
	static function threadsOfArticleInMonth( $article, $yyyymm ) {
		return Thread::threadsWhere( array('thread_article' => $article->getID(),
		                                   'thread_subthread_of is null',
		                                   'thread_touched >= "'.Date::beginningOfMonth($yyyymm).'"',
										   'thread_touched <= "'.Date::endOfMonth($yyyymm).'"'),
		                             array('ORDER BY' => 'thread_touched DESC') );
	}
	
	static function threadsOfArticleInLastNDays( $article, $n ) {
		$startdate = Date::now()->nDaysAgo($n)->midnight();
		return Thread::threadsWhere( array('thread_article' => $article->getID(),
		                                   'thread_subthread_of is null',
											'thread_touched >= ' . $startdate->text() ),
		                             array('ORDER BY' => 'thread_touched DESC' ) );
	}
	
	static function threadsWhoseRootPostIs( $post ) {
		return Thread::threadsWhere( array('thread_root_post' => $post->getID()) );
	}

	static function threadsWhere( $where_clause, $options = array() ) {
		$dbr =& wfGetDB( DB_SLAVE );
		$res = $dbr->select( array('lqt_thread'),
		                     array('*'),
		                     $where_clause,
		                     __METHOD__,
		                     $options);
		$threads = array();
		while ( $line = $dbr->fetchObject($res) ) {
			$threads[] = Thread::newFromDBLine( $line );
		}
		return $threads;	  
	}

	static function walk( $root, $thread_callback, $push_callback, $pop_callback ) {
		call_user_func($thread_callback, $root);
		$s = $root->subthreads(); if ($s) {
			call_user_func($push_callback);
			foreach ($s as $t) {
				Thread::walk($t, $thread_callback, $push_callback, $pop_callback);
			}
			call_user_func($pop_callback);
		}
	}

}

?>
