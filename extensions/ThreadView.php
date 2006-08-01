<?php

class ThreadView {

     /** hack. see showEditingForm. */
     protected static $callbackpost;
     protected static $callbackeditpage;

     protected static $stuffDoneOnceDone;
     
     /**
      * @param $thread Thread object we're looking at.
      * @param $first_post Only render $first_post and children; if not given, defaults to $thread->firstPost().
      */
     function ThreadView( $talkTitle,
			  $thread,
			  $first_post = null,
			  $editingId = -1,
			  $replyingToId = -1,
			  $historyId = -1,
			  $highlightingTitle = -1,
			  $movingId = -1 ) { /* TODO why the hell title/id? */
	  $this->mThread = $thread;
	  $this->mFirstPost = $first_post ? $first_post : $thread->firstPost();
	  $this->mIsTopLevel = ! $first_post;

	  $this->talkTitle = $talkTitle;
	  $this->editingId = $editingId;
	  $this->historyId = $historyId;
	  $this->replyingToId = $replyingToId;
	  $this->highlightingTitle = $highlightingTitle;
	  $this->movingId = $movingId;

	  $this->showNext = true;
     }

     function doStuffOnce() {
	  global $wgOut, $wgJsMimeType, $wgStylePath;

	  if (ThreadView::$stuffDoneOnceDone) return;
	  ThreadView::$stuffDoneOnceDone = true;

	  $s = "<script type=\"{$wgJsMimeType}\" src=\"{$wgStylePath}/common/lqt.js\"><!-- lqt js --></script>\n";
	  $wgOut->addScript($s);

	  $h = $wgOut->getOnloadHandler();
	  if ( $h != '' ) {
	       $h .= '; ';
	  }
	  $h .= 'lqt_on_load();';
	  $wgOut->setOnloadHandler($h);
     }

     function render() {
	  global $wgOut;

	  $this->doStuffOnce();

	  // Instruct Parser not to include section edit links.
	  $wgOut->mParserOptions->setEditSection(false);

	  $thread_id_attrib = 'lqt_thread_' . $this->mThread->getID();

	  $wgOut->addHTML("\n\n\n");

	  // Subject header:
	  if ( $this->mIsTopLevel ) {
	       if ( $this->mThread->mSubject )
		    $wgOut->addHTML( wfElement('h2', array('class'=>'lqt_thread_subject_header',
							   'onclick'=>"lqt_hide_show('$thread_id_attrib')"),
					       $this->mThread->mSubject) );
	       else
		    $wgOut->addWikiText( '----' );
	  }
	  
	  $wgOut->addHTML('<a name="'.$thread_id_attrib.'"></a><div class="lqt_thread" id="'.$thread_id_attrib.'">');
	  if( $this->mFirstPost ) {
	       $this->renderStartingFrom( $this->mFirstPost );
	  }
	  $wgOut->addHTML('</div>');

	  $wgOut->addHTML("\n\n\n");
     }


