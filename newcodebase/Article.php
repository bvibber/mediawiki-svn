<?
# See design.doc

class Article {
	/* private */ var $mTitle; # WikiTitle object
	/* private */ var $mContent, $mContentLoaded;
	/* private */ var $mUser, $mTimestamp;
	/* private */ var $mCounter, $mComment, $mRevision;
	/* private */ var $mMinorEdit;

	function Article( $t )
	{
		$this->mTitle = $t;
		$this->mContentLoaded = false;
		$this->mUser = $this->mCounter = -1; # Not loaded
		$this->mTimestamp = $this->mComment = "";
		$this->mRevision = 0;
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

	function getID() { return $this->mTitle->getArticleID(); }

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
		$sql = "SELECT cur_user,cur_timestamp,cur_revision," .
		  "cur_comment,cur_minor_edit FROM cur WHERE " .
		  "cur_id=" . $this->getID();
		wfDebug( "Art: 3: $sql\n" );

		$res = mysql_query( $sql, $conn );
		if ( ! ( false === $res ) ) {
			$s = mysql_fetch_object( $res );
			$this->mUser = $s->cur_user;
			$this->mTimestamp = $s->cur_timestamp;
			$this->mComment = $s->cur_comment;
			$this->mRevision = $s->cur_revision;
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
		global $wgOut;
		$wgOut->setPageTitle( $this->mTitle->getPrefixedText() );

		$this->showArticle();
		$this->viewUpdates();
	}

	/* private */ function showArticle()
	{
		global $wgOut;
		$wgOut->addWikiText( $this->getContent() );
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
				$this->insertArticle( $wpTextbox1, $wpSummary );
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
	/* private */ function insertArticle( $text, $summary )
	{
		global $wgOut, $wgUser, $wgTitle;

		$conn = wfGetDB();
		$sql = "INSERT INTO cur (cur_namespace,cur_title,cur_text," .
		  "cur_comment,cur_user,cur_timestamp,cur_minor_edit,cur_counter," .
		  "cur_restrictions,cur_revision,cur_ind_title) VALUES (" .
		  $this->mTitle->getNamespace() . ", '" .
		  $this->mTitle->getDBKey() . "', '" .
		  wfStrencode( $text ) . "', '" . wfStrencode( $summary ) . "', '" .
		  $wgUser->getID() . "', '" . date( "YmdHis" ) . "', 0, 0, '', 0, '" .
		  $this->mTitle->getPrefixedText() . "')";

		wfDebug( "Art: 2: $sql\n" );
		$res = mysql_query( $sql, $conn );
		$this->editUpdates();

		$s = str_replace( "$1", $wgTitle->getPrefixedText,
		  wfMsg( "newarticle" ) );
		$wgOut->setPageTitle( $s );
		$wgOut->addWikiText( $text );
	}

	function updateArticle( $text, $summary, $minor )
	{
		global $wgOut, $wgUser, $wgTitle;

		if ( $this->mMinorEdit ) { $me1 = 1; } else { $me1 = 0; }
		if ( $minor ) { $me2 = 1; } else { $me2 = 0; }

		$this->loadLastEdit();
		$conn = wfGetDB();
		$sql = "INSERT INTO old (old_namespace,old_title,old_text," .
		  "old_comment,old_user,old_revision,old_timestamp," .
		  "old_minor_edit) VALUES (" .
		  $this->mTitle->getNamespace() . ", '" .
		  $this->mTitle->getDBKey() . "', '" .
		  wfStrencode( $this->getContent() ) . "', '" .
		  wfStrencode( $this->mComment ) . "', " .
		  $this->mUser . ", " . $this->mRevision . ", '" .
		  $this->mTimestamp . "', " . $me1 . ")";

		wfDebug( "Art: 4: $sql\n" );
		$res = mysql_query( $sql, $conn );
		if ( false === $res ) {
			$wgOut->databaseError( wfMsg( "updatingarticle" ) );
			return;
		}
		$cond = "(old_namespace=" . $this->mTitle->getNamespace() .
		  " AND old_title='" . $this->mTitle->getDBKey() .
		  "' AND old_revision={$this->mRevision})";
		$newid = wfGetSQL( "old", "old_id", $cond );
		if ( 0 == $newid ) {
			$wgOut->databaseError( wfMsg( "updatingarticle" ) );
			return;
		}
		$conn = wfGetDB();
		$sql = "UPDATE cur SET cur_text='" .  wfStrencode( $text ) .
		  "',cur_comment='" .  wfStrencode( $summary ) .
		  "',cur_minor_edit={$me2}, cur_user=" . $wgUser->getID() .
		  ", cur_timestamp='" . date( "YmdHis" ) .
		  "',cur_revision=$newid " . 
		  "WHERE cur_id=" . $this->getID();

		wfDebug( "Art: 5: $sql\n" );
		$res = mysql_query( $sql, $conn );
		$this->editUpdates();

		$s = str_replace( "$1", $wgTitle->getPrefixedText(),
		  wfMsg( "updated" ) );
		$wgOut->setPageTitle( $s );
		$wgOut->addWikiText( $text );
	}

	function viewprintable()
	{
		global $wgOut, $wgUser, $wgTitle;

		$n = $this->mTitle->getPrefixedText();
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
			$u = new ViewCountUpdate( $this->getID(),
			  ( $this->getCount() + 1 ) );
			array_push( $wgDeferredUpdateList, $u );
			$u = new SiteStatsUpdate( 1, 0, 0 );
			array_push( $wgDeferredUpdateList, $u );
		}
	}

	# Do standard deferred updates after page edit
	#
	/* private */ function editUpdates()
	{
		global $wgDeferredUpdateList;

		$u = new SiteStatsUpdate( 0, 1, 0 );
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
}

?>
