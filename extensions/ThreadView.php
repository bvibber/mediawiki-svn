<?php

require_once('PostHistory.php');

class ThreadView {

     /** hack. see showEditingForm. @private */
     static $callbackpost;
     static $callbackeditpage;

	function ThreadView( $baseURL, 
		$channelName,
		$editingId = -1,
		$replyingToId = -1,
		$historyId = -1,
		$highlightingTitle = -1,
		$movingId = -1 ) { /* TODO why the hell title/id? */
			$this->baseURL = $baseURL;
			$this->channelName = $channelName;
			$this->editingId = $editingId;
			$this->historyId = $historyId;
			$this->replyingToId = $replyingToId;
			$this->highlightingTitle = $highlightingTitle;
			$this->movingId = $movingId;
	}
     
	/**
	* Render the article content, fetching from page cache if possible.
	* @private
	*/
	function renderBody($p)
	{
		global $wgOut, $wgUser, $wgEnableParserCache;

		# Should the parser cache be used?
		$pcache = $wgEnableParserCache &&
		intval( $wgUser->getOption( 'stubthreshold' ) ) == 0 &&
		$p->exists() &&
		empty( $oldid ); // FIXME oldid
		wfDebug( 'Post::renderBody using parser cache: ' . ($pcache ? 'yes' : 'no' ) . "\n" );
		if ( $wgUser->getOption( 'stubthreshold' ) ) {
			wfIncrStats( 'pcache_miss_stub' );
		}

		$outputDone = false;
		if ( $pcache ) {
			$outputDone = $wgOut->tryParserCache( $p, $wgUser );
		}

		if (!$outputDone) {
			$wgOut->addHTML('<span style="color: orange;">pasrer cache miss</span>');
			$wgOut->addWikiText($p->mContent);               
		}
	}
        
		function render($p, $is_top_level) {
			global $wgOut, $wgUser, $wgRequest;
			$p->fetchContent();

			$t = $p->mTitle->getPartialURL();

			$movingThis = ( $this->movingId == $p->getID() );

			// Topic header:
			if ( $is_top_level ) {
				// TODO I don't like that we're using wgRequest here.
				if ( $wgRequest->getVal('lqt_topic') )
					$wgOut->addHTML( $wgRequest->getVal('lqt_topic') );
				elseif ( $p->getTopic() )
					$wgOut->addWikiText( '=='.$p->getTopic().'==' );
				else
					$wgOut->addWikiText( '----' );
			}

			$wgOut->addHTML( wfElement('a', array('name'=>"lqt_post_$t"), " " ) );

			if ( $this->highlightingTitle == $p->mTitle->getPartialURL() || $movingThis ) {
				$wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post_highlight')) );
			} else {
				$wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post')) );
			}

			if ( $this->editingId == $p->getID() ) {
				$this->showEditingForm($p, "?lqt_editing={$p->getID()}", $is_top_level );
				
			} elseif ( $this->historyId == $p->getID() ) {
				$this->showPostHistory($p);

			} else {
				$author = User::newFromName($p->originalAuthor(), false);

				$sk = $wgUser->getSkin();

				// Post body:
				$wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post_body')) );
				$this->renderBody($p);
				$wgOut->addHTML( wfCloseElement( 'div') );

				// Begin footer:
				$wgOut->addHTML( wfOpenElement('ul', array('class'=>'lqt_footer')) );

				// Signature:
				$wgOut->addHTML( wfOpenElement( 'li') );
				$wgOut->addWikiText( $author->getSig(), false );
				$wgOut->addHTML( wfCloseElement( 'li') );
				$wgOut->addHTML( wfElement( 'li', null, ($p->isPostModified() ? "Modified" : "Original")) );

				// Edit, reply, move, history, and permalink:
				$edit_href = "{$this->channelName}?lqt_editing={$p->getID()}#lqt_post_$t";
				$wgOut->addHTML( wfOpenElement('li') .
				wfElementClean('a', array('href'=>$edit_href),'Edit') .
				wfCloseElement( 'li') );

				$reply_href = "{$this->channelName}?lqt_replying_to_id={$p->getID()}#lqt_post_$t";
				$wgOut->addHTML( wfOpenElement('li') .
				wfElementClean('a', array('href'=>$reply_href),'Reply') .
				wfCloseElement( 'li') );

				$move_href = "{$this->channelName}?lqt_moving_id={$p->getID()}";
				$wgOut->addHTML( wfOpenElement('li') .
				wfElementClean('a', array('href'=>$move_href),'Move') .
				wfCloseElement( 'li') );

				$history_href = "{$this->channelName}?lqt_show_history_id={$p->getID()}";
				$history_href = $p->getTitle()->getLocalURL("action=history");
				$wgOut->addHTML( wfOpenElement('li') .
				wfElementClean('a', array('href'=>$history_href),'History') .
				wfCloseElement( 'li') );

				$tmp = Thread::baseURL();
				$permalink_href = "$tmp{$p->getTitle()->getPartialURL()}";
				$wgOut->addHTML( wfOpenElement('li') .
				wfElementClean('a', array('href'=>$permalink_href),'Permalink') .
				wfCloseElement( 'li') );

				// End footer:
				$wgOut->addHTML( wfCloseElement('ul') );


			}
			$wgOut->addHTML( wfCloseElement( 'div') );
		}

