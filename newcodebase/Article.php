<?
# See design.doc

class Article {
	/* private */ var $mContent, $mContentLoaded;
	/* private */ var $mUser, $mTimestamp;
	/* private */ var $mCounter, $mComment;
	/* private */ var $mMinorEdit;

	function Article() { $this->clear(); }

	/* private */ function clear()
	{
		$this->mContentLoaded = false;
		$this->mUser = $this->mCounter = -1; # Not loaded
		$this->mTimestamp = $this->mComment = "";
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
		if ( $this->mContentLoaded ) return;

		$id = $this->getID();
		if ( 0 == $id ) return;

		$conn = wfGetDB();
		$sql = "SELECT cur_text,cur_timestamp,cur_user,cur_counter " .
		  "FROM cur WHERE cur_id=$id";
		wfDebug( "Art: 1: $sql\n" );
		$res = mysql_query( $sql, $conn );

		if ( ( false === $res ) || ( 0 == mysql_num_rows( $res ) ) ) {
			$this->mContent = "Fatal database error.\n";
		} else {
			$s = mysql_fetch_object( $res );
			$this->mContent = $s->cur_text;
			$this->mUser = $s->cur_user;
			$this->mCounter = $s->cur_counter;
			$this->mTimestamp = $s->cur_timestamp;
		}
		mysql_free_result( $res );
		$this->mContentLoaded = true;
	}

	function getID() { global $wgTitle; return $wgTitle->getArticleID(); }

	function getCount()
	{
		if ( -1 == $this->mCounter ) {
			$id = $this->getID();
			$this->mCounter = wfGetSQL( "cur", "cur_counter", "cur_id=$id" );
		}
		return $this->mCounter;
	}

	/* private */ function loadLastEdit()
	{
		if ( -1 != $this->mUser ) return;

		$conn = wfGetDB();
		$sql = "SELECT cur_user,cur_timestamp," .
		  "cur_comment,cur_minor_edit FROM cur WHERE " .
		  "cur_id=" . $this->getID();
		wfDebug( "Art: 3: $sql\n" );

		$res = mysql_query( $sql, $conn );
		if ( $res && ( mysql_num_rows( $res ) > 0 ) ) {
			$s = mysql_fetch_object( $res );
			$this->mUser = $s->cur_user;
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
		return $this->mTimestamp;
	}

	function view()
	{
		global $wgOut, $wgTitle;
		$wgOut->setPageTitle( $wgTitle->getPrefixedText() );

		$wgOut->addWikiText( $this->getContent() );
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

		$isConflict = false;
		if ( "save" == $formtype ) {
			if ( $wgUser->isBlocked() ) {
				$this->blockedIPpage();
				return;
			}
			$aid = $wgTitle->getArticleID();
			if ( 0 == $aid ) { # New aritlce
				$this->insertNewArticle( $wpTextbox1, $wpSummary );
				return;
			}
			# Check for edit conflict
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
			$wpSummary = "*";
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
		}
		$rows = $wgUser->getOption( "rows" );
		$cols = $wgUser->getOption( "cols" );
		$action = "$wgServer$wgScript?title=" .
		  $wgTitle->getPrefixedURL() . "&action=edit";
		$summary = wfMsg( "summary" );
		$minor = wfMsg( "minoredit" );
		$save = wfMsg( "savearticle" );
		$prev = wfMsg( "showpreview" );

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
<textarea tabindex=6 name='wpTextbox2' rows=$rows cols=$cols style='width:100%' wrap=virtual>\n" );
		}
		$wgOut->addHTML( "</form>\n" );

		if ( "preview" == $formtype ) {
			$wgOut->addHTML( "<h2>" . wfMsg( "preview" ) . "</h2>\n" );
			if ( $isConflict ) {
				$wgOut->addHTML( "<h2>" . wfMsg( "previewconflict" ) .
				  "</h2>\n" );
			}
			$wgOut->addWikiText( $wpTextbox1 );
			$wgOut->addHTML( "<p><large>" . wfMsg( "note" ) .
			  wfMsg( "previewnote" ) . "</large>\n" );
		}
	}

