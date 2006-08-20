<?php

require_once('Article.php');

class Post extends Article {
     // Empty for the time being.
}

class Thread {

     var $rootPost;

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

     function hasSubthreads() {
	  // TODO inefficient.
	  return count( $this->subthreads() ) != 0;
     }

     function subthreads() {
	  return Thread::threadsWhere( array('thread_subthread_of' => $this->id),
				       array('ORDER BY' => 'thread_touched DESC') );
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