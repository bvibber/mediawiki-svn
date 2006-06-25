
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

        /** Cause $this to be $other's nextPost. */
        function insertAfter($other) {
                if ( $it = $other->nextPost() )
                        $this->setNextPost( $it );
                $other->setNextPost( $this );
        }

        /** @return The last post at this level in the thread, including $this if $this has no nextPost. */
        function lastSibling() {
                $y = $x = $this;
                while( $x = $x->nextPost() ) {
                        $y = $x;
                }
                return $y;
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

        /** @private */
        function createLqtRecord() {
                $dbr =& wfGetDB( DB_MASTER );

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

                $res = $dbr->update( 'lqt',
                                     /* SET */   array( 'lqt_next' => $p->getID() ),
                                     /* WHERE */ array( 'lqt_this' => $this->getID(), ),
                                     __METHOD__);
                
		return null; // FIXME return success/failure.
        }

        function setFirstReply($p) { // TODO these two methods should be combined.
                $this->createLqtRecord();
                
                $dbr =& wfGetDB( DB_MASTER );

                $res = $dbr->update( 'lqt',
                                     /* SET */   array( 'lqt_first_reply' => $p->getID() ),
                                     /* WHERE */ array( 'lqt_this' => $this->getID(), ),
                                     __METHOD__);
                
		return null; // FIXME return success/failure.
        }
        
        /** @private */
        function followLink( $article, $variable ) {
                $dbr =& wfGetDB( DB_SLAVE );
                
                $line = $dbr->selectRow( array('lqt', 'page'), '*',
                                         array('lqt_this = page_id',
                                               'page_id' => $article->getID()),
                                         __METHOD__);
                
                if ( $line && $it = $line->$variable ) { 
                        $title = Title::newFromID($it);
                        return new Post($title);
                } else {
                        return null;
                }
        }
        
        /**
         * Find the given article's first top-level post. Returns null if the
         * given article doesn't have any unarchived posts.
         * @static
         */
        function firstPostOfArticle($article) {
                return Post::followLink($article, 'lqt_next');
        }
        
        /**
         * Returns this post's next sibling in the thread.
         */
        function nextPost() {
                return Post::followLink($this, 'lqt_next');
        }

        /**
         * Returns the first of this post's replies (children) in the thread.
         */
        function firstReply() {
                return Post::followLink($this, 'lqt_first_reply');
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
                        return User::newFromName($line->rev_user_text, false);
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
                        if ( User::newFromName($line->rev_user_text, false)->getName() != $orig->getName() ) {
                                $result = True;
                                break;
                        }
                }

		$dbr->freeResult($res);
		return $result;
        }

        function render($channel_name, $editing, $highlight) {
                global $wgOut, $wgUser;
                $this->fetchContent();

                $t = $this->mTitle->getPartialURL();
                
                $wgOut->addHTML( wfElement('a', array('name'=>"lqt_post_$t"), " " ) );
                
                if ($highlight) {
                        $wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post_highlight')) );
                } else {
                        $wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post')) );
                }

                if ($editing) {
                        $this->showEditingForm("?lqt_editing={$this->getID()}" );

                } else {
                        $author = $this->originalAuthor();
                        $sk = $wgUser->getSkin();

                        // Post body:
                        $wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post_body')) );
                        $wgOut->addWikiText($this->mContent);
                        $wgOut->addHTML( wfCloseElement( 'div') );

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

        static $titleString;
        
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

                // Show existing replies:
                if ( $it = $post->firstReply() ) {
                        $this->indent();
                        $this->renderThreadStartingFrom($it, $channel_name, $editing_id, $replying_to_id, $highlighting_title);
                        $this->unindent();
                }

                // Show reply editing form if we're rnneplying to this message:
                if ( $replying_to_id == $post->getID() ) {
                        $this->indent();
                        Post::newPostEditingForm($replying_to_id);
                        $this->unindent();
                }

                // Show siblings:
                if ( $it = $post->nextPost() ) {
                        $this->renderThreadStartingFrom($it, $channel_name, $editing_id, $replying_to_id, $highlighting_title);
                }
                
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
            
            if ($pageTitle == '') {
                    $wgOut->addWikiText("Try giving me the title of an article.");
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

            $editing_id = $wgRequest->getInt("lqt_editing", null);
            $replying_to_id = $wgRequest->getInt("lqt_replying_to_id", null);
            $highlighting_title = $wgRequest->getVal("lqt_highlight", null);
            $first_post = Post::firstPostOfArticle(new Article($title));
            $this->renderThreadStartingFrom( $first_post, $pageTitle, $editing_id, $replying_to_id, $highlighting_title);
        }
}

}


?>
