<?php

require_once('Article.php');

/**
	Contains the informaton needed to show an edit, reply, or new post form.
	Can grab that information either from an article in the database, or from
	the $wgRequest (or any other request). Can also present blank information
	for the case of replies or new posts on the first run through the
	edit-preview-diffs cycle.
*/
class PostProxy {
	
}

class Post extends Article {
	// Empty for the time being.
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
		                             array('ORDER BY' => 'thread_touched DESC') );
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

}

?>