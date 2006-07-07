<?php

require_once('Article.php');

class Post extends Article {
        /** cf. loadLinks() @private */
        var $mLinksLoaded = false;
        /** cf. loadBacklinks() @private */
        var $mBacklinksLoaded = false;
        
        /**
	 * Constructor and clear the post. This does exactly the same thing as
         * the Article constructor.
	 * @param $title Reference to a Title object.
	 * @param $oldId Integer revision ID, null to fetch from request, zero for current
	 */
	function Post( &$title, $oldId = null ) {
		$this->mTitle =& $title;
		$this->mOldId = $oldId;
		$this->clear();
	}

        
        /**
         * Absolve this post from any threading relationships except
         * this post's replies stay intact. For use when moving posts around.
         */
        function deleteLinks() {
                if ( $parent_post = $this->parentPost() )
                        $parent_post->setFirstReply($this->nextPost());
                if ( $previous_post = $this->previousPost() ) {
                        $previous_post->setNextPost($this->nextPost());
                }
                $this->setNextPost(null);
        }
        
        /** Cause $this to be $other's nextPost. */
        function insertAfter($other) {
                if ( $it = $other->nextPost() )
                        $this->setNextPost( $it );
                $other->setNextPost( $this );
        }
        /** Insert this as the *last* reply to $other, after any existing replies. */
        function insertAsReplyTo($other) {
                if( $first = $other->firstReply() ) {
                        $first->lastSibling()->setNextPost( $this );;
                } else {
                        // $other has no existing replies.
                        $other->setFirstReply( $this );
                }
        }

        /** Inserts this as the *first* reply to $other, before any existing replies. */
        function insertAsFirstReplyTo($other) {
                if ( $it = $other->firstReply() )
                        $this->setNextPost( $it );
                $other->setFirstReply( $this );
        }

	function talkPage() {
	     if ( $this->previousPost() ) {
		  if ( $this->previousPost()->isPost() )
		       return $this->previousPost()->talkPage();
		  else
		       return $this->previousPost();
	     } elseif ( $this->parentPost() ) {
		  return $this->parentPost()->talkPage();
	     } else {
		  return null;
	     }
	}

	function isPost() {
	     return ($this->mTitle->getNamespace() == 100); #FIXME
	}
	
        /** @return The last post at this level in the thread, including $this if $this has no nextPost. */
        function lastSibling() {
                $y = $x = $this;
                while( $x = $x->nextPost() ) {
                        $y = $x;
                }
                return $y;
        }

	function topLevelReplies() {
	     $x = $this->firstReply();
	     $result = array($x);
	     while( $x = $x->nextPost() ) {
		  array_push($result, $x);
	     }
	     return $result;
	}

        /**
         * Move this post in the threading so that it is either the nextpost or firstreply of the post whose ID is given.
         * @param $to Post to move next to.
         * @param $slot string either "next" or "reply" as appropriate.
         */
        function moveNextTo($to, $slot) {

                // If we've moved down into our own reply thread, we need to
                // reparent our children at a higher level.
                if ( $to->isUnderneath($this) ) {
		     $p = $this->previousPost();
		     foreach( $this->topLevelReplies() as $r ) {
			  $r->moveNextTo($p);
			  $p = $r; 
		     }
		}
		     
		     
/*		     if ( $this->previousPost() ) {
			  $this->previousPost()->setNextPost($this->firstReply());
		     } else {
			  $this->parentPost()->setFirstReply($this->firstReply());
		     }
		     $this->firstReply()->lastSibling()->setNextPost($this->nextPost());
		     $this->setFirstReply(null);*/

                // ok, now actually move us.
                $this->deleteLinks();
                if ($slot == "reply")
                        $this->insertAsFirstReplyTo($to);
                elseif ($slot == "next")
                        $this->insertAfter($to);

        }
	
	/**
	 @param $p Some Post
	 @return True if $this lies anywhere in the thread of replies to $p.
	 */
	function isUnderneath($p) {
	     return ( $this->parentPost() && $this->parentPost()->getID() == $p->getID() )    ||
		    ( $this->parentPost() && $this->parentPost()->isUnderneath($p) )          ||
 		    ( $this->previousPost() && $this->previousPost()->isUnderneath($p) );
	}
        
        /** @private */
        function createLqtRecord() {
                $dbr =& wfGetDB( DB_MASTER );

                if ( $this->getID() == 0 ) { debug_print_backtrace(); }
                
                $res = $dbr->select( array('lqt', 'page'), 'lqt_id',
                                     array('lqt_this = page_id',
                                           'page_id'=>$this->getID()),
                                     __METHOD__ );
                
                if ( $dbr->numrows($res) == 0 ) {
                        $dbr->freeResult($res);
                        $res2 = $dbr->insert('lqt', array('lqt_this' => $this->getID(),
                                                          'lqt_next' => null,
                                                          'lqt_first_reply' => null),
                                             __METHOD__);
                        return true;
                } else {
                        $dbr->freeResult($res);
                        return false;
                }
        }
        
