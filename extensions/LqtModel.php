<?php

require_once('Article.php');


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

	protected function updateRecord() {
		$dbr =& wfGetDB( DB_MASTER );

        $res = $dbr->update( 'lqt_thread',
                             /* SET */   array( 'thread_root_post' => $this->rootPostId,
                       							'thread_article' => $this->articleId,
												'thread_subthread_of' => $this->superthreadId,
												'thread_summary_page' => $this->summaryId,
												'thread_subject' => $this->subject,
												'thread_touched' => $this->touched ),
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
	
	static function threadsOfPost( $post ) {
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