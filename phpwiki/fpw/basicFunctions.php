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

# We unfortunately can't trust the locale functions for now, so we'll roll our own
function ucfirstIntl ( $str ) {
	global $wikiUpperChars , $wikiLowerChars ;
	
	if ( $str == "" ) return $str ;
	
	if ( is_array ( $wikiUpperChars ) ) {
		# Multi-byte charsets or multi-character letters to be capitalised (eg Dutch ij->IJ)
		# FIXME: For now, assuming UTF-8
		return preg_replace ( "/^([\\x00-\\x7f]|[\\xc0-\\xff][\\x80-\\xbf]*)/e", "strtr ( \"\$1\" , \$wikiUpperChars )" , $str ) ;
	}
	
	# Simple single-byte charsets
	return strtr ( substr ( $str , 0 , 1 ) , $wikiLowerChars , $wikiUpperChars ) . substr ( $str , 1 );
	}


function strtoupperIntl ( $str ) {
	global $wikiUpperChars , $wikiLowerChars ;
	
	if ( is_array ( $wikiUpperChars ) )
		return strtr ( $str, $wikiUpperChars ) ;
	return strtr ( $str , $wikiLowerChars , $wikiUpperChars );
	}

function strtolowerIntl ( $str ) {
	global $wikiUpperChars , $wikiLowerChars ;
	
	if ( is_array ( $wikiUpperChars ) )
		return strtr ( $str, $wikiLowerChars ) ;
	return strtr ( $str , $wikiUpperChars , $wikiLowerChars );
	}

function isBlockedIP () {
	$ip = getenv ( REMOTE_ADDR ) ;
	$list = getMySQL ( "cur" , "cur_text" , "cur_title=\"Wikipedia:Blocked_IPs\"" ) ;
	$list = explode ( "*$ip (" , $list ) ; # The most memory-wasting substring search ever!
	if ( count ( $list ) > 1 ) return true ;
	return false ;
	}

