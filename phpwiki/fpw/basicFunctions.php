<?
# Output error message. Rarely used.
function error ( $error ) {
	$page = new WikiPage ;
	$page->special ( "Yikes! An error!" ) ;
	$page->contents = "<h2>$error!</h2>Return to the [[:HomePage|HomePage]]!" ;
	return $page->renderPage () ;
	}

# Convert MySQL timestame to date
function tsc ( $t ) {
	$year = substr ( $t , 0 , 4 ) ;
	$month = substr ( $t , 4 , 2 ) ;
	$day = substr ( $t , 6 , 2 ) ;
	$hour = substr ( $t , 8 , 2 ) ;
	$min = substr ( $t , 10 , 2 ) ;
	$sec = substr ( $t , 12 , 2 ) ;
	return mktime ( $hour , $min , $sec , $month , $day , $year ) ;
	}

# Called when editing/saving a page
function edit ( $title ) {
	global $EditBox , $SaveButton , $PreviewButton , $MinorEdit ;
	global $user , $CommentBox , $vpage , $EditTime ;
	$npage = new WikiPage ;
	$npage->title = $title ;
	$npage->makeAll () ;
	$ret = "" ;
	if ( !$vpage->canEdit() ) return "<h3>You cannot edit this page!</h3>" ; # Check for allowance

	if ( $EditTime == "" ) $EditTime = date ( "YmdHis" ) ; # Stored for edit conflict detection

	if ( isset ( $SaveButton ) ) { # The edit is finished, someone pressed the "Save" button
		unset ( $SaveButton ) ;
		if ( $vpage->doesTopicExist() ) {
			$lastTime = getMySQL ( "cur" , "cur_timestamp" , "cur_title=\"$vpage->secureTitle\"" ) ;
			if ( tsc($EditTime) < tsc($lastTime) ) return "<h1>While you were typing, someone saved another version of this article!</h1>" ;
			}
		$text = $EditBox ;
		$text = str_replace ( "\\'" , "'" , $text ) ;
		$text = str_replace ( "\\\"" , "\"" , $text ) ;
		if ( $user->isLoggedIn ) $text = str_replace ( "~~~" , "[[user:$user->name|$user->name]]" , $text ) ;
		else $text = str_replace ( "~~~" , $user->getLink() , $text ) ;
		$title = str_replace ( "\\'" , "'" , $title ) ;
		$title = str_replace ( "\\\"" , "\"" , $title ) ;
		$npage->title = $title ;
		$npage->makeAll () ;
		if ( $npage->doesTopicExist() ) $npage->backup() ;
		else { $MinorEdit = 2 ; $npage->ensureExistence () ; }
		if ( !$user->isLoggedIn ) $npage->setEntry ( $text , $CommentBox , 0 , $user->getLink() , $MinorEdit*1 ) ;
		else $npage->setEntry ( $text , $CommentBox , $user->id , $user->name , $MinorEdit*1 ) ;
		global $wasSaved ;
		$wasSaved = true ;
		return "" ;
	} else if ( isset ( $PreviewButton ) ) { # Generating a preview to append to the page
		unset ( $PreviewButton ) ;
		$text = $EditBox ;
		$text = str_replace ( "\\'" , "'" , $text ) ;
		$text = str_replace ( "\\\"" , "\"" , $text ) ;
		$append = "<hr>\n<h2>Preview :</h2>\n".$npage->parseContents($text)."<hr><h3>Remember, this is only a preview and not yet saved!</h3>" ;
	} else if ( $npage->doesTopicExist() ) { # The initial edit request for an existing page
		$npage->load ( $npage->title ) ;
		$text = $npage->contents ;
	} else { # The initial edit request for a new page
		$text = "Describe the new page here." ;
		}

	if ( $MinorEdit ) $checked = "checked" ;
	else $checked = "" ;
	if ( $CommentBox == "" ) $CommentBox = "*" ;


	# Just trying to set the initial keyboard focus to the edit window; doesn't work, though...
	global $bodyOptions , $headerScript ;
	$headerScript = "<script> <!-- function setfocus() { document.f.EditBox.focus(); } --> </script>" ;
	$bodyOptions = " onLoad=setfocus()" ;

	$ret .= "<form method=POST name=f>" ;
	$ret .= "<textarea tabindex=1 name=EditBox rows=".$user->options["rows"]." cols=".$user->options["cols"]." STYLE=\"width:100%\" WRAP=virtual>$text</textarea><br>\n" ;
	$ret .= "Summary:<input tabindex=2 type=text value=\"$CommentBox\" name=CommentBox size=50 maxlength=200> \n" ;
	$ret .= "<input tabindex=3 type=checkbox name=MinorEdit $checked value=1>This is a minor edit \n" ;
	$ret .= "<input tabindex=4 type=submit value=Save name=SaveButton> \n" ;
	$ret .= "<input tabindex=5 type=submit value=Preview name=PreviewButton>\n" ;
	$ret .= "<input type=hidden value=\"$EditTime\" name=EditTime>\n" ;
	$ret .= "</form>" ;

	return $ret.$append ;
	}

function doEdit ( $title ) {
	global $THESCRIPT , $headerScript ;
	global $vpage , $action , $wasSaved ;
	$wasSaved = false ;
	$vpage = new WikiPage ;
	$vpage->isSpecialPage = true ;
	$vpage->title = $title ;
	$vpage->makeSecureTitle () ;
	$action = "" ;
	$ret = $vpage->getHeader() ;
	$action = "edit" ;

	$theMiddle = edit ( $title ) ;
	if ( $wasSaved ) {
		$ti = new wikiTitle ;
		$ti->setTitle ( $title ) ;
		$theMiddle = "<h1>Your page <a href=\"$THESCRIPT?title=$ti->secureTitle\">$title</a> was successfully saved!</h1>" ;
		$theMiddle .= "(If this page doesn't forward automatically, you have a really lame browser...)" ;
		$headerScript .= "<META HTTP-EQUIV=Refresh CONTENT=\"0; URL=$THESCRIPT?title=$vpage->secureTitle\">";
		}
	$ret .= $vpage->getMiddle ( $theMiddle ) ;

	$action = "" ;
	$ret .= $vpage->getFooter() ;
	$action = "edit" ;
	return $ret ;
	}

function view ( $title ) {
	global $vpage ;
	$vpage = new WikiPage ;
	$vpage->load ( $title ) ;
	return $vpage->renderPage () ;
	}

function doPrint ( $title ) {
	global $vpage ;
	$vpage = new WikiPage ;
	$vpage->load ( $title ) ;
	return $vpage->renderPage ( true ) ;
	}
?>