	# Theoretically we could defer these whole insert and update
	# functions for after display, but that's taking a big leap
	# leap of faith, and I want to be able to report database
	# errors at some point.
	#
	/* private */ function insertNewArticle( $text, $summary )
	{
		global $wgOut, $wgUser, $wgTitle, $wgLinkCache;

		$ns = $wgTitle->getNamespace();
		$ttl = $wgTitle->getDBkey();
		$text = $this->preSaveTransform( $text );

		$conn = wfGetDB();
		$sql = "INSERT INTO cur (cur_namespace,cur_title,cur_text," .
		  "cur_comment,cur_user,cur_timestamp,cur_minor_edit,cur_counter," .
		  "cur_restrictions,cur_ind_title,cur_user_text) " .
		  "VALUES ({$ns},'{$ttl}', '" . wfStrencode( $text ) . "', '" .
		  wfStrencode( $summary ) . "', '" .
		  $wgUser->getID() . "', '" . date( "YmdHis" ) . "', 0, 0, '', '" .
		  $wgTitle->getPrefixedText() . "', '" .
		  $wgUser->getName() . "')";

		wfDebug( "Art: 2: $sql\n" );
		$res = mysql_query( $sql, $conn );
		$wgTitle->resetArticleID();
		$newid = $this->getID();

		$s = str_replace( "$1", $wgTitle->getPrefixedText(),
		  wfMsg( "newarticle" ) );
		$wgOut->setPageTitle( $s );
		$wgOut->setArticleFlag( true );

		$wgLinkCache = new LinkCache();
		$wgOut->addWikiText( $text );
		$this->editUpdates( $newid, $wgTitle->getPrefixedDBkey() );
	}

	function updateArticle( $text, $summary, $minor )
	{
		global $wgOut, $wgUser, $wgTitle, $wgLinkCache;

		if ( $this->mMinorEdit ) { $me1 = 1; } else { $me1 = 0; }
		if ( $minor ) { $me2 = 1; } else { $me2 = 0; }
		$this->loadLastEdit();

		$text = $this->preSaveTransform( $text );
		$conn = wfGetDB();
		$sql = "INSERT INTO old (old_namespace,old_title,old_text," .
		  "old_comment,old_user,old_timestamp,old_minor_edit) VALUES (" .
		  $wgTitle->getNamespace() . ", '" .
		  $wgTitle->getDBkey() . "', '" .
		  wfStrencode( $this->getContent() ) . "', '" .
		  wfStrencode( $this->mComment ) . "', " .
		  $this->mUser . ", '" .
		  $this->mTimestamp . "', " . $me1 . ")";

		wfDebug( "Art: 4: $sql\n" );
		$res = mysql_query( $sql, $conn );
		if ( false === $res ) {
			$wgOut->databaseError( wfMsg( "updatingarticle" ) );
			return;
		}
		$conn = wfGetDB();
		$sql = "UPDATE cur SET cur_text='" .  wfStrencode( $text ) .
		  "',cur_comment='" .  wfStrencode( $summary ) .
		  "',cur_minor_edit={$me2}, cur_user=" . $wgUser->getID() .
		  ",cur_timestamp='" . date( "YmdHis" ) .
		  "',cur_user_text='" . $wgUser->getName() . "' " .
		  "WHERE cur_id=" . $this->getID();

		wfDebug( "Art: 5: $sql\n" );
		$res = mysql_query( $sql, $conn );

		$s = str_replace( "$1", $wgTitle->getPrefixedText(),
		  wfMsg( "updated" ) );
		$wgOut->setPageTitle( $s );
		$wgOut->setArticleFlag( true );

		$wgLinkCache = new LinkCache();
		$wgOut->addWikiText( $text );
		$this->editUpdates( $this->getID(), $wgTitle->getPrefixedDBkey() );
	}

	function viewprintable()
	{
		global $wgOut, $wgUser, $wgTitle;

		$n = $wgTitle->getPrefixedText();
		$wgOut->setPageTitle( $n );
		$wgOut->setPrintable();
		$wgOut->addWikiText( $this->getContent() );

		$this->viewUpdates();
	}

	function watch()
	{
	}

	function history()
	{
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
				$s .= $q[0];
				$text = $q[1];
			}
		}
		return $s;
	}

	/* private */ function pstPass2( $text )
	{
		global $wgUser, $wgLang, $wgTitle;

		$n = $wgUser->getName();
		$k = $wgUser->getOption( "nickname" );
		if ( "" == $k ) { $k = $n; }
		$d = $wgLang->dateFromTimestamp( date( "YmdHis" ) );

		$text = preg_replace( "/~~~~/", "[[User:$n|$k]] $d", $text );
		$text = preg_replace( "/~~~/", "[[User:$n|$k]]", $text );

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
		return $text;
	}
}

?>
