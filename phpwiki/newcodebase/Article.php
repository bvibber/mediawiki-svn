<?
# See design.doc

class Article {
	/* private */ var $mContent, $mContentLoaded;
	/* private */ var $mUser, $mTimestamp, $mUserText;
	/* private */ var $mCounter, $mComment, $mCountAdjustment;
	/* private */ var $mMinorEdit, $mRedirectedFrom;

	function Article() { $this->clear(); }

	/* private */ function clear()
	{
		$this->mContentLoaded = false;
		$this->mUser = $this->mCounter = -1; # Not loaded
		$this->mRedirectedFrom = $this->mUserText =
		$this->mTimestamp = $this->mComment = "";
		$this->mCountAdjustment = 0;
	}

	/* static */ function newFromID( $newid )
	{
		global $wgOut, $wgTitle, $wgArticle;
		$a = new Article();
		$n = Article::nameOf( $newid );

		$wgTitle = Title::newFromDBkey( $n );
		$wgTitle->resetArticleID( $newid );
		wfFreeResult( $res );

		return $a;
	}

	/* static */ function nameOf( $id )
	{
		$sql = "SELECT cur_namespace,cur_title FROM cur WHERE " .
		  "cur_id={$id}";
		$res = wfQuery( $sql, "Article::newFromID" );
		if ( 0 == wfNumRows( $res ) ) { return NULL; }

		$s = wfFetchObject( $res );
		$n = Title::makeName( $s->cur_namespace, $s->cur_title );
		return $n;
	}

	function getContent()
	{
		global $action;

		if ( 0 == $this->getID() ) {
			if ( "edit" == $action ) {
				return wfMsg( "newarticletext" );
			}
			return wfMsg( "noarticletext" );
		} else {
			$this->loadContent();
			return $this->mContent;
		}
	}

	function loadContent()
	{
		global $wgOut, $wgTitle, $oldid, $redirect;
		if ( $this->mContentLoaded ) return;
		$fname = "Article::loadContent";

		$t = $wgTitle->getPrefixedText();
		if ( $oldid ) { $t .= ",oldid={$oldid}"; }
		if ( $redirect ) { $t .= ",redirect={$redirect}"; }
		$this->mContent = str_replace( "$1", $t, wfMsg( "missingarticle" ) );

		if ( ! $oldid ) {
			$id = $this->getID();
			if ( 0 == $id ) return;

			$sql = "SELECT cur_text,cur_timestamp,cur_user,cur_counter " .
			  "FROM cur WHERE cur_id={$id}";
			$res = wfQuery( $sql, $fname );
			if ( 0 == wfNumRows( $res ) ) { return; }

			$s = wfFetchObject( $res );
			if ( ( "no" != $redirect ) &&
			  ( preg_match( "/^#redirect/i", $s->cur_text ) ) ) {
				if ( preg_match( "/\\[\\[([^\\]\\|]+)[\\]\\|]/",
				  $s->cur_text, $m ) ) {
					$rt = Title::newFromText( $m[1] );
					$rid = $rt->getArticleID();
					if ( 0 != $rid ) {
						$sql = "SELECT cur_text,cur_timestamp,cur_user," .
						  "cur_counter FROM cur WHERE cur_id={$rid}";
						$res = wfQuery( $sql, $fname );

						if ( 0 != wfNumRows( $res ) ) {
							$this->mRedirectedFrom = $wgTitle->getPrefixedText();
							$wgTitle = $rt;
							$s = wfFetchObject( $res );
						}
					}
				}
			}
			$this->mContent = $s->cur_text;
			$this->mUser = $s->cur_user;
			$this->mCounter = $s->cur_counter;
			$this->mTimestamp = $s->cur_timestamp;
			wfFreeResult( $res );
		} else {
			$sql = "SELECT old_text,old_timestamp,old_user FROM old " .
			  "WHERE old_id={$oldid}";
			$res = wfQuery( $sql, $fname );
			if ( 0 == wfNumRows( $res ) ) { return; }

			$s = wfFetchObject( $res );
			$this->mContent = $s->old_text;
			$this->mUser = $s->old_user;
			$this->mCounter = 0;
			$this->mTimestamp = $s->old_timestamp;
			wfFreeResult( $res );
		}
		$this->mContentLoaded = true;
	}

	function getID() { global $wgTitle; return $wgTitle->getArticleID(); }

	function getCount()
	{
		if ( -1 == $this->mCounter ) {
			$id = $this->getID();
			$this->mCounter = wfGetSQL( "cur", "cur_counter", "cur_id={$id}" );
		}
		return $this->mCounter;
	}

	# Would the given text make this article a "good" article (i.e.,
	# suitable for including in the article count)?
	#
	function isCountable( $text )
	{
		global $wgTitle;

		if ( 0 != $wgTitle->getNamespace() ) { return 0; }
		if ( preg_match( "/^#redirect/i", $text ) ) { return 0; }
		if ( false === strstr( $text, "," ) ) { return 0; }
		return 1;
	}