        function setNextPost($p) {

                $this->createLqtRecord();
                
                $dbr =& wfGetDB( DB_MASTER );

                $set_to = $p ? $p->getID() : null;
                
                $res = $dbr->update( 'lqt',
                                     /* SET */   array( 'lqt_next' => $set_to ),
                                     /* WHERE */ array( 'lqt_this' => $this->getID(), ),
                                     __METHOD__);

                $this->mNextPost = $p;
                
		return null; // FIXME return success/failure.
        }

        function setFirstReply($p) { // TODO these two methods should be combined.
                $this->createLqtRecord();
                
                $dbr =& wfGetDB( DB_MASTER );

                $set_to = $p ? $p->getID() : null;
                
                $res = $dbr->update( 'lqt',
                                     /* SET */   array( 'lqt_first_reply' => $set_to ),
                                     /* WHERE */ array( 'lqt_this' => $this->getID(), ),
                                     __METHOD__);

                $this->mFirstReply = $p;
                
		return null; // FIXME return success/failure.
        }

        /**
         * Populate $mFirstReply and $mNextPost with the appropriate Post objects.
         * Hits the database, but only the first time.
         * @private
         */
        function loadLinks() {
                if ($this->mLinksLoaded) return;
                $this->mLinksLoaded = true;

                $dbr =& wfGetDB( DB_SLAVE );
                
                $line = $dbr->selectRow( array('lqt', 'page'),
                                         array('lqt_next', 'lqt_first_reply'),
                                         array('lqt_this = page_id',
                                               'page_id' => $this->getID()),
                                         __METHOD__);

                if ( $line && $line->lqt_next ) {
                        $next_title = Title::newFromID($line->lqt_next);
                        $this->mNextPost = new Post($next_title);
                } else {
                        $this->mNextPost = null;
                }

                if ( $line && $line->lqt_first_reply ) {
                        $reply_title = Title::newFromID($line->lqt_first_reply);
                        $this->mFirstReply = new Post($reply_title);
                } else {
                        $this->mFirstReply = null;
                }
        }

        /**
         * Similar to loadLinks(), above, but finds mPreviousPost and
         * mParent. This is a separate method because it's a separate query and
         * it's only needed during moves and deletes.
         * TODO: find a way to consolidate these queries.
         * @private
         */
        function loadBacklinks() {
                if ($this->mBacklinksLoaded) return;
                $this->mBacklinksLoaded = true;

                $dbr =& wfGetDB( DB_SLAVE );
                
                $line = $dbr->selectRow( array('lqt', 'page'),
                                         array('lqt_next', 'page_id'),
                                         array('lqt_this = page_id',
                                               'lqt_next' => $this->getID()),
                                         __METHOD__);
                if ( $line && $line->page_id ) {
                        $title = Title::newFromID($line->page_id);
                        $this->mPreviousPost = new Post($title);
                } else {
                        $this->mPreviousPost = null;
                }

                $line = $dbr->selectRow( array('lqt', 'page'),
                                         array('lqt_first_reply', 'page_id'),
                                         array('lqt_this = page_id',
                                               'lqt_first_reply' => $this->getID()),
                                         __METHOD__);
                if ( $line && $line->page_id ) {
                        $title = Title::newFromID($line->page_id);
                        $this->mParentPost = new Post($title);
                } else {
                        $this->mParentPost = null;
                }


        }

        /**
         * Behavior is undefined if more than one post points to $this.
         * @return The post whose next post is $this.
         */
        function previousPost() {
                $this->loadBacklinks();
                return $this->mPreviousPost;
        }

        /**
         * Behavior is undefined if more than one post points to $this.
         * @return The post whose first reply is $this.
         */
        function parentPost() {
                $this->loadBacklinks();
                return $this->mParentPost;
        }
        
        /**
         * Find the given article's first top-level post. Returns null if the
         * given article doesn't have any unarchived posts.
         * @static
         */
        function firstPostOfArticle($article) {
                $article->loadLinks();
                return $article->mNextPost;
        }
        
        /**
         * Returns this post's next sibling in the thread.
         */
        function nextPost() {
                $this->loadLinks();
                return $this->mNextPost;
        }

        /**
         * Returns the first of this post's replies (children) in the thread.
         */
        function firstReply() {
                $this->loadLinks();
                return $this->mFirstReply;
        }
                
        /**
         * Find the article's original author (the user who created the first revision).
         * @param Article $article
         * @return User
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
                        return $line->rev_user_text;
#                        return User::newFromName($line->rev_user_text, false);
                else
                        return null;   # FIXME die.
        }
        
        /** @return boolean True if anyone other than the original author has edited $article. */
        function isPostModified() {
                $orig = $this->originalAuthor();
                
		$dbr =& wfGetDB( DB_SLAVE );

                $res = $dbr->select( array('revision', 'page'), 'rev_user_text',
                                     array('rev_page = page_id',
                                           'page_id' => $this->getId()) );
                
                $result = False;
		while ( $line = $dbr->fetchObject( $res ) ) {
                        if ( $line->rev_user_text != $orig ) {
                                $result = True;
                                break;
                        }
                }

		$dbr->freeResult($res);
		return $result;
        }
        
}

?>