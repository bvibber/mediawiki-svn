<?php

/**
 Ga ga goo goo.
*/
class Thread {

     var $mId;
     var $mArticleId;
     var $mFirstPostId;
     var $mSummaryPostId;
     var $mTouched;
     var $mSubject;

     var $mFirstPost;
     var $mFirstPostLoaded = false;
     
     var $mArticle;
     var $mArticleLoaded = false;

     var $mSummaryPost;
     var $mSummaryPostLoaded = false;

     /**
      * @param $dbline line as returned from $dbr->fetchObject().
      */
     protected function Thread( $dbline = null ) {
	  if ( $dbline ) {
	       $this->mId            = $dbline->lqt_thread_id;
	       $this->mArticleId     = $dbline->lqt_thread_page;
	       $this->mFirstPostId   = $dbline->lqt_thread_first_post;
	       $this->mSummaryPostId = $dbline->lqt_thread_summary_post;
	       $this->mTouched       = $dbline->lqt_thread_touched;
	       $this->mSubject       = $dbline->lqt_thread_subject;
	  }
     }

     static function insertNewThread( $article, $first_post ) {
	  $dbr =& wfGetDB( DB_MASTER );
	  $res = $dbr->insert('lqt_thread',
			      array('lqt_thread_page' => $article->getID(),
				    'lqt_thread_first_post' => $first_post->getID(),
				    'lqt_thread_touched' => wfTimestampNow()),
			      __METHOD__);
	  // TODO we could avoid a query here.
	  return Thread::newFromId( $dbr->insertId() );
     }

     static function newFromId( $id ) {
	  $dbr =& wfGetDB( DB_SLAVE );
                
	  $line = $dbr->selectRow( array('lqt_thread', 'page'),
				   array('*'),
				   array('lqt_thread_id' => $id),
				   __METHOD__);

	  if ( $line ) {
	       return new Thread( $line );
	  } else {
	       return null;
	  }

     }
     
     /** Returns an array of months in which threads of the given article have been touched. format is "YYYYMM" */
     static function monthsWithThreadsInArticle( $article ) {
	  $dbr =& wfGetDB( DB_SLAVE );
	  
	  $res = $dbr->select( array('lqt_thread'),
			       array('lqt_thread_touched'),
			       array('lqt_thread_page' => $article->getID()),
			       __METHOD__,
			       array('ORDER BY' => 'lqt_thread_touched DESC'));
	  
	  $months = array();
	  while ( $line = $dbr->fetchObject($res) ) {
	       $months[] = substr($line->lqt_thread_touched, 0, 6);
	  }
	  return array_unique($months);
     }
     
     /**
      * Return an array of every thread belonging to the given Article.
      * If $first_day and $last_day are provided, only return threads
      * that have been touched between those days, inclusive.
      * They should be strings of the form 'YYYYMMDDHHMMSS'.
      */
     static function threadsOfArticle( $article, $first_day = null, $last_day = null ) {
	  $dbr =& wfGetDB( DB_SLAVE );
	  
	  if ( $first_day && $last_day ) {
	       $where_clause = array('lqt_thread_page' => $article->getID(),
				     "lqt_thread_touched >= $first_day",
				     "lqt_thread_touched <= $last_day");
	  } else {
	       $where_clause = array('lqt_thread_page' => $article->getID());
	  }

	  $res = $dbr->select( array('lqt_thread'),
			       array('*'),
			       $where_clause,
			       __METHOD__,
			       array('ORDER BY' => 'lqt_thread_touched DESC'));
	  $threads = array();
	  while ( $line = $dbr->fetchObject($res) ) {
	       $threads[] = new Thread( $line );
	  }
	  return $threads;
     }

     function getID() { return $this->mId; }
     
     /** @return Post the thread's first top-level post, from which the linked list hangs. */
     function firstPost() {
	  if (!$this->mFirstPostLoaded) {
	       $title = Title::newFromID($this->mFirstPostId);
	       $this->mFirstPost = new Post($title);
	       $this->mFirstPostLoaded = true;
	  }
	  return $this->mFirstPost;
     }

     /** @return Article the article to whose talk page this thread belongs. */
     function article() {
	  if (!$this->mArticleLoaded) {
	       $title = Title::newFromID($this->mArticleId);
	       $this->mArticle = new Article($title);
	       $this->mArticleLoaded = true;
	  }
	  return $this->mArticle;
     }

     /** @return Article the summary article */
     function summary() {
	  if (!$this->mSummaryPostLoaded) {
	       $title = Title::newFromID($this->mSummaryPostId);
	       $this->mSummaryPost = new Article($title);
	       $this->mSummaryPostLoaded = true;
	  }
	  return $this->mSummaryPost;
     }

     function subject() {
	  return $this->mSubject;
     }

     function touched() {
	  return $this->mTouched;
     }

     function setSubject($s) {
	  $dbr =& wfGetDB( DB_MASTER );

	  $res = $dbr->update( 'lqt_thread',
			       /* SET */   array( 'lqt_thread_subject' => $s ),
			       /* WHERE */ array( 'lqt_thread_id' => $this->getID() ),
			       __METHOD__);

	  $this->mSubject = $s;
                
	  return $res;
     }

     
}

?>