<?
# See design.doc

class Article {
	/* private */ var $mTitle; # WikiTitle object
	/* private */ var $mContent, $mContentLoaded;
	/* private */ var $mTimestamp, $mParams;
	/* private */ var $mCounter;

	function Article( $t )
	{
		$this->mTitle = $t;
		$this->mContentLoaded = false;
		$this->mCounter = -1; # Not loaded
		$this->mTimestamp = "";
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
		$sql = "SELECT cur_text, cur_timestamp, cur_counter, " .
		  "cur_params FROM cur WHERE cur_id=$id";
		wfDebug( "Art: 1: $sql\n" );
		$result = mysql_query( $sql, $conn );

		if ( ! $result ) {
			$this->mContent = "Fatal database error.\n";
		} else {
			$s = mysql_fetch_object( $result );
			$this->mContent = $s->cur_text;
			$this->mCounter = $s->cur_counter;
			$this->mParams = $s->cur_params;
			$this->mTimestamp = $s->cur_timestamp;
		}
		mysql_free_result( $result );
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

	function getTimestamp()
	{
		if ( "" == $this->mTimestamp ) {
			$id = $this->getID();
			$this->mTimestamp = wfGetSQL( "cur", "cur_timestamp", "cur_id=$id" );
		}
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
		global $wgOut, $wgUser, $wgTitle;
		global $wpTextbox1, $wpSummary, $wpSave, $wpPreview;
		global $wpMinoredit, $wpEdittime, $wpTextbox2;

		if ( ! $wgTitle->userCanEdit() ) {
			$this->view();
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

		if ( "save" == $formtype ) {
			if ( $wgUser->isBlocked() ) {
				$this->blockedIPpage();
				return;
			}
			$aid = $wgTitle->getArticleID();
			if ( 0 == $aid ) { # New aritlce
				$conn = wfGetDB();
				$sql = "INSERT INTO cur (cur_namespace,cur_title,cur_text," .
				  "cur_comment,cur_user,cur_timestamp) VALUES ('" .
				  $wgTitle->getNamespace() . "', '" .
				  $wgTitle->getDBKey() . "', '" .
				  wfStrencode( $wpTextbox1 ) . "', '" .
				  wfStrencode( $wpSummary ) . "', '" . $wgUser->getID() .
				  "', '" . date( "YmdHis" ) . "')";

				wfDebug( "Art: 2: $sql\n" );
				$res = mysql_query( $sql, $conn );
				$this->editUpdates();

				$wgOut->setPageTitle( wfMsg( "newarticle" ) . ": " .
				  $wgTitle->getPrefixedText() );
				$wgOut->addWikiText( $wpTextbox1 );
				return;
			}
			# Check for edit conflict
			#

			# All's well: save the article here
			#
			$conn = wfGetDB();
			$sql = "";
			$this->editUpdates();
		}
		if ( "initial" == $formtype ) {
			$wpEdittime = time( "YmdHis" );
			$wpTextbox1 = $this->getContent();
			$wpSummary = "*";
		}
		$wgOut->setPageTitle( "Editing " . $wgTitle->getPrefixedText() );
		$wgOut->setRobotpolicy( "noindex,nofollow" );
		$wgOut->setArticleFlag( false );

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
<input type=hidden value='$wpEdittime' name='wpEdittime'>
</form>\n" );

		if ( "preview" == $formtype ) {
			$wgOut->addHTML( "<h2>" . wfMsg( "preview" ) . "</h2>\n" );
			$wgOut->addWikiText( $wpTextbox1 );
		}
	}

	function viewprintable()
	{
		global $wgOut, $wgUser;

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
		$id = $wgUser->blockedBy();
		$reason = $wgUser->blockedFor();

		$name = User::whoIs( $id );
		$link = "[[User:$name|$name]]";

		$text = str_replace( "$1", $link, wfMsg( "blockedtext" ) );
		$text = str_replace( "$2", $reason, $text );
		$wgOut->addWikiText( $text );
	}
}

?>