	/* private */ function loadLastEdit()
	{
		global $wgOut;
		if ( -1 != $this->mUser ) return;

		$sql = "SELECT cur_user,cur_user_text,cur_timestamp," .
		  "cur_comment,cur_minor_edit FROM cur WHERE " .
		  "cur_id=" . $this->getID();
		$res = wfQuery( $sql, "Article::loadLastEdit" );

		if ( wfNumRows( $res ) > 0 ) {
			$s = wfFetchObject( $res );
			$this->mUser = $s->cur_user;
			$this->mUserText = $s->cur_user_text;
			$this->mTimestamp = $s->cur_timestamp;
			$this->mComment = $s->cur_comment;
			$this->mMinorEdit = $s->cur_minor_edit;
		}
	}

	function getTimestamp()
	{
		$this->loadLastEdit();
		return $this->mTimestamp;
	}

	function getUser()
	{
		$this->loadLastEdit();
		return $this->mUser;
	}

	function getUserText()
	{
		$this->loadLastEdit();
		return $this->mUserText;
	}

	function getComment()
	{
		$this->loadLastEdit();
		return $this->mComment;
	}

	function getMinorEdit()
	{
		$this->loadLastEdit();
		return $this->mMinorEdit;
	}

	function view()
	{
		global $wgUser, $wgOut, $wgTitle, $wgLang;
		global $oldid, $diff;

		# $wgOut->addHeader( "Expires", $wgLang->rfc1123( time() + 3600 ) );
		# $wgOut->addHeader( "Cache-Control", "private" );

		$wgOut->setArticleFlag( true );
		$wgOut->setRobotpolicy( "index,follow" );

		if ( isset( $diff ) ) {
			$wgOut->setPageTitle( $wgTitle->getPrefixedText() );
			$de = new DifferenceEngine( $oldid, $diff );
			$de->showDiffPage();
			return;
		}
		$text = $this->getContent();
		$wgOut->setPageTitle( $wgTitle->getPrefixedText() );

		if ( $oldid ) {
			$this->setOldSubtitle();
			$wgOut->setRobotpolicy( "noindex,follow" );
		}

		if ( "" != $this->mRedirectedFrom ) {
			$sk = $wgUser->getSkin();
			$redir = $sk->makeKnownLink( $this->mRedirectedFrom, "",
			  "redirect=no" );
			$s = str_replace( "$1", $redir, wfMsg( "redirectedfrom" ) );
			$wgOut->setSubtitle( $s );
		}
		$wgOut->addWikiText( $text );
		
		$ins = Namespace::getIndex( "Image" );
		if ( $ins == $wgTitle->getNamespace() ) {
			$this->imageHistory();
			$this->imageLinks();
		}
		$this->viewUpdates();
	}

	function edit()
	{
		global $wgOut, $wgUser, $wgTitle;
		global $wpTextbox1, $wpSummary, $wpSave, $wpPreview;
		global $wpMinoredit, $wpEdittime, $wpTextbox2;

		$fields = array( "wpTextbox1", "wpSummary", "wpTextbox2" );
		wfCleanFormFields( $fields );

		if ( ! $wgTitle->userCanEdit() ) {
			$this->view();
			return;
		}
		if ( $wgUser->isBlocked() ) {
			$this->blockedIPpage();
			return;
		}
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		if ( isset( $wpSave ) ) {
			$this->editForm( "save" );
		} else if ( isset( $wpPreview ) ) {
			$this->editForm( "preview" );
		} else { # First time through
			$this->editForm( "initial" );
		}
	}

	function submit()
	{
		global $wpSave, $wpPreview;
		if ( ! isset( $wpPreview ) ) { $wpSave = 1; }

		$this->edit();
	}

