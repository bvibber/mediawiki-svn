
<?php

/**
 * @package MediaWiki
 * @subpackage Extensions
 * @author David McCabe <davemccabe@gmail.com>
 * @licence GPL2
 */


# Wiki::articleFromTitle() seems to be your hook for special prefixes.

if( !defined( 'MEDIAWIKI' ) ) {
        echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
        die( -1 );
}
else {
        
require_once("Article.php");
require_once("EditPage.php");
        
require_once( 'SpecialPage.php' );
$wgExtensionFunctions[] = 'efLQ';

// FIXME need to find real way to do this.
function baseURL() {
        return "/wiki/index.php/Special:LQ/";
}
        

function efLQ() {
        global $wgMessageCache;
        $wgMessageCache->addMessage( 'lq', 'LiquidThreads' );
        SpecialPage::addPage( new LQ() );
}

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

        /* Inserts this as the *first* reply to $other, before any existing replies. */
        function insertAsFirstReplyTo($other) {
                if ( $it = $other->firstReply() )
                        $this->setNextPost( $it );
                $other->setFirstReply( $this );
        }

        /** @return The last post at this level in the thread, including $this if $this has no nextPost. */
        function lastSibling() {
                $y = $x = $this;
                while( $x = $x->nextPost() ) {
                        $y = $x;
                }
                return $y;
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

                        
                        
                }

                // ok, now actually move us.
                $this->deleteLinks();
                if ($slot == "reply")
                        $this->insertAsFirstReplyTo($to);
                elseif ($slot == "next")
                        $this->insertAfter($to);

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

                if ( $line->lqt_next ) {
                        $next_title = Title::newFromID($line->lqt_next);
                        $this->mNextPost = new Post($next_title);
                } else {
                        $this->mNextPost = null;
                }

                if ( $line->lqt_first_reply ) {
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
                if ( $line->page_id ) {
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
                if ( $line->page_id ) {
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
		$fname = 'LQ::originalAuthor';
                
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
                $fname = 'LQ::isPostModified';
                
                $orig = $this->originalAuthor();
                
		$dbr =& wfGetDB( DB_SLAVE );

                $res = $dbr->select( array('revision', 'page'), 'rev_user_text',
                                     array('rev_page = page_id',
                                           'page_id' => $this->getId()) );
                
                $result = False;
		while ( $line = $dbr->fetchObject( $res ) ) {
                        // newFromName normalizes, so we have to make sure both names have gone through it.
                        //        if ( User::newFromName($line->rev_user_text, false)->getName() != $orig->getName() ) {
                        if ( $line->rev_user_text != $orig ) {
                                $result = True;
                                break;
                        }
                }

		$dbr->freeResult($res);
		return $result;
        }


        /**
         * Render the article content, fetching from page cache if possible.
         * @private
         */
        function renderBody()
        {
                global $wgOut, $wgUser, $wgEnableParserCache;

                # Should the parser cache be used?
		$pcache = $wgEnableParserCache &&
			intval( $wgUser->getOption( 'stubthreshold' ) ) == 0 &&
			$this->exists() &&
			empty( $oldid ); // FIXME oldid
		wfDebug( 'Post::renderBody using parser cache: ' . ($pcache ? 'yes' : 'no' ) . "\n" );
		if ( $wgUser->getOption( 'stubthreshold' ) ) {
			wfIncrStats( 'pcache_miss_stub' );
		}

                $outputDone = false;
		if ( $pcache ) {
			$outputDone = $wgOut->tryParserCache( $this, $wgUser );
		}
                
                if (!$outputDone) {
                        $wgOut->addHTML('<span style="color: orange;">pasrer cache miss</span>');
                        $wgOut->addWikiText($this->mContent);
                }
        }
        
        function render($channel_name, $editing, $highlight) {
                global $wgOut, $wgUser;
                $this->fetchContent();

                $t = $this->mTitle->getPartialURL();

                $movingThis = ( LQ::$moving == $this->getID() );
                
                $wgOut->addHTML( wfElement('a', array('name'=>"lqt_post_$t"), " " ) );
                
                if ($highlight || $movingThis) {
                        $wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post_highlight')) );
                } else {
                        $wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post')) );
                }

                if ($editing) {
                        $this->showEditingForm("?lqt_editing={$this->getID()}" );

                } else {
                        $author = User::newFromName($this->originalAuthor(), false);

                        $sk = $wgUser->getSkin();

                        // Post body:
                        $wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post_body')) );
                        $this->renderBody();
                        $wgOut->addHTML( wfCloseElement( 'div') );

                        // Begin footer:
                        $wgOut->addHTML( wfOpenElement('ul', array('class'=>'lqt_footer')) );

                        // Signature:
                        $wgOut->addHTML( wfOpenElement( 'li') );
                        $wgOut->addWikiText( $author->getSig(), false );
                        $wgOut->addHTML( wfCloseElement( 'li') );
                        $wgOut->addHTML( wfElement( 'li', null, ($this->isPostModified() ? "Modified" : "Original")) );

                        // Edit and reply links:
                        $edit_href = "$channel_name?lqt_editing={$this->getID()}#lqt_post_$t";
                        $wgOut->addHTML( wfOpenElement('li') .
                                         wfElementClean('a', array('href'=>$edit_href),'Edit') .
                                         wfCloseElement( 'li') );

                        $reply_href = "$channel_name?lqt_replying_to_id={$this->getID()}#lqt_post_$t";
                        $wgOut->addHTML( wfOpenElement('li') .
                                         wfElementClean('a', array('href'=>$reply_href),'Reply') .
                                         wfCloseElement( 'li') );

                        $move_href = "$channel_name?lqt_moving_id={$this->getID()}";
                        $wgOut->addHTML( wfOpenElement('li') .
                                         wfElementClean('a', array('href'=>$move_href),'Move') .
                                         wfCloseElement( 'li') );

                        // End footer:
                        $wgOut->addHTML( wfCloseElement('ul') );

                        
                }
                $wgOut->addHTML( wfCloseElement( 'div') );
        }

        
        function showEditingForm( $query ) {
                global $wgRequest, $wgOut;
                $e = new EditPage($this);
                $e->setAction(  baseURL() . LQ::$titleString . $query );
                $e->edit();
                if ($e->mDidRedirect) {
                        // Override editpage's redirect.
                        $t = $this->mTitle->getPartialURL();
                        $wgOut->redirect(baseURL().LQ::$titleString.'?lqt_highlight='.$t.'#lqt_post_'.$t);
                }
                // Insert new posts into the threading:
                if ($e->mDidSave && $wgRequest->getVal("lqt_post_new", false)) {
                        $channel_title = Title::newFromText(LQ::$titleString);
                        $this->insertAfter(new Post($channel_title));
                }
                // Insert replies into the threading:
                $replying_to_id = $wgRequest->getVal("lqt_replying_to_id", null);
                if ($e->mDidSave && $replying_to_id) {
                        $reply_to_title = Title::newFromID( $replying_to_id );
                        $this->insertAsReplyTo( new Post($reply_to_title) );
                }
        }


        /**
         * @param ID of the post that this is a reply to, or else null if it's not a reply.
         * @static
         */
        function newPostEditingForm($reply_to=null) {
                global $wgRequest;
                if ( !$it = $wgRequest->getVal("lqt_post_title", false) ) {
                        $token = md5(uniqid(rand(), true));
                        $new_title = Title::newFromText( "Post:$token" );
                } else {
                        $new_title = $it;
                }

                $p = new Post($new_title);
                if ($reply_to) {
                        $p->showEditingForm("?lqt_replying_to_id=$reply_to&lqt_post_title=${$new_title->getPartialURL}");
                } else {
                        $p->showEditingForm("?lqt_post_new=1&lqt_post_title=${$new_title->getPartialURL}");
                }
        }

        
}


class LQ extends SpecialPage {

        static $article;
        static $titleString;
        static $moving;
        
        function LQ() {
                SpecialPage::SpecialPage( 'LQ', 'lq' );
        }
        
        
        /**
         * Recursively tells every post in a thread to render itself.
         * Also invokes showEditingForm as appropriate.
         * TODO get rid of all these lame parameters.
         * @param $post Post The first top-level post in the thread.
         * @param $channel_name string Title of the channel/talk page we're on.
         * @param $editing_id int ID of the post we are editing, if any.
         * @param $replying_to_id int ID of the post we are replying to, if any.
         * @param $highlighting_title string Title main part of post to highlight.
         */
        function renderThreadStartingFrom($post, $channel_name, $editing_id=-1, $replying_to_id=-1, $highlighting_title=null) {

                $post->render( $channel_name, ($editing_id == $post->getId()), ($highlighting_title==$post->mTitle->getPartialURL()) );

                // Button to move a post to be the first reply of this post:
                if ( LQ::$moving &&
                     LQ::$moving != $post->getID() &&
                     ($post->firstReply() ? LQ::$moving != $post->firstReply()->getID() : true) ) {
                        $this->indent();
                        $this->showMoveButton( 'reply', $post->getID() );
                        $this->unindent();
                }
                
                // Show existing replies:
                if ( $it = $post->firstReply() ) {
                        $this->indent();
                        $this->renderThreadStartingFrom($it, $channel_name, $editing_id, $replying_to_id, $highlighting_title);
                        $this->unindent();
                }

                // Show reply editing form if we're replying to this post:
                if ( $replying_to_id == $post->getID() ) {
                        $this->indent();
                        Post::newPostEditingForm($replying_to_id);
                        $this->unindent();
                }

                // Button to move a post to be the next post of this post:
                if ( LQ::$moving &&
                     LQ::$moving != $post->getID() &&
                     ($post->nextPost() ? LQ::$moving != $post->nextPost()->getID() : true) ) {
                        $this->showMoveButton( 'next', $post->getID() );
                }

                // Show siblings:
                if ( $it = $post->nextPost() ) {
                        $this->renderThreadStartingFrom($it, $channel_name, $editing_id, $replying_to_id, $highlighting_title);
                }
        }

        function showMoveButton( $type, $id ) {
                global $wgOut;
                $form_action = baseURL() . LQ::$titleString;
                $wgOut->addHTML(
                        wfOpenElement('form', array('action' => $form_action,
                                                    'method' => 'POST')) .
                        wfHidden("lqt_move_to_$type", "$id") .
                        wfHidden("lqt_move_post_id", LQ::$moving) .
                        wfSubmitButton('Move Here') .
                        wfCloseElement('form') );
        }

        // Should these be separate methods?
        
        /**
         * Output any HTML that is to come right before we tell our reply
         * comments to render themselves.
        */
        function indent( ) {
                global $wgOut;
                $wgOut->addHTML( wfOpenElement( 'dl', array('class'=>'lqt_replies') ) );
                $wgOut->addHTML( wfOpenElement( 'dd') );
        }

        /**
         * Output any HTML that is to come right after we tell our reply
         * comments to render themselves.
        */
        function unindent( ) {
                global $wgOut;
                $wgOut->addHTML( wfCloseElement( 'dd') );
                $wgOut->addHTML( wfCloseElement( 'dl') );
        }

        function execute() {
            global $wgUser, $wgRequest, $wgOut, $wgArticle;
                                         
            $this->setHeaders(); # not sure what this does.
            
            # Extract the 'title' part of the path (between slash and query string)
            $tmp1 = split( "LQ/", $wgRequest->getRequestURL() );
            $tmp2 = split('\?', $tmp1[1]);
            LQ::$titleString = $pageTitle = $tmp2[0]; //FIXME
            $this->title = $title = Title::newFromText($pageTitle); 

            LQ::$article = new Post($title); // post so we can do firstPostOfArticle() etc.
            
            if ($pageTitle == '') {
                    $wgOut->addWikiText("Try giving me the title of an article.");
                    return;
            }

            $first_post = Post::firstPostOfArticle(LQ::$article);

            // Execute move operations:
            $post_id     = $wgRequest->getInt( 'lqt_move_post_id',  false );
            $reply_to_id = $wgRequest->getInt( 'lqt_move_to_reply', false );
            $next_to_id  = $wgRequest->getInt( 'lqt_move_to_next',  false );
            $to_id = $reply_to_id ? $reply_to_id : $next_to_id;
            if ( $post_id && $to_id ) {
                    $posttitle = Title::newFromID($post_id);
                    $totitle = Title::newFromID($to_id);
                    $post = new Post( $posttitle );
                    $to = new Post( $totitle );
                    $post->moveNexTo($to, $reply_to_id ? 'reply' : 'next');

                    // Wipe out POST so user doesn't get the "Danger Will
                    // Robinson there's POST data" message when refreshing the page.
                    $query = "?lqt_highlight={$posttitle->getPartialURL()}#lqt_post_{$posttitle->getPartialURL()}";
                    $wgOut->redirect( baseURL() . LQ::$titleString . $query );
                    return;
            }
            
            $wgOut->addHTML( wfOpenElement('p') .
                             wfElement('strong', null, $pageTitle) .
                             wfCloseElement('p'));
            
            if ( $wgRequest->getBool("lqt_post_new", false) ) {
                    Post::newPostEditingForm(null);
            } else {
                    $wgOut->addHTML( wfElement('a',
                                               array('href'=>"{$pageTitle}?lqt_post_new=1"),
                                               "Post New Thread") );
            }

            LQ::$moving = $wgRequest->getInt('lqt_moving_id');
            if ( LQ::$moving ) {
                    // we'll add this when we figure out how to make it work:
                    /*$wgOut->addHTML( wfOpenElement('p') );
                    $wgOut->addHTML(  wfElement( 'label', array('for'=>'lqt_move_summary'), "Comment for this move:" ) .
                                      wfInput( 'lqt_move_summary' ) );
                                      $wgOut->addHTML( wfCloseElement('p') );*/

                    if( LQ::$moving != $first_post->getID() ) {
                            $wgOut->addHTML( wfOpenElement('p') );
                            $this->showMoveButton( 'next', LQ::$article->getID() );
                            $wgOut->addHTML( wfCloseElement('p') );
                    }
            }
            
            $editing_id = $wgRequest->getInt("lqt_editing", null);
            $replying_to_id = $wgRequest->getInt("lqt_replying_to_id", null);
            $highlighting_title = $wgRequest->getVal("lqt_highlight", null);

            $this->renderThreadStartingFrom( $first_post, $pageTitle, $editing_id, $replying_to_id, $highlighting_title);
        }
}

}


?>
