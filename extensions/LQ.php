
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
                $this->setNextPost( $other->nextPost() );
                $other->setNextPost( $this );
        }

        /** @private */
        function createLqtRecord() {
                $dbr =& wfGetDB( DB_MASTER );

                $sql = "SELECT lqt_id FROM lqt JOIN page ON lqt_this = page_id WHERE page_id = {$this->getID()}";
                $res = $dbr->query($sql, __METHOD__);

                if (!$dbr->fetchObject($res)) {
                        $dbr->freeResult($res);
                        $res2 = $dbr->query("INSERT INTO lqt (lqt_this, lqt_next, lqt_first_reply)
                                             VALUES ({$this->getID()}, NULL, NULL)", __METHOD__);
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

        /** @private */
        function followLink( $article, $variable ) {
                $dbr =& wfGetDB( DB_SLAVE );
                
                $sql = "SELECT * FROM lqt
                            JOIN page ON lqt_this = page_id
                            WHERE page_id = {$article->getID()}";
                
                $res = $dbr->query($sql, __METHOD__);
                
                if ( $line = $dbr->fetchObject($res) ) { 
                        if ( $it = $line->$variable ) {
                                $title = Title::newFromID($it);
                                $post = new Post($title);
                        } else {
                                $post = null;
                        }
                } else {
                        $post = NULL;
                }
                
		$dbr->freeResult($res);
		return $post;
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
         * @param Article $article
         * @return string The user_text of the user who created the first
         *                revision of this article (the original author).
         */
        function originalAuthor() {
		$fname = 'LQ::originalAuthor';
                
		$dbr =& wfGetDB( DB_SLAVE );
		$revTable = $dbr->tableName( 'revision' );
                
                $sql = "select rev_user_text from $revTable
                            join page on rev_page=page_id
                            where page_id = {$this->getID()}
                            order by rev_timestamp
                            limit 1;";
		$res = $dbr->query($sql, $fname);
                
		if ( $line = $dbr->fetchObject( $res ) ) {
                        $result = $line->rev_user_text;
                } else {
                        # FIXME die.
                        $result = NULL;
                }
                
		$dbr->freeResult($res);
		return $result;
        }
        
        /** @return boolean True if anyone other than the original author has edited $article. */
        function isPostModified() {
                $fname = 'LQ::isPostModified';
                
                $orig = $this->originalAuthor();
                
		$dbr =& wfGetDB( DB_SLAVE );
		$revTable = $dbr->tableName( 'revision' );

                $sql = "select rev_user_text from $revTable
                            join page on rev_page=page_id
                            where page_id = {$this->getId()}";
		$res = $dbr->query($sql, $fname);

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

        function render($channel_name, $editing) {
                global $wgOut;
                $this->fetchContent();

                $wgOut->addHTML('<div class="lqt_post">');
                if ($editing) {
                        $action_url = baseURL() . $channel_name . "?lqt_do_editing=1&lqt_post_title=" . $this->mTitle->getPartialURL();
                        $e = new EditPage($this);
                        $e->initialiseForm();
                        $e->showEditForm(null, $action_url, $channel_name);
                } else {
                        $wgOut->addHTML('<div class="lqt_post_body">');
                        $wgOut->addWikiText($this->mContent);
                        $wgOut->addHTML('</div><div class="lqt_post_footer">');
                        $wgOut->addWikiText( $this->originalAuthor() );
                        $wgOut->addWikiText( $this->isPostModified() ? "Modified" : "Original" );
                        $wgOut->addWikiText( "[[{$this->mTitle->getPrefixedURL()}|Permalink]]" );
                        $wgOut->addHTML( "<a href=\"$channel_name?lqt_editing={$this->getID()}\">Edit</a>" );
                        $wgOut->addHTML('</div>');
                        $wgOut->addHTML('</div>');
                }
        }

}

class LQ extends SpecialPage {
        
        function LQ() {
                SpecialPage::SpecialPage( 'LQ', 'lq' );
        }
        

        /**
         * Recursively tells every post in a thread to render itself.
         * @param The first top-level post in the thread.
         */
        function renderThreadStartingFrom($post, $channel_name, $editing_id) {

                $post->render( $channel_name, ($editing_id == $post->getId()) );

                if ( $it = $post->firstReply() ) {
                        $this->indent();
                        $this->renderThreadStartingFrom($it, $channel_name, $editing_id);
                        $this->unindent();
                }
                if ( $it = $post->nextPost() ) {
                        $this->renderThreadStartingFrom($it, $channel_name, $editing_id);
                }
                
        }

        // Should these be separate methods?
        
        /**
         * Output any HTML that is to come right before we tell our reply
         * comments to render themselves.
        */
        function indent( ) {
                global $wgOut;
		$wgOut->addHTML( '<div class="lqt_begin_replies">' );
        }

        /**
         * Output any HTML that is to come right after we tell our reply
         * comments to render themselves.
        */
        function unindent( ) {
                global $wgOut;
                $wgOut->addHTML( '</div>');
        }

        // ugliness alert...
        
        function execute() {
            global $wgUser, $wgRequest, $wgOut, $wgArticle;
            $this->setHeaders(); # not sure what this does.

            
            # Extract the 'title' part of the path (between slash and query string)
            $tmp1 = split( "LQ/", $wgRequest->getRequestURL() );
            $tmp2 = split('\?', $tmp1[1]);
            $pageTitle = $tmp2[0];
            $title = Title::newFromText($pageTitle);
            
            if (!$pageTitle != '') {
                    $wgOut->addWikiText("Try giving me the title of an article.");
                    return;
            }

            if ( $wgRequest->getBool("do_post_new", false) ||
                    $wgRequest->getBool("lqt_do_editing", false) ) {

                    $new_title = Title::newFromText( "Post:{$wgRequest->getVal('lqt_post_title')}" );
                    $p = new Post($new_title);
                    $e = new EditPage($p);
                    $e->importFormData( $wgRequest );
                    $e->attemptSave();

                    if ( $wgRequest->getBool("do_post_new", false) )
                         $p->insertAfter(new Post($title));

                    // Override the redirect made by $e->attemptSave().
                    // TODO handle errors.
                    $wgOut->redirect(baseURL().$pageTitle);
                    
                    return;
            }

            
            $wgOut->addWikiText($pageTitle);
            
            if ( $wgRequest->getBool("post_new", false) ) {

                    $token = md5(uniqid(rand(), true));
                    $new_title = Title::newFromText( "Post:$token" );
                    // TODO this should be POST.
                    $action_url = baseURL() . $pageTitle . "?do_post_new=1&lqt_post_title=" . $new_title->getPartialURL();
                    
                    $p = new Post($new_title);
                    $e = new EditPage($p);
                    $e->showEditForm(null, $action_url, $wgRequest->getRequestURL());
            } else {
                    $wgOut->addHTML( "<a href=\"{$pageTitle}?post_new=1\">Post a New Thread</a>" );
            }

            $editing_id = $wgRequest->getInt("lqt_editing", null);
            $first_post = Post::firstPostOfArticle(new Article($title));
            $this->renderThreadStartingFrom( $first_post, $pageTitle, $editing_id );
        }
}

}

?>