		function showPostHistory ( $p )
		{
			$ph = new PostHistory( $p );
			$ph->doSubtitle("foo");
			$ph->history();
		}

		/* This function is called as a static in the middle of
		rendering the form in EditPage::showEditForm.  Since it's
		called as static, we pass in the current post with a static
		variable.  What this callback actually does is show the
		'topic' field. Cf. showEditingForm(), below. */
		function topicCallback($wgOut) {
			global $wgRequest;

			$p = ThreadView::$callbackpost;
			$e = ThreadView::$callbackeditpage;

			// If the request already contains the variable we're
			// interested in, this is a preview or somesuch, so we
			// should ues that value. Otherwise, grab the existing
			// value from the database.
			if ( $wgRequest->getVal('lqt_topic') ) {
				$fvalue = $wgRequest->getVal('lqt_topic');
			} elseif ( $p->exists() ) {
				$fvalue = $p->getTopic() ? $p->getTopic() : '';
			} else {
				$fvalue = '';
			}


			// length and maxlength of field are as found in EditPage.
			$wgOut->addHTML( wfOpenElement( 'div', array('class'=>'lqt_topic_field') ) .
			wfLabel( 'Topic:', 'lqt_topic' ) .
			wfInput( 'lqt_topic', 60, $fvalue, array('maxlength'=>'200',
															 'tabindex'=>'0') ) .
			wfCloseElement( 'div' ));

		}
        
        function showEditingForm( $p, $query, $is_top_level ) {
                global $wgRequest, $wgOut;

                $e = new EditPage($p);
                $e->setAction(  $this->baseURL . $this->channelName . $query );
		
		if ( $is_top_level ) {
		     ThreadView::$callbackpost = $p;
		     ThreadView::$callbackeditpage = $e;
		     $e->formCallback = array('ThreadView', 'topicCallback');
		}

                $e->edit();

		// Override editpage's redirect.
                if ($e->mDidRedirect) {
		     $t = $p->getTitle()->getPartialURL();
		     $wgOut->redirect($this->baseURL.$this->channelName.'?lqt_highlight='.$t.'#lqt_post_'.$t);
                }

                // Insert new posts into the threading:
                if ($e->mDidSave && $wgRequest->getVal("lqt_post_new", false)) {
		     $channel_title = Title::newFromText($this->channelName);
		     $p->insertAfter(new Post($channel_title));
                }

                // Insert replies into the threading:
                $replying_to_id = $wgRequest->getVal("lqt_replying_to_id", null);
                if ($e->mDidSave && $replying_to_id) {
		     $reply_to_title = Title::newFromID( $replying_to_id );
		     $p->insertAsReplyTo( new Post($reply_to_title) );
                }

		// Save new topic line if there is one:
		if ( $e->mDidSave && $wgRequest->getVal('lqt_topic') ) {
		     $v = Sanitizer::stripAllTags($wgRequest->getVal('lqt_topic'));
		     $p->setTopic( $v );
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
		     $new_title = Title::newFromText("Post:$it");
                }

                $p = new Post($new_title);
                if ($reply_to) {
		     $this->showEditingForm($p, "?lqt_replying_to_id=$reply_to&lqt_post_title={$new_title->getPartialURL()}",
					    false);
                } else {
		     $this->showEditingForm($p, "?lqt_post_new=1&lqt_post_title={$new_title->getPartialURL()}",
					    true);
                }
        }
        
		/**
		* Recursively tells every post in a thread to render itself.
		* Also invokes showEditingForm as appropriate.
		* @param $post Post The first top-level post in the thread.
		* @param $show_next bool If true, recursively show siblings; otherwise, only show replies underneath starting post.
		*/
		function renderThreadStartingFrom($post, $is_top_level = true, $show_next = true) {

			$this->render( $post, $is_top_level );

			// Button to move a post to be the first reply of this post:
			if ( $this->movingId &&
				 $this->movingId != $post->getID() &&
				($post->firstReply() ? $this->movingId != $post->firstReply()->getID() : true ) ) {
					$this->indent();
					$this->showMoveButton( 'reply', $post->getID() );
					$this->unindent();
			}

			// Show existing replies:
			if (  $post->firstReply() ) {
				$this->indent();
				$this->renderThreadStartingFrom( $post->firstReply(), false );
				$this->unindent();
			}

			// Show reply editing form if we're replying to this post:
			if ( $this->replyingToId == $post->getID() ) {
				$this->indent();
				$this->newPostEditingForm($this->replyingToId);
				$this->unindent();
			}

			// Button to move a post to be the next post of this post:
			if ( $this->movingId &&
				$this->movingId != $post->getID() &&
				($post->nextPost() ? $this->movingId != $post->nextPost()->getID() : true ) ) {
					$this->showMoveButton( 'next', $post->getID() );
			}

			// Show siblings:
			if ( $show_next && $post->nextPost() ) {
				$this->renderThreadStartingFrom( $post->nextPost(), $is_top_level );
			}
		}

        function showMoveButton( $type, $id ) {
                global $wgOut;
                $form_action = $this->baseURL . $this->channelName;
                $wgOut->addHTML(
                        wfOpenElement('form', array('action' => $form_action,
                                                    'method' => 'POST')) .
                        wfHidden("lqt_move_to_$type", "$id") .
                        wfHidden("lqt_move_post_id", $this->movingId) .
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


     
}

?>