	function editForm( $formtype )
	{
		global $wgOut, $wgUser, $wgTitle;
		global $wpTextbox1, $wpSummary, $wpSave, $wpPreview;
		global $wpMinoredit, $wpEdittime, $wpTextbox2, $wpCountable;
		global $oldid, $redirect;

		$sk = $wgUser->getSkin();
		$isConflict = false;
		if ( "save" == $formtype ) {
			if ( $wgUser->isBlocked() ) {
				$this->blockedIPpage();
				return;
			}
			$aid = $wgTitle->getArticleID();
			if ( 0 == $aid ) {
				if ( ( "" == $wpTextbox1 ) ||
				  ( wfMsg( "newarticletext" ) == trim( $wpTextbox1 ) ) ) {
					$wgOut->redirect(  wfLocalUrl(
					  $wgTitle->getPrefixedURL() ) );
					return;
				}
				$this->mCountAdjustment = $this->isCountable( $wpTextbox1 );
				$this->insertNewArticle( $wpTextbox1, $wpSummary, $wpMinoredit );
				return;
			}
			# Check for edit conflict. TODO: check oldid here?
			#
			$this->clear(); # Force reload
			if ( $this->getTimestamp() > $wpEdittime ) { $isConflict = true; }
			$u = $wgUser->getID();
			if ( ( 0 != $u ) && ( $this->getUser() == $u ) ) {
				$isConflict = false;
			}
			if ( ! $isConflict ) {
				# All's well: save the article here
				$this->mCountAdjustment = $this->isCountable( $wpTextbox1 ) -
				  $wpCountable;
				$this->updateArticle( $wpTextbox1, $wpSummary, $wpMinoredit );
				return;
			}
		}
		if ( "initial" == $formtype ) {
			$wpEdittime = $this->getTimestamp();
			$wpTextbox1 = $this->getContent();
			$wpCountable = $this->isCountable( $wpTextbox1 );
			$wpSummary = "";
		}
		$wgOut->setRobotpolicy( "noindex,nofollow" );
		$wgOut->setArticleFlag( false );

		if ( $isConflict ) {
			$s = str_replace( "$1", $wgTitle->getPrefixedText(),
			  wfMsg( "editconflict" ) );
			$wgOut->setPageTitle( $s );
			$wgOut->addHTML( wfMsg( "explainconflict" ) );

			$wpTextbox2 = $wpTextbox1;
			$wpTextbox1 = $this->getContent();
			$wpEdittime = $this->getTimestamp();
		} else {
			$s = str_replace( "$1", $wgTitle->getPrefixedText(),
			  wfMsg( "editing" ) );
			$wgOut->setPageTitle( $s );
			if ( $oldid ) {
				$this->setOldSubtitle();
				$wgOut->addHTML( wfMsg( "editingold" ) );
			}
		}
		$rows = $wgUser->getOption( "rows" );
		$cols = $wgUser->getOption( "cols" );

		$q = "action=submit";
		if ( "no" == $redirect ) { $q .= "&redirect=no"; }
		$action = wfEscapeHTML( wfLocalUrl( $wgTitle->getPrefixedURL(), $q ) );

		$summary = wfMsg( "summary" );
		$minor = wfMsg( "minoredit" );
		$save = wfMsg( "savearticle" );
		$prev = wfMsg( "showpreview" );

		$cancel = $sk->makeKnownLink( $wgTitle->getPrefixedURL(),
		  wfMsg( "cancel" ) );
		$edithelp = $sk->makeKnownLink( wfMsg( "edithelppage" ),
		  wfMsg( "edithelp" ) );
		$copywarn = str_replace( "$1", $sk->makeKnownLink(
		  wfMsg( "copyrightpage" ) ), wfMsg( "copyrightwarning" ) );

		$wpTextbox1 = wfEscapeHTML( $wpTextbox1 );
		$wpTextbox2 = wfEscapeHTML( $wpTextbox2 );
		$wpSummary = wfEscapeHTML( $wpSummary );

		$wgOut->addHTML( "
<form method=post action=\"$action\"
enctype='application/x-www-form-urlencoded'>
<textarea tabindex=1 name='wpTextbox1' rows={$rows} cols={$cols} wrap=virtual>
{$wpTextbox1}
</textarea><br>
{$summary}: <input tabindex=2 type=text value=\"{$wpSummary}\" name='wpSummary' maxlength=200>
<input tabindex=3 type=checkbox value=1 name='wpMinoredit'>{$minor}<br>
<input tabindex=4 type=submit value=\"{$save}\" name='wpSave'>
<input tabindex=5 type=submit value=\"{$prev}\" name='wpPreview'>
<em>{$cancel}</em> | <em>{$edithelp}</em>
<br><br>{$copywarn}
<input type=hidden value=\"{$wpEdittime}\" name='wpEdittime'>
<input type=hidden value=\"{$wpCountable}\" name='wpCountable'>\n" );

		if ( $isConflict ) {
			$wgOut->addHTML( "<h2>" . wfMsg( "yourdiff" ) . "</h2>\n" );
			DifferenceEngine::showDiff( $wpTextbox2, $wpTextbox1,
			  wfMsg( "yourtext" ), wfMsg( "storedversion" ) );

			$wgOut->addHTML( "<h2>" . wfMsg( "yourtext" ) . "</h2>
<textarea tabindex=6 name='wpTextbox2' rows={$rows} cols={$cols} wrap=virtual>
{$wpTextbox2}
</textarea>" );
		}
		$wgOut->addHTML( "</form>\n" );

		if ( "preview" == $formtype ) {
			$wgOut->addHTML( "<h2>" . wfMsg( "preview" ) . "</h2>\n" );
			if ( $isConflict ) {
				$wgOut->addHTML( "<h2>" . wfMsg( "previewconflict" ) .
				  "</h2>\n" );
			}
			$previewtext = wfUnescapeHTML( $wpTextbox1 );
			$wgOut->addWikiText( $this->preSaveTransform( $previewtext ) );
			$wgOut->addHTML( "<p><large>" . wfMsg( "note" ) .
			  wfMsg( "previewnote" ) . "</large>\n" );
		}
	}

	# Theoretically we could defer these whole insert and update
	# functions for after display, but that's taking a big leap
	# leap of faith, and I want to be able to report database
	# errors at some point.
	#
	/* private */ function insertNewArticle( $text, $summary, $isminor )
	{
		global $wgOut, $wgUser, $wgTitle, $wgLinkCache;
		$ns = $wgTitle->getNamespace();
		$ttl = $wgTitle->getDBkey();
		$text = $this->preSaveTransform( $text );
		if ( preg_match( "/^#redirect/i", $text ) ) { $redir = 1; }
		else { $redir = 0; }

		$sql = "INSERT INTO cur (cur_namespace,cur_title,cur_text," .
		  "cur_comment,cur_user,cur_timestamp,cur_minor_edit,cur_counter," .
		  "cur_restrictions,cur_ind_title,cur_user_text,cur_is_redirect," .
		  "cur_is_new) VALUES ({$ns},'" . wfStrencode( $ttl ) . "', '" .
		  wfStrencode( $text ) . "', '" .
		  wfStrencode( $summary ) . "', '" .
		  $wgUser->getID() . "', '" . date( "YmdHis" ) . "', " .
		  ( $isminor ? 1 : 0 ) . ", 0, '', '" .
		  wfStrencode( $wgTitle->getIndexTitle() ) .
		  "', '" . wfStrencode( $wgUser->getName() ) . "', $redir, 1)";
		$res = wfQuery( $sql, "Article::insertNewArticle" );

		$newid = wfInsertId();
		$wgTitle->resetArticleID( $newid );

		$this->showArticle( $text, wfMsg( "newarticle" ) );
	}

	function updateArticle( $text, $summary, $minor )
	{
		global $wgOut, $wgUser, $wgTitle, $wgLinkCache;
		$fname = "Article::updateArticle";

		if ( $this->mMinorEdit ) { $me1 = 1; } else { $me1 = 0; }
		if ( $minor ) { $me2 = 1; } else { $me2 = 0; }
		if ( preg_match( "/^#redirect/i", $text ) ) { $redir = 1; }
		else { $redir = 0; }
		$this->loadLastEdit();

		$text = $this->preSaveTransform( $text );

		if ( 0 != strcmp( $text, $this->getContent() ) ) {
			$sql = "INSERT INTO old (old_namespace,old_title,old_text," .
			  "old_comment,old_user,old_user_text,old_timestamp," .
			  "old_minor_edit) VALUES (" .
			  $wgTitle->getNamespace() . ", '" .
			  wfStrencode( $wgTitle->getDBkey() ) . "', '" .
			  wfStrencode( $this->getContent() ) . "', '" .
			  wfStrencode( $this->getComment() ) . "', " .
			  $this->getUser() . ", '" .
			  wfStrencode( $this->getUserText() ) . "', '" .
			  $this->getTimestamp() . "', " . $me1 . ")";
			$res = wfQuery( $sql, $fname );

			$sql = "UPDATE cur SET cur_text='" .  wfStrencode( $text ) .
			  "',cur_comment='" .  wfStrencode( $summary ) .
			  "',cur_minor_edit={$me2}, cur_user=" . $wgUser->getID() .
			  ",cur_timestamp='" . date( "YmdHis" ) .
			  "',cur_user_text='" . wfStrencode( $wgUser->getName() ) .
			  "',cur_is_redirect={$redir}, cur_is_new=0 " .
			  "WHERE cur_id=" . $this->getID();
			$res = wfQuery( $sql, $fname );
		}
		$this->showArticle( $text, wfMsg( "updated" ) );
	}

	function showArticle( $text, $subtitle )
	{
		global $wgOut, $wgTitle, $wgUser, $wgLinkCache;

		$wgLinkCache = new LinkCache();
		$wgOut->addWikiText( $text ); # Just to update links

		$this->editUpdates( $this->getID(), $wgTitle->getPrefixedDBkey(),
		  $text, $this->mCountAdjustment );
		$wgOut->redirect(  wfLocalUrl( $wgTitle->getPrefixedURL() ) );
	}

	function imageHistory()
	{
		global $wgUser, $wgOut, $wgLang, $wgTitle;
		$fname = "Article::imageHistory";

		$sql = "SELECT img_size,img_description,img_user," .
		  "img_user_text,img_timestamp FROM image WHERE " .
		  "img_name='" . wfStrencode( $wgTitle->getDBkey() ) . "'";
		$res = wfQuery( $sql, $fname );

		if ( 0 == wfNumRows( $res ) ) { return; }

		$sk = $wgUser->getSkin();
		$s = $sk->beginImageHistoryList();		

		$line = wfFetchObject( $res );
		$s .= $sk->imageHistoryLine( true, $line->img_timestamp,
		  $wgTitle->getText(),  $line->img_user,
		  $line->img_user_text, $line->img_size, $line->img_description );

		$sql = "SELECT oi_size,oi_description,oi_user," .
		  "oi_user_text,oi_timestamp,oi_archive_name FROM oldimage WHERE " .
		  "oi_name='" . wfStrencode( $wgTitle->getDBkey() ) . "' " .
		  "ORDER BY oi_timestamp DESC";
		$res = wfQuery( $sql, $fname );

		while ( $line = wfFetchObject( $res ) ) {
			$s .= $sk->imageHistoryLine( false, $line->oi_timestamp,
			  $line->oi_archive_name, $line->oi_user,
			  $line->oi_user_text, $line->oi_size, $line->oi_description );
		}
		$s .= $sk->endImageHistoryList();
		$wgOut->addHTML( $s );
	}

	function imageLinks()
	{
		global $wgUser, $wgOut, $wgTitle;

		$wgOut->addHTML( "<h2>" . wfMsg( "imagelinks" ) . "</h2>\n" );

		$sql = "SELECT il_from FROM imagelinks WHERE il_to='" .
		  wfStrencode( $wgTitle->getDBkey() ) . "'";
		$res = wfQuery( $sql, "Article::imageLinks" );

		if ( 0 == wfNumRows( $res ) ) {
			$wgOut->addHtml( "<p>" . wfMsg( "nolinkstoimage" ) . "\n" );
			return;
		}
		$wgOut->addHTML( "<p>" . wfMsg( "linkstoimage" ) .  "\n<ul>" );

		$sk = $wgUser->getSkin();
		while ( $s = wfFetchObject( $res ) ) {
			$name = $s->il_from;
			$link = $sk->makeKnownLink( $name, "" );
			$wgOut->addHTML( "<li>{$link}</li>\n" );
		}
		$wgOut->addHTML( "</ul>\n" );
	}

	function watch()
	{
		global $wgUser, $wgTitle, $wgOut, $wgLang;
		global $wgDeferredUpdateList;

		if ( 0 == $wgUser->getID() ) {
			$wgOut->errorpage( "watchnologin", "watchnologintext" );
			return;
		}
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		$wgUser->addWatch( $wgTitle->getPrefixedDBkey() );

		$wgOut->setPagetitle( wfMsg( "addedwatch" ) );
		$wgOut->setRobotpolicy( "noindex,follow" );

		$text = str_replace( "$1", $wgTitle->getPrefixedText(),
		  wfMsg( "addedwatchtext" ) );
		$wgOut->addHTML( $text );

		$up = new UserUpdate();
		array_push( $wgDeferredUpdateList, $up );

		$wgOut->returnToMain( false );
	}

	function unwatch()
	{
		global $wgUser, $wgTitle, $wgOut, $wgLang;
		global $wgDeferredUpdateList;

		if ( 0 == $wgUser->getID() ) {
			$wgOut->errorpage( "watchnologin", "watchnologintext" );
			return;
		}
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		$wgUser->removeWatch( $wgTitle->getPrefixedDBkey() );

		$wgOut->setPagetitle( wfMsg( "removedwatch" ) );
		$wgOut->setRobotpolicy( "noindex,follow" );

		$text = str_replace( "$1", $wgTitle->getPrefixedText(),
		  wfMsg( "removedwatchtext" ) );
		$wgOut->addHTML( $text );

		$up = new UserUpdate();
		array_push( $wgDeferredUpdateList, $up );

		$wgOut->returnToMain( false );
	}

	# This shares a lot of issues (and code) with Recent Changes
	#
	function history()
	{
		global $wgUser, $wgOut, $wgLang, $wgTitle;

		$wgOut->setPageTitle( $wgTitle->getPRefixedText() );
		$wgOut->setSubtitle( wfMsg( "revhistory" ) );
		$wgOut->setArticleFlag( false );
		$wgOut->setRobotpolicy( "noindex,nofollow" );

		$sql = "SELECT old_id,old_namespace,old_title,old_user," .
		  "old_comment,old_user_text,old_timestamp,old_minor_edit FROM old " .
		  "WHERE old_namespace=" . $wgTitle->getNamespace() . " AND " .
		  "old_title='" . wfStrencode( $wgTitle->getDBkey() ) . "' " .
		  "ORDER BY old_timestamp DESC";
		$res = wfQuery( $sql, "Article::history" );

		$revs = wfNumRows( $res );
		$sk = $wgUser->getSkin();
		$s = $sk->beginHistoryList();		

		$s .= $sk->historyLine( $this->getTimestamp(), $this->getUser(),
		  $this->getUserText(), $wgTitle->getNamespace(),
		  $wgTitle->getText(), 0, $this->getComment(),
		  ( $this->getMinorEdit() > 0 ) );

		while ( $revs ) {
			$line = wfFetchObject( $res );

			$s .= $sk->historyLine( $line->old_timestamp, $line->old_user,
			  $line->old_user_text, $line->old_namespace,
			  $line->old_title, $line->old_id,
			  $line->old_comment, ( $line->old_minor_edit > 0 ) );
			--$revs;
		}
		$s .= $sk->endHistoryList();
		$wgOut->addHTML( $s );
	}

	function protect()
	{
		global $wgUser, $wgOut, $wgTitle;

		if ( ! $wgUser->isSysop() ) {
			$wgOut->sysopRequired();
			return;
		}
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		$id = $wgTitle->getArticleID();
		if ( 0 == $id ) {
			$wgOut->fatalEror( wfMsg( "badarticleerror" ) );
			return;
		}
		$sql = "UPDATE cur SET cur_timestamp=cur_timestamp," .
		  "cur_restrictions='sysop' WHERE cur_id={$id}";
		wfQuery( $sql, "Article::protect" );

		$wgOut->redirect( wfLocalUrl( $wgTitle->getPrefixedURL() ) );
	}

	function unprotect()
	{
		global $wgUser, $wgOut, $wgTitle;

		if ( ! $wgUser->isSysop() ) {
			$wgOut->sysopRequired();
			return;
		}
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		$id = $wgTitle->getArticleID();
		if ( 0 == $id ) {
			$wgOut->fatalEror( wfMsg( "badarticleerror" ) );
			return;
		}
		$sql = "UPDATE cur SET cur_timestamp=cur_timestamp," .
		  "cur_restrictions='' WHERE cur_id={$id}";
		wfQuery( $sql, "Article::unprotect" );

		$wgOut->redirect( wfLocalUrl( $wgTitle->getPrefixedURL() ) );
	}

	function delete()
	{
		global $wgUser, $wgOut, $wgTitle;
		global $wpConfirm, $wpReason, $image, $oldimage;

		if ( ( ! $oldimage ) && ( ! $wgUser->isSysop() ) ) {
			$wgOut->sysopRequired();
			return;
		}
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		if ( $oldimage || 1 == $wpConfirm ) {
			$this->doDelete();
			return;
		}
		$wgOut->setPagetitle( wfMsg( "confirmdelete" ) );
		if ( $image ) {
			$sub = str_replace( "$1", $image, wfMsg( "deletesub" ) );
		} else {
			$sub = str_replace( "$1", $wgTitle->getPrefixedText(),
			  wfMsg( "deletesub" ) );
		}
		$wgOut->setSubtitle( $sub );
		$wgOut->setRobotpolicy( "noindex,nofollow" );
		$wgOut->addWikiText( wfMsg( "confirmdeletetext" ) );

		$t = $wgTitle->getPrefixedURL();
		$q = "action=delete";
		if ( $image ) { $q .= "&image={$image}"; }
		if ( $oldimage ) { $q .= "&oldimage={$oldimage}"; }

		$action = wfEscapeHTML( wfLocalUrl( $t, $q ) );
		$confirm = wfMsg( "confirm" );
		$check = wfMsg( "confirmcheck" );
		$delcom = wfMsg( "deletecomment" );

		$wgOut->addHTML( "
<form method=post action=\"{$action}\">
<table border=0><tr><td align=right>
{$delcom}:</td><td align=left>
<input type=text size=20 name='wpReason' value=\"{$wpReason}\">
</td></tr><tr><td>&nbsp;</td></tr>
<tr><td align=right>
<input type=checkbox name='wpConfirm' value='1'>
</td><td>{$check}</td>
</tr><tr><td>&nbsp;</td><td>
<input type=submit name='wpConfirmB' value=\"{$confirm}\">
</td></tr></table></form>\n" );

		$wgOut->returnToMain( false );
	}

	function doDelete()
	{
		global $wgOut, $wgTitle, $wgUser;
		global $image, $oldimage, $wpReason;
		$fname = "Article::doDelete";

		if ( $image ) {
			$dest = wfImageDir( $image );
			$archive = wfImageDir( $image );
			if ( ! unlink( "{$dest}/{$image}" ) ) {
				$wgOut->fileDeleteError( "{$dest}/{$image}" );
				return;
			}
			$sql = "DELETE FROM image WHERE img_name='" .
			  wfStrencode( $image ) . "'";
			wfQuery( $sql, $fname );

			$sql = "SELECT oi_archive_name FROM oldimage WHERE oi_name='" .
			  wfStrencode( $image ) . "'";
			$res = wfQuery( $sql, $fname );

			while ( $s = wfFetchObject( $res ) ) {
				$this->doDeleteOldImage( $s->oi_archive_name );
			}	
			$sql = "DELETE FROM oldimage WHERE oi_name='" .
			  wfStrencode( $image ) . "'";
			wfQuery( $sql, $fname );

			$nt = Title::newFromText( "Image:{$image}" );
			$this->doDeleteArticle( $nt );

			$deleted = $image;
		} else if ( $oldimage ) {
			$this->doDeleteOldImage( $oldimage );
			$sql = "DELETE FROM oldimage WHERE oi_archive_name='" .
			  wfStrencode( $oldimage ) . "'";
			wfQuery( $sql, $fname );

			$deleted = $oldimage;
		} else {
			$this->doDeleteArticle( $wgTitle );
			$deleted = $wgTitle->getPrefixedText();
		}
		$wgOut->setPagetitle( wfMsg( "actioncomplete" ) );
		$wgOut->setRobotpolicy( "noindex,nofollow" );

		$sk = $wgUser->getSkin();
		$loglink = $sk->makeKnownLink( "Wikipedia:" . wfMsg( "dellogpage" ),
		  wfMsg( "deletionlog" ) );

		$text = str_replace( "$1" , $deleted, wfMsg( "deletedtext" ) );
		$text = str_replace( "$2", $loglink, $text );

		$wgOut->addHTML( "<p>" . $text );
		$wgOut->returnToMain( false );
	}

	function doDeleteOldImage( $oldimage )
	{
		global $wgOut;

		$name = substr( $oldimage, 15 );
		$archive = wfImageArchiveDir( $name );
		if ( ! unlink( "{$archive}/{$oldimage}" ) ) {
			$wgOut->fileDeleteError( "{$archive}/{$oldimage}" );
		}
	}

	function doDeleteArticle( $title )
	{
		global $wgUser, $wgOut, $wgLang, $wpReason;

		$fname = "Article::doDeleteArticle";
		$ns = $title->getNamespace();
		$t = wfStrencode( $title->getDBkey() );
		$id = $title->getArticleID();

		$sql = "DELETE FROM cur WHERE cur_namespace={$ns} AND " .
		  "cur_title='{$t}'";
		wfQuery( $sql, $fname );

		$sql = "DELETE FROM old WHERE old_namespace={$ns} AND " .
		  "old_title='{$t}'";
		wfQuery( $sql, $fname );

		if ( 0 != $id ) {
			$t = wfStrencode( $title->getPrefixedDBkey() );
			$sql = "SELECT l_from FROM links WHERE l_to={$id}";
			$res = wfQuery( $sql, $fname );

			$sql = "INSERT INTO brokenlinks (bl_from,bl_to) VALUES ";
			$first = true;

			while ( $s = wfFetchObject( $res ) ) {
				$nt = Title::newFromDBkey( $s->l_from );
				$lid = $nt->getArticleID();

				if ( ! $first ) { $sql .= ","; }
				$first = false;
				$sql .= "({$lid},'{$t}')";
			}
			if ( ! $first ) { wfQuery( $sql, $fname ); }
			wfFreeResult( $res );

			$sql = "DELETE FROM links WHERE l_to={$id}";
			wfQuery( $sql, $fname );

			$sql = "DELETE FROM links WHERE l_from='{$t}'";
			wfQuery( $sql, $fname );

			$sql = "DELETE FROM imagelinks WHERE il_from='{$t}'";
			wfQuery( $sql, $fname );

			$sql = "DELETE FROM brokenlinks WHERE bl_from={$id}";
			wfQuery( $sql, $fname );
		}
		$logpage = wfStrencode( wfMsg( "dellogpage" ) );
		$sql = "SELECT cur_id,cur_text FROM cur WHERE cur_namespace=" .
		  Namespace::getIndex( "Wikipedia" ) . " AND cur_title='" .
		  "{$logpage}'";
		$res = wfQuery( $sql, $fname );

		if ( 0 == wfNumRows( $res ) ) {
			# Error: need Article deletion log article
		}
		$s = wfFetchObject( $res );
		$text = $s->cur_text;
		$id = $s->cur_id;

		$uid = $wgUser->getID();
		$ut = $wgUser->getName();
		if ( 0 == $uid ) { $ul = $ut; }
		else { $ul = "[[User:{$ut}|{$ut}]]"; }

		$art = $title->getPrefixedText();
		$d = $wgLang->timeanddate( date( "YmdHis" ), true );

		preg_match( "/^(.*?)<ul>(.*)$/sD", $text, $m );	
		$da = str_replace( "$1", $art, wfMsg( "deletedarticle" ) );

		if ( "" == $wpReason ) {
			$lcom = "{$da}";
			$com = "";
		} else {
			$lcom = "{$da}: {$wpReason}";
			$com = " <em>({$wpReason})</em>";
		}
	
		$text = "{$m[1]}<ul><li>{$d} {$ul} {$da}{$com}</li>\n{$m[2]}";

		$sql = "UPDATE cur SET cur_timestamp='" . date( "YmdHis" ) .
		  "', cur_user={$uid}, cur_user_text='" . wfStrencode( $ut ) .
		  "', cur_text='" . wfStrencode( trim( $text ) ) . "', " .
		  "cur_comment='" . wfStrencode( $lcom ) . "' " .
		  "WHERE cur_id={$id}";
		wfQuery( $sql, $fname );
	}

	function revert()
	{
		global $wgOut;
		global $oldimage;

		if ( strlen( $oldimage ) < 16 ) {
			$wgOut->unexpectedValueError( "oldimage", $oldimage );
			return;
		}
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		$name = substr( $oldimage, 15 );

		$dest = wfImageDir( $name );
		$archive = wfImageArchiveDir( $name );
		$curfile = "{$dest}/{$name}";

		if ( ! is_file( $curfile ) ) {
			$wgOut->fileNotFoundError( $curfile );
			return;
		}
		$oldver = date( "YmdHis" ) . "!{$name}";
		$size = wfGetSQL( "oldimage", "oi_size", "oi_archive_name='" .
		  wfStrencode( $oldimage ) . "'" );

		if ( ! rename( $curfile, "${archive}/{$oldver}" ) ) {
			$wgOut->fileRenameError( $curfile, "${archive}/{$oldver}" );
			return;
		}
		if ( ! copy( "{$archive}/{$oldimage}", $curfile ) ) {
			$wgOut->fileCopyError( "${archive}/{$oldimage}", $curfile );
		}
		wfRecordUpload( $name, $oldver, $size, "Reverted to earlier image" );

		$wgOut->setPagetitle( wfMsg( "actioncomplete" ) );
		$wgOut->setRobotpolicy( "noindex,nofollow" );
		$wgOut->addHTML( wfMsg( "imagereverted" ) );
		$wgOut->returnToMain( false );
	}

	# Do standard deferred updates after page view
	#
	/* private */ function viewUpdates()
	{
		global $wgDeferredUpdateList;

		if ( 0 != $this->getID() ) {
			$u = new ViewCountUpdate( $this->getID() );
			array_push( $wgDeferredUpdateList, $u );
			$u = new SiteStatsUpdate( 1, 0, 0 );
			array_push( $wgDeferredUpdateList, $u );
		}
	}

	# Do standard deferred updates after page edit
	#
	/* private */ function editUpdates( $id, $title, $text, $adj )
	{
		global $wgDeferredUpdateList;
		if ( 0 != $id ) {
			$u = new LinksUpdate( $id, $title );
			array_push( $wgDeferredUpdateList, $u );
			$u = new SiteStatsUpdate( 0, 1, $adj );
			array_push( $wgDeferredUpdateList, $u );
			$u = new SearchUpdate( $id, $title, $text );
			array_push( $wgDeferredUpdateList, $u );
		}
	}

	/* private */ function setOldSubtitle()
	{
		global $wgLang, $wgOut;

		$td = $wgLang->timeanddate( $this->mTimestamp, true );
		$r = str_replace( "$1", "{$td}", wfMsg( "revisionasof" ) );
		$wgOut->setSubtitle( "({$r})" );
	}

	function blockedIPpage()
	{
		global $wgOut, $wgUser;

		$wgOut->setPageTitle( wfMsg( "blockedtitle" ) );
		$wgOut->setRobotpolicy( "noindex,nofollow" );
		$wgOut->setArticleFlag( false );

		$id = $wgUser->blockedBy();
		$reason = $wgUser->blockedFor();

		$name = User::whoIs( $id );
		$link = "[[User:$name|$name]]";

		$text = str_replace( "$1", $link, wfMsg( "blockedtext" ) );
		$text = str_replace( "$2", $reason, $text );
		$wgOut->addWikiText( $text );
		$wgOut->returnToMain( false );
	}

	# This function is called right before saving the wikitext,
	# so we can do things like signatures and links-in-context.
	#
	function preSaveTransform( $text )
	{
		$s = "";
		while ( "" != $text ) {
			$p = preg_split( "/<\\s*nowiki\\s*>/i", $text, 2 );
			$s .= $this->pstPass2( $p[0] );

			if ( ( count( $p ) < 2 ) || ( "" == $p[1] ) ) { $text = ""; }
			else {
				$q = preg_split( "/<\\/\\s*nowiki\\s*>/i", $p[1], 2 );
				$s .= "<nowiki>{$q[0]}</nowiki>";
				$text = $q[1];
			}
		}
		return trim( $s );
	}

	/* private */ function pstPass2( $text )
	{
		global $wgUser, $wgLang, $wgTitle;

		# Signatures
		#
		$n = $wgUser->getName();
		$k = $wgUser->getOption( "nickname" );
		if ( "" == $k ) { $k = $n; }
		$d = $wgLang->timeanddate( date( "YmdHis" ), false ) .
		  " (" . date( "T" ) . ")";

		$text = preg_replace( "/~~~~/", "[[User:$n|$k]] $d", $text );
		$text = preg_replace( "/~~~/", "[[User:$n|$k]]", $text );

		# Context links: [[|name]] and [[name (context)|]]
		#
		$tc = "[&;%\\-,.\\(\\)' _0-9A-Za-z\\/:\\x80-\\xff]";
		$np = "[&;%\\-,.' _0-9A-Za-z\\/:\\x80-\\xff]"; # No parens
		$conpat = "/^({$np}+) \\(({$tc}+)\\)$/";

		$p1 = "/\[\[({$np}+) \\(({$np}+)\\)\\|]]/"; # [[page (context)|]]
		$p2 = "/\[\[\\|({$tc}+)]]/"; # [[|page]]

		$context = "";
		$t = $wgTitle->getText();
		if ( preg_match( $conpat, $t, $m ) ) {
			$context = $m[2];
		}
		$text = preg_replace( $p1, "[[\\1 (\\2)|\\1]]", $text );

		if ( "" == $context ) {
			$text = preg_replace( $p2, "[[\\1]]", $text );
		} else {
			$text = preg_replace( $p2, "[[\\1 ({$context})|\\1]]", $text );
		}
		# Replace local image links with new [[image:]] style
		#
		$text = preg_replace(
		  "/(^|[^[])http:\/\/(www.|)wikipedia.com\/upload\/" .
		  "([a-zA-Z0-9_:.~\%\-]+)\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)/",
		  "\\1[[image:\\3.\\4]]", $text );
		$text = preg_replace(
		  "/(^|[^[])http:\/\/(www.|)wikipedia.com\/images\/uploads\/" .
		  "([a-zA-Z0-9_:.~\%\-]+)\.(png|PNG|jpg|JPG|jpeg|JPEG|gif|GIF)/",
		  "\\1[[image:\\3.\\4]]", $text );

		return $text;
	}
}

?>
