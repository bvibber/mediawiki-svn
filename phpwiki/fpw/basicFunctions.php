<?
# Output error message. Rarely used.
function error ( $error ) {
	global $wikiErrorPageTitle , $wikiErrorMessage ;
	$page = new WikiPage ;
	$page->special ( $wikiErrorPageTitle ) ;
	$page->contents = str_replace ( "$1" , "$error" , $wikiErrorMessage ) ;
	return $page->renderPage () ;
	}

# Make a nice URL
function nurlencode ( $s ) {
	$ulink = urlencode ( $s ) ;
	$ulink = str_replace ( "%3A" , ":" , $ulink ) ;
	$ulink = str_replace ( "%2F" , "/" , $ulink ) ;
	return $ulink ;
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
	global $EditBox , $SaveButton , $PreviewButton , $MinorEdit , $FromEditForm ;
	global $user , $CommentBox , $vpage , $EditTime , $wikiDescribePage ;
	global $wikiCannotEditPage , $wikiEditConflictMessage , $wikiPreviewAppend , $wikiEditHelp , $wikiRecodeInput ;
	global $wikiSummary , $wikiMinorEdit , $wikiCopyrightNotice , $wikiSave , $wikiPreview , $wikiDontSaveChanges ;
	$npage = new WikiPage ;
	$npage->title = $title ;
	$npage->makeAll () ;
	$ret = "" ;
	if ( !$vpage->canEdit() ) return $wikiCannotEditPage ;
	if ( $EditTime == "" ) $EditTime = date ( "YmdHis" ) ; # Stored for edit conflict detection
	$editConflict = false ;

	if ( isset($FromEditForm) and !isset($SaveButton) and !isset($PreviewButton) ) $SaveButton = "yes" ;

	# Landuage recoding
	$EditBox = $wikiRecodeInput ( $EditBox ) ;
	$CommentBox = $wikiRecodeInput ( $CommentBox ) ;

	if ( isset ( $SaveButton ) ) { # The edit is finished, someone pressed the "Save" button
		unset ( $SaveButton ) ;
		$doSave = true ;
		if ( $vpage->doesTopicExist() ) {
			$lastTime = getMySQL ( "cur" , "cur_timestamp" , "cur_title=\"$vpage->secureTitle\"" ) ;
			if ( tsc($EditTime) < tsc($lastTime) ) {
				$doSave = false ;
				$ret .= $wikiEditConflictMessage ;
				$oldSubmittedText = $EditBox ;
				$oldSubmittedText = str_replace ( "\\'" , "'" , $oldSubmittedText ) ;
				$oldSubmittedText = str_replace ( "\\\"" , "\"" , $oldSubmittedText ) ;
				$oldSubmittedText = str_replace ( "\\\\" , "\\" , $oldSubmittedText ) ;
				$oldSubmittedText = str_replace ( "&" , "&amp;" , $oldSubmittedText ) ;
				$EditTime = date ( "YmdHis" ) ; # reset time counter
				$npage->load ( $npage->title ) ;
				$text = $npage->contents ;
				$text = str_replace ( "&" , "&amp;" , $text ) ;
				$editConflict = true ;
				}
			}
		if ( $doSave ) {
			$text = $EditBox ;
			$text = str_replace ( "\\'" , "'" , $text ) ;
			$text = str_replace ( "\\\"" , "\"" , $text ) ;
#			$text = urldecode ( $text ) ;
#			$text = str_replace ( "&" , "&amp;" , $text ) ;
			if ( $user->isLoggedIn ) $text = str_replace ( "~~~" , "[[user:$user->name|$user->name]]" , $text ) ;
			else $text = str_replace ( "~~~" , $user->getLink() , $text ) ;
			$title = str_replace ( "\\'" , "'" , $title ) ;
			$title = str_replace ( "\\\"" , "\"" , $title ) ;
			$title = str_replace ( "\\\\" , "\\" , $title ) ;
			$npage->title = $title ;
			$npage->makeAll () ;
			if ( $npage->doesTopicExist() ) $npage->backup() ;
			else { $MinorEdit = 2 ; $npage->ensureExistence () ; }
			if ( !$user->isLoggedIn ) $npage->setEntry ( $text , $CommentBox , 0 , $user->getLink() , $MinorEdit*1 ) ;
			else $npage->setEntry ( $text , $CommentBox , $user->id , $user->name , $MinorEdit*1 ) ;
			global $wasSaved ;
			$wasSaved = true ;
			return "" ;
			}
	} else if ( isset ( $PreviewButton ) ) { # Generating a preview to append to the page
		unset ( $PreviewButton ) ;
		$text = $EditBox ;
		$text = str_replace ( "\\'" , "'" , $text ) ;
		$text = str_replace ( "\\\"" , "\"" , $text ) ;
		$text = str_replace ( "\\\\" , "\\" , $text ) ;
#		$text = urldecode ( $text ) ;
		$text = str_replace ( "&" , "&amp;" , $text ) ;
		$append = str_replace ( "$1" , $npage->parseContents($text) , $wikiPreviewAppend ) ;
	} else if ( $npage->doesTopicExist() ) { # The initial edit request for an existing page
		$npage->load ( $npage->title ) ;
		$text = $npage->contents ;
		$text = str_replace ( "&" , "&amp;" , $text ) ;
	} else { # The initial edit request for a new page
		$text = $wikiDescribePage ;
		}

	if ( $MinorEdit ) $checked = "checked" ;
	else $checked = "" ;
	if ( $CommentBox == "" ) $CommentBox = "*" ;
	$CommentBox = str_replace ( array ( "\\'", "\\\"", "\\\\" ) , array ( "'" , "\"", "\\" ) , $CommentBox );
	$CommentBox = htmlspecialchars ( $CommentBox );

	# Just trying to set the initial keyboard focus to the edit window; doesn't work, though...
	global $bodyOptions , $headerScript ;
#JAVASCRIPT DEACTIVATED
#	$headerScript = "<script> <!-- function setfocus() { document.f.EditBox.focus(); } --> </script>" ;
#	$bodyOptions = " onLoad=setfocus()" ;

	$ret .= "<form method=POST action=\"".wikiLink($npage->url)."\" name=f\" enctype=\"application/x-www-form-urlencoded\">" ;
	$ret .= "<textarea tabindex=1 name=EditBox rows=".$user->options["rows"]." cols=".$user->options["cols"]." STYLE=\"width:100%\" WRAP=virtual>".$text."</textarea><br>\n" ;
	$ret .= "$wikiSummary<input tabindex=2 type=text value=\"$CommentBox\" name=CommentBox size=50 maxlength=200> \n" ;
	if ( $user->isLoggedIn == "yes" ) 
		$ret .= "<input tabindex=3 type=checkbox name=MinorEdit $checked value=1>$wikiMinorEdit &nbsp; " ;
	else
		$ret .= "<input type=hidden name=MinorEdit value=1>" ;
	$ret .= "$wikiEditHelp<br>\n" ;
	$ret .= "$wikiCopyrightNotice<br>\n" ;

	$ret .= "<input tabindex=4 type=submit value=$wikiSave name=SaveButton> \n" ;
	$ret .= "<input tabindex=5 type=submit value=$wikiPreview name=PreviewButton>\n" ;
	$ret .= "<input type=hidden value=\"$EditTime\" name=EditTime>\n" ;
	$ret .= "<input type=hidden value=yes name=FromEditForm>\n" ;
	$ret .= " <a href=\"".wikiLink($vpage->url)."\">$wikiDontSaveChanges</a></form>" ; 
	if ( $editConflict ) {
		$ret .= "<br><hr><br><b>This is the text you submitted :</b><br>\n" ;
		$ret .= "<textarea name=NotIMPORTANT rows=".$user->options["rows"]." cols=".$user->options["cols"]." STYLE=\"width:100%\" WRAP=virtual>$oldSubmittedText</textarea><br>\n" ;
		}

	return $ret.$append ;
	}

