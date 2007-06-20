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
		$d = new DateTime($this->text());
		$d->modify($str);
		return new Date($d->format('YmdHis'));
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
			$t->$e +=     $t->$e - $o->$e;
		}

		// format in style of DateTime::modify().
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

class HistoricalThread {
	static function create( $livethread ) {
		echo ("pretended to create new historical thread.");
	}
}

class LiveThread {
	/* ID references to other objects that are loaded on demand: */
	protected $rootId;
	protected $articleId;
	protected $summaryId;

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
	
	/* Copy of $this made when first loaded from database, to store the data
	   we will write to the history if a new revision is commited. */
	protected $double;
	
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
					'thread_revision' => $this->revisionNumber ),
		     /* WHERE */ array( 'thread_id' => $this->id, ),
		     __METHOD__);
	}

	function __construct($line, $children) {
		$this->id = $line->thread_id;
		$this->rootId = $line->thread_root;
		$this->articleId = $line->thread_article;
		$this->summaryId = $line->thread_summary_page;
		$this->path = $line->thread_path;
		$this->timestamp = $line->thread_timestamp;
		$this->revisionNumber = $line->thread_revision;
		$this->replies = $children;
		//$this->double = clone $this;
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
		$this->touch();
	}

	function article() {
		if ( !$this->articleId ) return null;
		if ( !$this->article ) $this->article = new Article(Title::newFromID($this->articleId));
		return $this->article;
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
	
	protected function updateRecord() {
		$dbr =& wfGetDB( DB_MASTER );
		$res = $dbr->update( 'lqt_thread',
				     /* SET */   array( 'thread_root_post' => $this->rootId,
							'thread_article' => $this->articleId,
							'thread_subthread_of' => $this->superthreadId,
							'thread_summary_page' => $this->summaryId,
							'thread_subject' => $this->subject,
							'thread_timestamp' => $this->timestamp ),
				     /* WHERE */ array( 'thread_id' => $this->id, ),
				     __METHOD__);
	}
}

/** Module of factory methods. */
class Threads {

	static $loadedLiveThreads = array();
	
    static function newThread( $root, $article, $superthread = null ) {
        $dbr =& wfGetDB( DB_MASTER );
        $res = $dbr->insert('thread',
            array('thread_article' => $article->getID(),
                  'thread_root' => $root->getID(),
                  'thread_timestamp' => wfTimestampNow()),
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
				$threads[] = Threads::buildLiveThread( &$lines, $l );
			}
		}
		return $threads;
	}

	private static function buildLiveThread( $lines, $l ) {
		$children = array();
		$l_path = preg_quote($l->thread_path);
		foreach( $lines as $key => $m ) {
			if ( preg_match( "/^{$l_path}\.\d+$/", $m->thread_path ) ) {
					// $m->path begins with $l->path; this is a child.
//				unset($lines[$key]);
				$children[] = Threads::buildLiveThread( &$lines, $m );
			}
		}
		$t = new LiveThread($l, $children);
		Threads::$loadedLiveThreads[$l->thread_id] = $t;
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
		if( array_key_exists( $id, Threads::$loadedLiveThreads ) ) {
			return Threads::$loadedLiveThreads[ $id ];
		}
			
		$ts = Threads::where( array('thread.thread_id' => $id ) );
		if( count($ts) == 0 ) { return null; }
		if ( count($ts) >1 ) {
			Threads::databaseError("More than one thread with thread_id = {$id}.");
		}
		return $ts[0];
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
