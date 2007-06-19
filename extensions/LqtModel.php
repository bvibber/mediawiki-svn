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
	}*/
	static function monthString($text) {
		return substr($text, 0, 6);
	}

	function delta( $o ) {
	  $t = clone $this;
	  $els = array('year', 'month', 'day', 'hour', 'minute', 'second');
	  $deltas = array();
	  foreach ($els as $e) {
	    $deltas[$e] = $t->$e - $o->$e;
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

class LiveThread {
	/* ID references to other objects that are loaded on demand: */
	protected $rootId;
	protected $articleId;
	protected $summaryId;
	protected $superthreadId;

	/* Actual objects loaded on demand from the above when accessors are called: */
	protected $root;
	protected $article;
	protected $summary;
	protected $superthread;

	/* Simple strings: */
	protected $subject;
	protected $timestamp;
	
	/* Identity */
	protected $id;

	function __construct($line, $children) {
		$this->id = $line->thread_id;
		$this->rootId = $line->thread_root;
		$this->articleId = $line->thread_article;
		$this->summaryId = $line->thread_summary_page;
		$this->path = $line->thread_path;
		$this->timestamp = $line->thread_timestamp;
		$this->replies = $children;
	}

	function setSuperthread($thread) {
		$this->superthreadId = $thread->id();
		$this->touch();
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
		$this->summaryId = $post->getID();
		$this->updateRecord();
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
		if( $this->superthread() ) {
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
		
		/* Select the client's threads, AND all their children: */

		$sql = <<< SQL
SELECT children.* FROM $tables thread, thread children
WHERE $where AND
children.thread_path LIKE CONCAT(thread.thread_path, "%")
$options
SQL;
                $res = $dbr->query($sql); 

		/*
                 God probably kills a kitten whenever this next section of code is run.
                 We're creating a tree of objects from the flat list of rows. Please someone
                 think of a way to do this in one pass.
		*/

                $tree = array(); 
		while ( $line = $dbr->fetchObject($res) ) {
			$path = explode('.', $line->thread_path);
			Threads::setDeepArray( $tree, $line, $path );
		}
		var_dump($tree);

		$threads = array();
		foreach( $tree as $root ) {
			$threads[] = Threads::createThreads($root);
		}
		
		return $threads;
	}

	private static function createThreads( $thread ) {
		$subthreads = array();
		foreach( $thread as $key => $val ) {
			if ( $key != 'root' ) {
				$subthreads[] = Threads::createThreads( $val );
			}
		}
		return new LiveThread( $thread['root'], $subthreads );
	}

	/** setDeepArray( $a, $v, array(1,2,3) ) <=> $a[1][2][3]['root'] = $v; */
	private static function setDeepArray( &$a, $v, $p ) {
		if( count($p) == 1 ) {
			$a[$p[0]]["root"] = $v;
		} else {
			if( !array_key_exists( $p[0], $a ) )
				$a[$p[0]] = array();
			Threads::setDeepArray( $a[$p[0]], $v, array_slice($p, 1) );
		}
	}

	static function withRoot( $post ) {
		return Threads::where( array('thread.thread_root' => $post->getID()) );
	}

}
/*
function lqtCheapTest() {
	$threads = Threads::threadsWhere( "thread.thread_id = 1", "order by thread_timestamp" );
	function cheapShowThread($t) {
		global $wgOut;
		$wgOut->addHTML($t->id());
		$wgOut->addHTML('<dl><dd>');
		foreach( $t->replies as $r ) {
			cheapShowThread($r);
		}
		$wgOut->addHTML('</dd></dl>');
	}
	foreach( $threads as $t ) {
		cheapShowThread($t);
	}
}
*/
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