     protected function renderStartingFrom($post) {
	  $this->renderPost($post);

	  // Render replies:
	  if ( $post->firstReply() ) {
	       $this->indent();
	       $this->renderStartingFrom($post->firstReply());
	       $this->unindent();
	  }


	  // Show reply editing form if we're replying to this post:
	  if ( $this->replyingToId == $post->getID() ) {
	       $this->indent();
	       $this->replyForm( $this->replyingToId );
	       $this->unindent();
	  }


	  // Render siblings:
	  if ( $this->showNext && $post->nextPost() ) {
	       $this->renderStartingFrom($post->nextPost());
	  }


     }

     
     function renderPost($p) {
	  global $wgOut, $wgUser, $wgRequest;
	  $p->fetchContent();

	  $t = $p->mTitle->getPartialURL();

	  $movingThis = ( $this->movingId == $p->getID() );

	  $wgOut->addHTML( wfElement('a', array('name'=>"lqt_post_$t"), " " ) );

	  if ( $this->highlightingTitle == $p->mTitle->getPartialURL() || $movingThis ) {
	       $wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post_highlight')) );
	  } else {
	       $wgOut->addHTML( wfOpenElement('div', array('class'=>'lqt_post')) );
	  }

	  $is_top_level = ( $this->mThread->firstPost()->getID() == $p->getID() );
	  
	  if ( $p->isDeleted() && !$wgRequest->getVal('lqt_show_deleted', false) ) {
	       // Render deleted posts:
	       $author = $p->originalAuthor();
	       $show_href = $this->talkTitle->getLocalURL('lqt_show_deleted=true&lqt_highlight='.$t.'#lqt_post_'.$t);
	       $wgOut->addHTML( '<span class="lqt_deleted_notice">Deleted post by '.$author.'.<a href='.$show_href.'>Show</a></span>' );
	  }
	  elseif ( $this->editingId == $p->getID() ) {
	       $this->editForm( $p );
//	       $this->showEditingForm($p, "lqt_editing={$p->getID()}",  $is_top_level);
				
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
	       $edit_href = $this->talkTitle->getLocalURL( "lqt_editing={$p->getID()}#lqt_post_$t" );
	       $wgOut->addHTML( wfOpenElement('li') .
				wfElementClean('a', array('href'=>$edit_href),'Edit') .
				wfCloseElement( 'li') );

	       $reply_href = $this->talkTitle->getLocalURL( "lqt_replying_to_id={$p->getID()}#lqt_post_$t" );
	       $wgOut->addHTML( wfOpenElement('li') .
				wfElementClean('a', array('href'=>$reply_href),'Reply') .
				wfCloseElement( 'li') );

	       $move_href = $this->talkTitle->getLocalURL( "lqt_moving_id={$p->getID()}" );
	       $wgOut->addHTML( wfOpenElement('li') .
				wfElementClean('a', array('href'=>$move_href),'Move') .
				wfCloseElement( 'li') );

	       if ($p->isDeleted()) {
		    $delete_href = $this->talkTitle->getLocalURL( "lqt_do_undelete_id={$p->getID()}" );
		    $delete_message = "Undelete";
	       } else {
		    $delete_href = $this->talkTitle->getLocalURL( "lqt_do_delete_id={$p->getID()}" );
		    $delete_message = "Delete";
	       }
	       $wgOut->addHTML( wfOpenElement('li') .
				wfElementClean('a', array('href'=>$delete_href), $delete_message) .
				wfCloseElement('li') );

	       $permalinkTitle = Title::makeTitle( LQT_NS_THREAD, $p->getTitle()->getDBkey() );
	       $permalink_href = $permalinkTitle->getLocalURL();
	       $wgOut->addHTML( wfOpenElement('li') .
				wfElementClean('a', array('href'=>$permalink_href),'Permalink') .
				wfCloseElement( 'li') );

	       // End footer:
	       $wgOut->addHTML( wfCloseElement('ul') );


	  }
	  $wgOut->addHTML( wfCloseElement( 'div') );
     }

     
     