# Called when editing/saving a page
function edit ( $title ) {
	global $EditBox , $SaveButton , $PreviewButton , $MinorEdit , $FromEditForm , $wikiIPblocked ;
	global $user , $CommentBox , $vpage , $EditTime , $wikiDescribePage , $wikiUser , $namespaceBackground , $wikiNamespaceBackground ;
	global $wikiCannotEditPage , $wikiEditConflictMessage , $wikiPreviewAppend , $wikiEditHelp , $wikiRecodeInput ;
	global $wikiSummary , $wikiMinorEdit , $wikiCopyrightNotice , $wikiSave , $wikiPreview , $wikiDontSaveChanges , $wikiGetDate ;
	global $wikiBeginDiff, $wikiEndDiff;
	$npage = new WikiPage ;
	$npage->title = $title ;
	$npage->makeAll () ;
	if ( $npage->namespace ) $namespaceBackground = $wikiNamespaceBackground[strtolower($npage->namespace)] ;
	$ret = "" ;
	if ( !$vpage->canEdit() ) return $wikiCannotEditPage ;
	if ( $EditTime == "" ) $EditTime = date ( "YmdHis" ) ; # Stored for edit conflict detection
	$editConflict = false ;

	if ( isset($FromEditForm) and !isset($SaveButton) and !isset($PreviewButton) ) $SaveButton = "yes" ;

	# Landuage recoding
	$EditBox = $wikiRecodeInput ( $EditBox ) ;
	$CommentBox = $wikiRecodeInput ( $CommentBox ) ;

	if ( $SaveButton ) { # The edit is finished, someone pressed the "Save" button
		$SaveButton = "" ;
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
				#$oldSubmittedText = str_replace ( "&" , "&amp;" , $oldSubmittedText ) ;
				$EditTime = date ( "YmdHis" ) ; # reset time counter
				$npage->load ( $npage->title ) ;
				$text = $npage->contents ;
				#$text = str_replace ( "&" , "&amp;" , $text ) ;

				$editConflict = true ;
				}
			}
		if ( $doSave ) { # Actually saving the article!
			$text = $EditBox ;
			$text = str_replace ( "\\'" , "'" , $text ) ;
			$text = str_replace ( "\\\"" , "\"" , $text ) ;

			if ( $user->isLoggedIn ) $replText = "[[$wikiUser:$user->name|$user->name]]" ;
			else $replText = $user->getLink() ;
			$dt = $wikiGetDate ( time() ) ;

			$text = str_replace ( "~~~~" , "$replText, $dt" , $text ) ;
			$text = str_replace ( "~~~" , $replText , $text ) ;

			$title = str_replace ( "\\'" , "'" , $title ) ;
			$title = str_replace ( "\\\"" , "\"" , $title ) ;
			$title = str_replace ( "\\\\" , "\\" , $title ) ;
			$npage->title = $title ;
			$npage->makeAll () ;
			if ( $npage->doesTopicExist() ) $npage->backup() ;
			else { $MinorEdit = 2 ; $npage->ensureExistence () ; }

			# Checking for blocked IP
			if ( isBlockedIP() ) return $wikiIPblocked ;

			if ( !$user->isLoggedIn ) $npage->setEntry ( $text , $CommentBox , 0 , $user->getLink() , $MinorEdit*1 ) ;
			else $npage->setEntry ( $text , $CommentBox , $user->id , $user->name , $MinorEdit*1 ) ;
			global $wasSaved ;
			$wasSaved = true ;
			return "" ;
			}
	} else if ( $PreviewButton ) { # Generating a preview to append to the page
		$PreviewButton = "" ;
		$text = $EditBox ;
		$text = str_replace ( "\\'" , "'" , $text ) ;
		$text = str_replace ( "\\\"" , "\"" , $text ) ;
		$text = str_replace ( "\\\\" , "\\" , $text ) ;
		$append = str_replace ( "$1" , $npage->parseContents($text) , $wikiPreviewAppend ) ;
	} else if ( $npage->doesTopicExist() ) { # The initial edit request for an existing page
		$npage->load ( $npage->title ) ;
		$text = $npage->contents ;
	} else { # The initial edit request for a new page
		$text = $wikiDescribePage ;
		}

	#$text = htmlspecialchars ( $text ) ;

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
	$ret .= "<textarea tabindex=1 name=EditBox rows=".$user->options["rows"]." cols=".$user->options["cols"]." STYLE=\"width:100%\" WRAP=virtual>".htmlspecialchars ( $text )."</textarea><br>\n" ;
	$ret .= "$wikiSummary<input tabindex=2 type=text value=\"$CommentBox\" name=CommentBox size=50 maxlength=200> \n" ;
	if ( $user->isLoggedIn == "yes" ) 
		$ret .= "<input tabindex=3 type=checkbox name=MinorEdit $checked value=1>$wikiMinorEdit &nbsp; " ;
	else
		$ret .= "<input type=hidden name=MinorEdit value=0>" ;
	$ret .= "<br>$wikiCopyrightNotice<br>\n" ;

	$ret .= "<input tabindex=4 type=submit value=$wikiSave name=SaveButton> \n" ;
	$ret .= "<input tabindex=5 type=submit value=$wikiPreview name=PreviewButton>\n" ;
	$ret .= "<input type=hidden value=\"$EditTime\" name=EditTime>\n" ;
	$ret .= "<input type=hidden value=yes name=FromEditForm>\n" ;
	$ret .= " <a href=\"".wikiLink($vpage->url)."\">$wikiDontSaveChanges</a>\n" ; 
	$ret .= " | <a href=\"".wikiLink("wikipedia:How_does_one_edit_a_page")."\">$wikiEditHelp</a>\n" ;

	if ( $editConflict ) {
		# Add the diffs between the two competing versions:
		$ret .= "<br><hr><nowiki><font color=red><b>$wikiBeginDiff</b></font><br>\n\n" ;
		$old_lines = explode ( "\n" , htmlspecialchars( $oldSubmittedText ) ) ;
		$new_lines = explode ( "\n" , htmlspecialchars( $text ) ) ;
		#$old_lines = explode ( "\n" , $oldSubmittedText ) ;
		#$new_lines = explode ( "\n" , $text ) ;
		include_once( "./difflib.php" );
		$diffs = new Diff($old_lines, $new_lines);
		$formatter = new TableDiffFormatter();
		$ret .= $formatter->format($diffs);
		$ret .= "<font color=red><b>$wikiEndDiff</b></font><hr></nowiki>\n" ;
		$ret .= "<br><b>This is the text you submitted :</b><br>\n" ;
		$ret .= "<textarea name=NotIMPORTANT rows=".$user->options["rows"]." cols=".$user->options["cols"]." STYLE=\"width:100%\" WRAP=virtual>" . htmlspecialchars ( $oldSubmittedText ) . "</textarea><br>\n" ;
	      }

	$ret .= " </form>\n" ;

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
	global $FromEditForm , $action , $namespaceBackground , $wikiNamespaceBackground ;
	global $redirect ;
	global $vpage , $wikiDescribePage ;
	if ( $FromEditForm ) {
		$s = doEdit ( $title ) ;
		$FromEditForm = "" ;
		$action = "edit" ;
		if ( $s != "" ) return $s ;
		$action = "view" ;
		}
	$vpage = new WikiPage ;
	if ( $redirect == "no" )
	    # Don't follow redirects if global $redirect is "no":
	    $vpage->load ( $title, false) ;
	else 
	    $vpage->load ( $title, true) ;
	if ( $vpage->namespace ) $namespaceBackground = $wikiNamespaceBackground[strtolower($vpage->namespace)] ;
	if ( $vpage->contents == $wikiDescribePage ) {
		$action = "edit" ;
		return doEdit ( $vpage->title ) ;
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