function doEdit ( $title ) {
	global $headerScript ;
	global $vpage , $action , $wasSaved ;
	$wasSaved = false ;
	$vpage = new WikiPage ;
	$vpage->isSpecialPage = true ;
	$vpage->SetTitle ( $title ) ;
	$action = "" ;
	$ret = $vpage->getHeader() ;
	$action = "edit" ;

	$theMiddle = edit ( $title ) ;
	if ( $wasSaved ) {
		return "" ;
#		$action = "view" ;
#		return view ( $title ) ;
		}
	$ret .= $vpage->getMiddle ( $theMiddle ) ;

	$action = "" ;
	$ret .= $vpage->getFooter() ;
	$action = "edit" ;
	return $ret ;
	}

function view ( $title ) {
	global $FromEditForm , $action ;
	global $vpage , $wikiDescribePage ;
	if ( $FromEditForm ) {
		$s = doEdit ( $title ) ;
		$FromEditForm = "" ;
		unset ( $FromEditForm ) ;
		$action = "edit" ;
		if ( $s != "" ) return $s ;
		$action = "view" ;
		}
	$vpage = new WikiPage ;
	$vpage->load ( $title ) ;
	if ( $vpage->contents == $wikiDescribePage ) {
		$action = "edit" ;
		return doEdit ( $title ) ;
		}
	return $vpage->renderPage () ;
	}

function doPrint ( $title ) {
	global $vpage ;
	$vpage = new WikiPage ;
	$vpage->load ( $title ) ;
	return $vpage->renderPage ( true ) ;
	}
?>