     /**
      * Render the article content, fetching from page cache if possible.
      * @private
      */
     function renderBody($p)
     {
	  global $wgOut, $wgUser, $wgEnableParserCache;

          // Should the parser cache be used?
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
	       // Cache miss; parse and output it.
	       $wgOut->addWikiText($p->mContent);
	  }
     }

     /**
      * Output any HTML that is to come right before we tell our reply
      * comments to render themselves.
      */
     protected function indent( ) {
	  global $wgOut;
	  $wgOut->addHTML( wfOpenElement( 'dl', array('class'=>'lqt_replies') ) );
	  $wgOut->addHTML( wfOpenElement( 'dd') );
     }

     /**
      * Output any HTML that is to come right after we tell our reply
      * comments to render themselves.
      */
     protected function unindent( ) {
	  global $wgOut;
	  $wgOut->addHTML( wfCloseElement( 'dd') );
	  $wgOut->addHTML( wfCloseElement( 'dl') );
     }

     /**
      * @param ID of the post that this is a reply to, or else null if it's not a reply.
      */
     function newPostEditingForm($talkTitle, $article, $reply_to=null) {
	  global $wgRequest;
	  if ( $it = $wgRequest->getVal("lqt_post_title", false) ) {
	       $new_title = Title::newFromText("Post:$it");
	  } else {
	       $token = md5(uniqid(rand(), true));
	       $new_title = Title::newFromText( "Post:$token" );
	  }
	  $p = new Post($new_title);

	  // Make a new, dummy threadview to render the form, since it isn't associated with an actual
	  // thread at this point. the $first_post arguement equals 'true' because if it's null,
	  // the constructor will try to pull a default out of $thread, which is also null.
	  // TODO: make this not a mess.
	  $view = new ThreadView( $talkTitle, null, true );
	  if ($reply_to) {
	       $view->showEditingForm($p, "lqt_replying_to_id=$reply_to&lqt_post_title={$new_title->getPartialURL()}",
				      false, $article);
	  } else {
	       $view->showEditingForm($p, "lqt_post_new=1&lqt_post_title={$new_title->getPartialURL()}",
				      true, $article);
	  }
     }

     /**  EditPage::edit() is self-submitting, and so, so is this. */
     function showEditingForm( $p, $query, $is_top_level, $article = null ) {
	  global $wgRequest, $wgOut;

//	  echo $this->talkTitle->getFullURL($query);

	  $e = new EditPage($p);
	  $e->setAction( $this->talkTitle->getFullURL($query) );

	  if ( $is_top_level ) {
	       ThreadView::$callbackpost = $p;
	       ThreadView::$callbackeditpage = $e;
	       $e->formCallback = array('ThreadView', 'topicCallback');
	  }

	  $e->edit();

	  // override what happens in EditPage::showEditForm, called from $e->edit():
	  $wgOut->setArticleRelated( false ); 
	  $wgOut->setArticleFlag( false ); 

	  // Override editpage's redirect.
	  if ($e->mDidRedirect) {
	       $t = $p->getTitle()->getPartialURL();
	       $wgOut->redirect( $this->talkTitle->getFullURL( "lqt_highlight=$t#lqt_post_$t" ) );
	  }
	       
	  // Create a new thread if needed:
	  if ($e->mDidSave && $wgRequest->getVal("lqt_post_new", false)) {
	       $new_thread = Thread::insertNewThread( $article, $p );
	       $p->setThread($new_thread);
	  } else if ($e->mDidSave) {
	       $p->setThread($this->mThread);
	       $new_thread = $this->mThread;
	  } else {
	       $new_thread = $this->mThread;
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
	       $new_thread->setSubject($v);
	  }

     }

     static function newThreadForm( $article, $talkTitle ) {
	  global $wgRequest, $wgOut;

	  if ( $it = $wgRequest->getVal("lqt_post_title", false) ) {
	       $new_title = Title::newFromText("Post:$it");
	  } else {
	       $token = md5(uniqid(rand(), true));
	       $new_title = Title::newFromText( "Post:$token" );
	  }
	  $p = new Post($new_title);

	  $e = new EditPage($p);
	  $e->setAction($talkTitle->getFullURL( "lqt_post_new=1&lqt_post_title={$new_title->getPartialURL()}" ));

	  // Topc field:
	  ThreadView::$callbackpost = $p;
	  ThreadView::$callbackeditpage = $e;
	  $e->formCallback = array('ThreadView', 'topicCallback');

	  $e->edit();
	  
	  // override what happens in EditPage::showEditForm, called from $e->edit():
	  $wgOut->setArticleRelated( false ); 
	  $wgOut->setArticleFlag( false ); 

	  // Override editpage's redirect.
	  if ($e->mDidRedirect) {
	       $t = $p->getTitle()->getPartialURL();
	       $wgOut->redirect( $talkTitle->getFullURL( "lqt_highlight=$t#lqt_post_$t" ) );
	  }

	  // Create the new thread:
	  if ($e->mDidSave) {
	       $new_thread = Thread::insertNewThread( $article, $p );
	       $p->setThread($new_thread);
	  }

	  // Save new topic line if there is one:
	  if ( $e->mDidSave && $wgRequest->getVal('lqt_topic') ) {
	       $v = Sanitizer::stripAllTags($wgRequest->getVal('lqt_topic'));
	       $new_thread->setSubject($v);
	  }
     }

     /** Self-submitting edit form for use when REPLYING */
     function replyForm( $reply_to ) {
	  global $wgRequest, $wgOut;

	  if ( $it = $wgRequest->getVal("lqt_post_title", false) ) {
	       $new_title = Title::newFromText("Post:$it");
	  } else {
	       $token = md5(uniqid(rand(), true));
	       $new_title = Title::newFromText( "Post:$token" );
	  }
	  $p = new Post($new_title);

	  $e = new EditPage($p);
	  $e->setAction($this->talkTitle->getFullURL("lqt_replying_to_id=$reply_to&lqt_post_title={$new_title->getPartialURL()}"));

	  $e->edit();
	  
	  // override what happens in EditPage::showEditForm, called from $e->edit():
	  $wgOut->setArticleRelated( false ); 
	  $wgOut->setArticleFlag( false ); 

	  // Override editpage's redirect.
	  if ($e->mDidRedirect) {
	       $t = $p->getTitle()->getPartialURL();
	       $wgOut->redirect( $this->talkTitle->getFullURL( "lqt_highlight=$t#lqt_post_$t" ) );
	  }
	  
	  if ($e->mDidSave) {
	       $p->setThread($this->mThread);
	  }

          // Insert replies into the threading:
	  $replying_to_id = $wgRequest->getVal("lqt_replying_to_id", null);
	  if ($e->mDidSave && $replying_to_id) {
	       $reply_to_title = Title::newFromID( $replying_to_id );
	       $p->insertAsReplyTo( new Post($reply_to_title) );
	  }
	  
     }

     /** Self-submitting edit form for use when EDITING */
     function editForm( $p ) {
	  global $wgRequest, $wgOut;

	  $e = new EditPage($p);
	  $e->setAction( $this->talkTitle->getFullURL( "lqt_editing={$p->getID()}" ) );

	  if ( $p->thread()->firstPost()->getID() == $p->getID() ) {
	       // This is the thread's root post; display topic field.
	       ThreadView::$callbackpost = $p;
	       ThreadView::$callbackeditpage = $e;
	       $e->formCallback = array('ThreadView', 'topicCallback');
	  }

	  $e->edit();

	  // Override what happens in EditPage::showEditForm, called from $e->edit():
	  $wgOut->setArticleRelated( false ); 
	  $wgOut->setArticleFlag( false );

	  // Override editpage's redirect.
	  if ($e->mDidRedirect) {
	       $t = $p->getTitle()->getPartialURL();
	       $wgOut->redirect( $this->talkTitle->getFullURL( "lqt_highlight=$t#lqt_post_$t" ) );
	  }

	  // Save new topic line if there is one:
	  if ( $e->mDidSave && $wgRequest->getVal('lqt_topic') ) {
	       $v = Sanitizer::stripAllTags($wgRequest->getVal('lqt_topic'));
	       $p->thread()->setSubject($v);
	  }
     }

     /* This function is called as a static in the middle of
      rendering the form in EditPage::showEditForm.  Since it's
      called as static, we pass in the current post with a static
      variable.  What this callback actually does is show the
      'topic' field. Cf. showEditingForm(), above. */
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
	       $fvalue = $p->thread()->subject() ? $p->thread()->subject() : '';
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

}

?>