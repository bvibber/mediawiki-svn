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

class HistoricalThread extends Thread {
	function __construct($t) {
		$this->rootId = $t->rootId;
		$this->articleId = $t->articleId;
		$this->summaryId = $t->summaryId;
		$this->articleNamespace = $t->articleNamespace;
		$this->articleTitle = $t->articleTitle;
		$this->timestamp = $t->timestamp;
		$this->path = $t->path;
		$this->id = $t->id;
		$this->revisionNumber = $t->revisionNumber;
		
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
	static function create( $t ) {
		$tmt = $t->topmostThread();
		$contents = HistoricalThread::textRepresentation($tmt);
		$dbr =& wfGetDB( DB_MASTER );
		$res = $dbr->insert( 'historical_thread', array(
			'hthread_id'=>$tmt->id(),
			'hthread_revision'=>$tmt->revisionNumber(),
			'hthread_contents'=>$contents), __METHOD__ );
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
}

class Thread {
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
	protected $subject;
	protected $timestamp;
	protected $path;
	
	protected $id;
	protected $revisionNumber;
	
	/* Only used by $double to be saved into a historical thread. */
	protected $rootRevision;
	
	/* Copy of $this made when first loaded from database, to store the data
	   we will write to the history if a new revision is commited. */
	protected $double;
	
	protected $replies;
	
	function revisionNumber() {
		return $this->revisionNumber;
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
	
	function commitRevision() {
		// TODO open a transaction.
		HistoricalThread::create( $this->double );

		$this->revisionNumber += 1;

		$dbr =& wfGetDB( DB_MASTER );
		$res = $dbr->update( 'thread',
		     /* SET */array( 'thread_root' => $this->rootId,
					'thread_article' => $this->articleId,
					'thread_path' => $this->path,
					'thread_summary_page' => $this->summaryId,
					'thread_timestamp' => $this->timestamp,
					'thread_revision' => $this->revisionNumber,
					'thread_article_namespace' => $this->articleNamespace,
				    'thread_article_title' => $this->articleTitle),
		     /* WHERE */ array( 'thread_id' => $this->id, ),
		     __METHOD__);
	}

	function __construct($line, $children) {
		$this->id = $line->thread_id;
		$this->rootId = $line->thread_root;
		$this->articleId = $line->thread_article;
		$this->articleNamespace = $line->thread_article_namespace;
		$this->articleTitle = $line->thread_article_title;
		$this->summaryId = $line->thread_summary_page;
		$this->path = $line->thread_path;
		$this->timestamp = $line->thread_timestamp;
		$this->revisionNumber = $line->thread_revision;
		$this->replies = $children;
		
		$this->double = clone $this;
		
		/*
		Root revision is ignored on live threads but will be important when
		when we save a historical thread, since by that time an edit will
		have already taken place and it will be more difficult to find
		the pre-edit revision number. But I hate this.
		
		(we could do Revision::getPrevious() we just need to know whether or not
		there was a new revision saved at save time. make it run then make it right.)
		*/
		$rev = Revision::newFromTitle( $this->root()->getTitle() );
		$this->double->rootRevision = $rev->getId();
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
		if ( !$this->root ) $this->root = new Post( Title::newFromID( $this->rootId ) );
		return $this->root;
	}
	
	function setRootRevision($rr) {
		if( (is_object($rr)) ) {
			$this->rootRevision = $rr->getId();
		} else if (is_int($rr)) {
			$this->rootRevision = $rr;
		}
	}
	
	function summary() {
		if ( !$this->summaryId ) return null;
		if ( !$this->summary ) $this->summary = new Post( Title::newFromID( $this->summaryId ) );
		return $this->summary;
	}
	
	function setSummary( $post ) {
		$this->summary = null;
		$this->summaryId = $post->getID();
	}
	
	function wikilink() {
		return $this->root()->getTitle()->getPrefixedText();
	}
	
	function wikilinkWithoutIncrement() {
		$foo = explode( ' ', $this->wikilink() );
		array_pop($foo);
		return implode( ' ', $foo );
	}
	
	function hasDistinctSubject() {
		if( $this->hasSuperthread() ) {
			return $this->superthread()->subjectWithoutIncrement()
				!= $this->subjectWithoutIncrement();
		} else {
			return true;
		}
	}

	function subject() {
		return $this->root()->getTitle()->getText();
		return $this->subject;
	}
	
	function subjectWithoutIncrement() {
		$foo = explode( ' ', $this->subject() );
		array_pop($foo);
		return implode( ' ', $foo );
	}
	
	function increment() {
		return array_pop( explode(' ', $this->subject()) );
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
}

/** Module of factory methods. */
class Threads {

	static $loadedThreads = array();
	
    static function newThread( $root, $article, $superthread = null ) {
        $dbr =& wfGetDB( DB_MASTER );
			
		if( $article->exists() ) {
			$aclause = array("thread_article" => $article->getID());
		} else {
			$aclause = array("thread_article_namespace" => $article->getTitle()->getNamespace(),
						     "thread_article_title" => $article->getTitle()->getDBkey());
		}

        $res = $dbr->insert('thread',
            array('thread_root' => $root->getID(),
                  'thread_timestamp' => wfTimestampNow()) + $aclause,
            __METHOD__);
		
		$newid = $dbr->insertId();
		
		if( $superthread ) {
			$newpath = $superthread->path() . '.' . $newid;
		} else
		{
			$newpath = $newid;
		}
		$res = $dbr->update( 'thread',
			     /* SET */   array( 'thread_path' => $newpath ),
			     /* WHERE */ array( 'thread_id' => $newid, ),
			     __METHOD__);
		
		// TODO we could avoid a query here.
        return Threads::withId($newid);
     }
	
	static function where( $where, $options = array(), $extra_tables = array() ) {
		$dbr =& wfGetDB( DB_SLAVE );
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
SELECT children.*, ($root_test) as is_root FROM $tables thread, thread children
WHERE $where AND
children.thread_path LIKE CONCAT(thread.thread_path, "%")
$options
SQL;
		$res = $dbr->query($sql); 

		$lines = array();
		$threads = array();

		while ( $line = $dbr->fetchObject($res) ) {
			$lines[] = $line;
		}

		foreach( $lines as $key => $l ) {
			if( $l->is_root ) {
//				unset($lines[$key]);
				$threads[] = Threads::buildThread( &$lines, $l );
			}
		}
		return $threads;
	}

	private static function buildThread( $lines, $l ) {
		$children = array();
		$l_path = preg_quote($l->thread_path);
		foreach( $lines as $key => $m ) {
			if ( preg_match( "/^{$l_path}\.\d+$/", $m->thread_path ) ) {
//				unset($lines[$key]);
				$children[] = Threads::buildThread( &$lines, $m );
			}
		}
		$t = new Thread($l, $children);
		Threads::$loadedThreads[$l->thread_id] = $t;
		return $t;
	}

	private static function databaseError( $msg ) {
		// TODO tie into MW's error reporting facilities.
		echo("Corrupt liquidthreads database: $msg");
		die();
	}

	static function withRoot( $post ) {
		$ts = Threads::where( array('thread.thread_root' => $post->getID()) );
		if( count($ts) == 0 ) { return null; }
		if ( count($ts) >1 ) {
			Threads::databaseError("More than one thread with thread_root = {$post->getID()}.");
		}
		return $ts[0];
	}

	static function withId( $id ) {
		if( array_key_exists( $id, Threads::$loadedThreads ) ) {
			return Threads::$loadedThreads[ $id ];
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

?>
