<?
# See design.doc

class Article {
	/* private */ var $mContent, $mContentLoaded;
	/* private */ var $mUser, $mTimestamp, $mUserText;
	/* private */ var $mCounter, $mComment;
	/* private */ var $mMinorEdit, $mRedirectedFrom;

	function Article() { $this->clear(); }

	/* private */ function clear()
	{
		$this->mContentLoaded = false;
		$this->mUser = $this->mCounter = -1; # Not loaded
		$this->mRedirectedFrom = $this->mUserText =
		$this->mTimestamp = $this->mComment = "";
	}

	/* static */ function newFromID( $newid )
	{
		global $wgOut, $wgTitle, $wgArticle;
		$a = new Article();

		$conn = wfGetDB();
		$sql = "SELECT cur_namespace,cur_title FROM cur WHERE " .
		  "cur_id={$newid}";
		$res = wfQuery( $sql, $conn, "Article::newFromID" );
		if ( 0 == mysql_num_rows( $res ) ) { return NULL; }

		$s = mysql_fetch_object( $res );
		$wgTitle = Title::newFromDBkey( Title::makeName( $s->cur_namespace,
		  $s->cur_title ) );
		$wgTitle->resetArticleID( $newid );
		mysql_free_result( $res );

		return $a;
	}

	function getContent()
	{
		if ( 0 == $this->getID() ) {
			return wfMsg( "newarticletext" );
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

			$conn = wfGetDB();
			$sql = "SELECT cur_text,cur_timestamp,cur_user,cur_counter " .
			  "FROM cur WHERE cur_id={$id}";
			$res = wfQuery( $sql, $conn, $fname );
			if ( 0 == mysql_num_rows( $res ) ) { return; }

			$s = mysql_fetch_object( $res );
			if ( ( "no" != $redirect ) &&
			  ( preg_match( "/^#redirect/i", $s->cur_text ) ) ) {
				if ( preg_match( "/\\[\\[([^\\]\\|]+)[\\]\\|]/",
				  $s->cur_text, $m ) ) {
					$rt = Title::newFromText( $m[1] );
					$rid = $rt->getArticleID();
					if ( 0 != $rid ) {
						$conn = wfGetDB();
						$sql = "SELECT cur_text,cur_timestamp,cur_user," .
						  "cur_counter FROM cur WHERE cur_id={$rid}";
						$res = wfQuery( $sql, $conn, $fname );

						if ( 0 != mysql_num_rows( $res ) ) {
							$this->mRedirectedFrom = $wgTitle->getPrefixedText();
							$wgTitle = $rt;
							$s = mysql_fetch_object( $res );
						}
					}
				}
			}
			$this->mContent = $s->cur_text;
			$this->mUser = $s->cur_user;
			$this->mCounter = $s->cur_counter;
			$this->mTimestamp = $s->cur_timestamp;
			mysql_free_result( $res );
		} else {
			$conn = wfGetDB();
			$sql = "SELECT old_text,old_timestamp,old_user FROM old " .
			  "WHERE old_id={$oldid}";
			$res = wfQuery( $sql, $conn, $fname );
			if ( 0 == mysql_num_rows( $res ) ) { return; }

			$s = mysql_fetch_object( $res );
			$this->mContent = $s->old_text;
			$this->mUser = $s->old_user;
			$this->mCounter = 0;
			$this->mTimestamp = $s->old_timestamp;
			mysql_free_result( $res );
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

	/* private */ function loadLastEdit()
	{
		global $wgOut;
		if ( -1 != $this->mUser ) return;

		$conn = wfGetDB();
		$sql = "SELECT cur_user,cur_user_text,cur_timestamp," .
		  "cur_comment,cur_minor_edit FROM cur WHERE " .
		  "cur_id=" . $this->getID();
		$res = wfQuery( $sql, $conn, "Article::loadLastEdit" );

		if ( mysql_num_rows( $res ) > 0 ) {
			$s = mysql_fetch_object( $res );
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

		if ( isset( $diff ) ) {
			$wgOut->setPageTitle( $wgTitle->getPrefixedText() );
			$wgOut->setArticleFlag( false );
			$de = new DifferenceEngine( $oldid, $diff );
			$de->showDiffs();
			return;
		}
		$text = $this->getContent();
		$wgOut->setPageTitle( $wgTitle->getPrefixedText() );
		if ( $oldid ) { $this->setOldSubtitle(); }

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
		}
		$this->viewUpdates();
	}

	function edit()
	{
		global $wgOut, $wgUser, $wgTitle, $wgReadOnly;
		global $wpTextbox1, $wpSummary, $wpSave, $wpPreview;
		global $wpMinoredit, $wpEdittime, $wpTextbox2;

		if ( ! $wgTitle->userCanEdit() ) {
			$this->view();
			return;
		}
		if ( $wgUser->isBlocked() ) {
			$this->blockedIPpage();
			return;
		}
		if ( $wgReadOnly ) {
			$this->readOnlyPage();
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

	function editForm( $formtype )
	{
		global $wgOut, $wgUser, $wgTitle;
		global $wgServer, $wgScript;
		global $wpTextbox1, $wpSummary, $wpSave, $wpPreview;
		global $wpMinoredit, $wpEdittime, $wpTextbox2;
		global $oldid, $redirect;

		$isConflict = false;
		if ( "save" == $formtype ) {
			if ( $wgUser->isBlocked() ) {
				$this->blockedIPpage();
				return;
			}
			$aid = $wgTitle->getArticleID();
			if ( 0 == $aid ) { # New aritlce
				$this->insertNewArticle( $wpTextbox1, $wpSummary, $wpMinoredit );
				return;
			}
			# Check for edit conflict. TODO: check oldid here?
			#
			if ( $this->getUser() != $wgUser->getID() &&
			  $this->mTimestamp > $wpEdittime ) {
				$isConflict = true;
			} else {
				# All's well: save the article here
				$this->updateArticle( $wpTextbox1, $wpSummary, $wpMinoredit );
				return;
			}
		}
		if ( "initial" == $formtype ) {
			$wpEdittime = $this->getTimestamp();
			$wpTextbox1 = $this->getContent();
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
		$action = "$wgServer$wgScript?title=" .
		  $wgTitle->getPrefixedURL() . "&amp;action=edit";
		if ( "no" == $redirect ) { $action .= "&amp;redirect=no"; }

		$summary = wfMsg( "summary" );
		$minor = wfMsg( "minoredit" );
		$save = wfMsg( "savearticle" );
		$prev = wfMsg( "showpreview" );

		$wpTextbox1 = wfEscapeHTML( $wpTextbox1 );
		$wpTextbox2 = wfEscapeHTML( $wpTextbox2 );
		$wpSummary = wfEscapeHTML( $wpSummary );

		$wgOut->addHTML( "
<form method=post action='$action'
enctype='application/x-www-form-urlencoded'>
<textarea tabindex=1 name='wpTextbox1' rows=$rows cols=$cols style='width:100%' wrap=virtual>
$wpTextbox1
</textarea><br>
$summary: <input tabindex=2 type=text value='$wpSummary' name='wpSummary' maxlength=200>
<input tabindex=3 type=checkbox value=1 name='wpMinoredit'>$minor<br>
<input tabindex=4 type=submit value='$save' name='wpSave'>
<input tabindex=5 type=submit value='$prev' name='wpPreview'>
<input type=hidden value='$wpEdittime' name='wpEdittime'>\n" );

		if ( $isConflict ) {
			$wgOut->AddHTML( "<h2>" . wfMsg( "yourtext" ) . "</h2>
<textarea tabindex=6 name='wpTextbox2' rows=$rows cols=$cols style='width:100%' wrap=virtual>
$wpTextbox2
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
			$wgOut->addWikiText( $previewtext );
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

		$conn = wfGetDB();
		$sql = "INSERT INTO cur (cur_namespace,cur_title,cur_text," .
		  "cur_comment,cur_user,cur_timestamp,cur_minor_edit,cur_counter," .
		  "cur_restrictions,cur_ind_title,cur_user_text,cur_is_redirect) " .
		  "VALUES ({$ns},'{$ttl}', '" . wfStrencode( $text ) . "', '" .
		  wfStrencode( $summary ) . "', '" .
		  $wgUser->getID() . "', '" . date( "YmdHis" ) . "', " .
		  ( $isminor ? 1 : 0 ) . ", 0, '', '" .
		  $wgTitle->getPrefixedText() . "', '" .
		  wfStrencode( $wgUser->getName() ) . "', $redir)";
		$res = wfQuery( $sql, $conn, "Article::insertNewArticle" );

		$newid = mysql_insert_id( $conn );
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

		$conn = wfGetDB();
		$sql = "INSERT INTO old (old_namespace,old_title,old_text," .
		  "old_comment,old_user,old_user_text,old_timestamp," .
		  "old_minor_edit) VALUES (" .
		  $wgTitle->getNamespace() . ", '" .
		  $wgTitle->getDBkey() . "', '" .
		  wfStrencode( $this->getContent() ) . "', '" .
		  wfStrencode( $this->getComment() ) . "', " .
		  $this->getUser() . ", '" .
		  wfStrencode( $this->getUserText() ) . "', '" .
		  $this->getTimestamp() . "', " . $me1 . ")";
		$res = wfQuery( $sql, $conn, $fname );

		$conn = wfGetDB();
		$sql = "UPDATE cur SET cur_text='" .  wfStrencode( $text ) .
		  "',cur_comment='" .  wfStrencode( $summary ) .
		  "',cur_minor_edit={$me2}, cur_user=" . $wgUser->getID() .
		  ",cur_timestamp='" . date( "YmdHis" ) .
		  "',cur_user_text='" . wfStrencode( $wgUser->getName() ) .
		  "',cur_is_redirect={$redir} " .
		  "WHERE cur_id=" . $this->getID();
		$res = wfQuery( $sql, $conn, $fname );

		$this->showArticle( $text, wfMsg( "updated" ) );
	}

	function showArticle( $text, $subtitle )
	{
		global $wgOut, $wgTitle, $wgUser, $wgLinkCache;

		$wgOut->setPageTitle( $wgTitle->getPrefixedText() );
		$wgOut->setSubtitle( $subtitle );
		$wgOut->setArticleFlag( true );

		$wgLinkCache = new LinkCache();
		$wgOut->addWikiText( $text );

		$ins = Namespace::getIndex( "Image" );
		if ( $ins == $wgTitle->getNamespace() ) {
			$this->imageHistory();
		}
		$this->editUpdates( $this->getID(), $wgTitle->getPrefixedDBkey() );
	}

	function imageHistory()
	{
		global $wgOut, $wgTitle;

		$wgOut->addHTML( "\n<h2>" . wfMsg( "imghistory" ) . "</h2>\n" );
		$wgOut->addHTML( "<p>(TODO: Image history)\n" );
	}

	function watch()
	{
	}

	# This shares a lot of issues (and code) with Recent Changes
	#
	function history()
	{
		global $wgUser, $wgOut, $wgLang, $wgTitle;

		$wgOut->setPageTitle( $wgTitle->getPRefixedText() );
		$wgOut->setSubtitle( wfMsg( "revhistory" ) );
		$wgOut->setArticleFlag( false );

		$conn = wfGetDB();
		$sql = "SELECT old_id,old_namespace,old_title,old_user," .
		  "old_comment,old_user_text,old_timestamp,old_minor_edit FROM old " .
		  "WHERE old_namespace=" . $wgTitle->getNamespace() . " AND " .
		  "old_title='" . $wgTitle->getDBkey() . "' " .
		  "ORDER BY old_timestamp DESC";
		$res = wfQuery( $sql, $conn, "Article::history" );

		$revs = mysql_num_rows( $res );
		$sk = $wgUser->getSkin();
		$s = $sk->beginHistoryList();		

		$s .= $sk->historyLine( $this->getTimestamp(), $this->getUser(),
		  $this->getUserText(), $wgTitle->getNamespace(),
		  $wgTitle->getText(), 0, $this->getComment(),
		  ( $this->getMinorEdit() > 0 ) );

		while ( $revs ) {
			$line = mysql_fetch_object( $res );

			$s .= $sk->historyLine( $line->old_timestamp, $line->old_user,
			  $line->old_user_text, $line->old_namespace,
			  $line->old_title, $line->old_id,
			  $line->old_comment, ( $line->old_minor_edit > 0 ) );
			--$revs;
		}
		$s .= $sk->endHistoryList();
		$wgOut->addHTML( $s );
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
	/* private */ function editUpdates( $id, $title )
	{
		global $wgDeferredUpdateList;

		$u = new SiteStatsUpdate( 0, 1, 0 );
		array_push( $wgDeferredUpdateList, $u );

		$u = new LinksUpdate( $id, $title );
		array_push( $wgDeferredUpdateList, $u );
	}

	/* private */ function setOldSubtitle()
	{
		global $wgLang, $wgOut;

		$td = $wgLang->timeanddate( $this->mTimestamp );
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
		$wgOut->returnToMain();
	}

	function readOnlyPage()
	{
		global $wgOut, $wgUser;

		$wgOut->setPageTitle( wfMsg( "readonly" ) );
		$wgOut->setRobotpolicy( "noindex,nofollow" );
		$wgOut->setArticleFlag( false );

		$wgOut->addWikiText( wfMsg( "readonlytext" ) );
		$wgOut->returnToMain();
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

			if ( "" == $p[1] ) { $text = ""; }
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
		$d = $wgLang->timeanddate( date( "YmdHis" ) );

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
