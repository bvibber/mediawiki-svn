<?
function editUserSettings () {
	global $ButtonSave ;
	global $vpage , $user ;
	global $wikiUserSettings , $wikiUserSettingsError , $wikiUserSettingsSaved ;
	$vpage->title = $wikiUserSettings ;
	if ( !$user->isLoggedIn ) return $wikiUserSettingsError ;
	$ret = "" ;

	if ( isset ( $ButtonSave ) ) {
		unset ( $ButtonSave ) ;
		global $QuickBar , $NewTopics , $UnderlineLinks , $ShowHover , $ROWS , $COLS , $doSkin , $VIEWRC ;
		global $OLDPASSWORD , $NEWPASSWORD , $RETYPEPASSWORD , $EMAIL , $RESULTSPERPAGE , $doJustify , $ChangesLayout ;
		global $SHOWSTRUCTURE , $HOURDIFF , $NumberHeadings , $ViewFrames , $encoding , $HideMinor ;
		if ( $RESULTSPERPAGE < 2 ) $RESULTSPERPAGE = 20 ;

		# Checkbox fixing
		if ( $ShowHover == "" ) $ShowHover = "no" ;
		if ( $UnderlineLinks == "" ) $UnderlineLinks = "no" ;
		if ( $NewTopics == "" ) $NewTopics = "normal" ;
		if ( $doJustify == "" ) $doJustify = "no" ;
		if ( $ChangesLayout == "" ) $ChangesLayout = "classic" ;
		if ( $NumberHeadings == "" ) $NumberHeadings = "no" ;

		$user->options["quickBar"] = $QuickBar ;
		$user->options["markupNewTopics"] = $NewTopics ;
		$user->options["underlineLinks"] = $UnderlineLinks ;
		$user->options["viewFrames"] = $ViewFrames ;
		$user->options["showHover"] = $ShowHover ;
		$user->options["cols"] = $COLS ;
		$user->options["rows"] = $ROWS ;
		$user->options["justify"] = $doJustify ;
		$user->options["resultsPerPage"] = $RESULTSPERPAGE ;
		$user->options["skin"] = $doSkin ;
#		$user->options["showStructure"] = $SHOWSTRUCTURE ;
		$user->options["showStructure"] = "no" ; #Subpages turned off
		$user->options["numberHeadings"] = $NumberHeadings ;
		$user->options["changesLayout"] = $ChangesLayout ;
		$user->options["hideMinor"] = $HideMinor ;
		$user->email = $EMAIL ;
		$user->options["hourDiff"] = $HOURDIFF ;
		$user->options["encoding"] = $encoding ;
		$user->options["viewRecentChanges"] = $VIEWRC ;

		if ( $OLDPASSWORD == $user->password ) {
			global $wikiUserSettingsNewPasswordError ;
			if ( $NEWPASSWORD == $RETYPEPASSWORD ) $user->password = $NEWPASSWORD ;
			else $ret .= $wikiUserSettingsNewPasswordError ;
			}

		$user->saveSettings () ;
		$user->loadSettings () ;
		$msg = $wikiUserSettingsSaved ;
		}

	global $wikiLoggedInAs , $wikiID_Help , $wikiViewRecentChanges ;
	global $wikiQuickBarSettings , $wikiSettingsNone , $wikiSettingsStandard , $wikiSettingsLeft , $wikiSettingsRight ;
	global $wikiOldPassword , $wikiNewPassword , $wikiYourPasswordAgain , $wikiSkin , $wikiStarTrek , $wikiNostalgy ;
	global $wikiShowHoverBox , $wikiUnderlineLinks , $wikiNewTopicsRed , $wikiJustifyParagraphs , $wikiShowRecentChangesTable ;
	global $wikiDoNumberHeadings , $wikiViewWithFrames , $wikiTurnedOn , $wikiTurnedOff ;
	global $wikiTextboxDimensions , $wikiCols , $wikiRows , $wikiYourEmail , $wikiResultsPerPage , $wikiTimeDiff , $wikiSave , $wikiReset ;
	global $wikiEncodingNames, $wikiOutputEncoding , $wikiHideMinorEdits ;

	$ret .= str_replace ( "$1" , $user->name , $wikiLoggedInAs ) ;
	$ret .= str_replace ( "$1" , $user->id , $wikiID_Help)."\n" ;
	$ret .= "<nowiki><FORM action=\"".wikiLink("special:editUserSettings")."\" method=post>" ;
	$ret .= "<table border=1 bordercolor=".$user->options["borderColor"]." cellspacing=0 cellpadding=2>" ;

	# QuickBar options
	$qb[$user->options["quickBar"]] = "checked" ;
	$ret .= "<tr><td valign=top nowrap><b>$wikiQuickBarSettings</b><br>\n" ;
	$ret .= "<input type=radio value=none ".$qb["none"]." name=QuickBar>$wikiSettingsNone ($wikiSettingsStandard)<br>\n" ;
	$ret .= "<input type=radio value=left ".$qb["left"]." name=QuickBar>$wikiSettingsLeft<br>\n" ;
	$ret .= "<input type=radio value=right ".$qb["right"]." name=QuickBar>$wikiSettingsRight\n" ;

	# Password change
	$ret .= "</td><td valign=top nowrap><b>Change password :</b><br><font face=courier>\n" ;
  	$ret .= "$wikiOldPassword<INPUT TYPE=password NAME=OLDPASSWORD VALUE=\"\" SIZE=20><br>\n" ;
  	$ret .= "$wikiNewPassword<INPUT TYPE=password NAME=NEWPASSWORD VALUE=\"\" SIZE=20><br>\n" ;
  	$ret .= "$wikiYourPasswordAgain<INPUT TYPE=password NAME=RETYPEPASSWORD VALUE=\"\" SIZE=20>\n" ;
	$ret .= "</font></td></tr>" ;

	# Skin
	$sk[$user->options["skin"]] = "checked" ;
	$ret .= "<tr><td valign=top nowrap><b>$wikiSkin</b><br>\n" ;
	$ret .= "<input type=radio value=None ".$sk["None"]." name=doSkin>$wikiSettingsNone ($wikiSettingsStandard)<br>\n" ;
	$ret .= "<input type=radio value=\"Star Trek\" ".$sk["Star Trek"]." name=doSkin>$wikiStarTrek<br>\n" ;
	$ret .= "<input type=radio value=\"Nostalgy\" ".$sk["Nostalgy"]." name=doSkin>$wikiNostalgy<br>\n" ;

#----------------------------------------------
	$ret .= "</td><td valign=top nowrap>" ;

	# Show Hover
	$sh[$user->options["showHover"]] = "CHECKED" ;
	$ret .= "<input type=checkbox value=yes name=ShowHover ".$sh["yes"].">" ;
	$ret .= "$wikiShowHoverBox ($wikiSettingsStandard:$wikiTurnedOn)<br>\n" ;

	# Underline Links
	$ul[$user->options["underlineLinks"]] = "checked" ;
	$ret .= "<input type=checkbox value=yes name=UnderlineLinks ".$ul["yes"].">" ;
	$ret .= "$wikiUnderlineLinks ($wikiSettingsStandard:$wikiTurnedOn)<br>\n" ;

	# New topics
	$nt[$user->options["markupNewTopics"]] = "checked" ;
	$ret .= "<input type=checkbox value=red name=NewTopics ".$nt["red"].">" ;
	$ret .= "$wikiNewTopicsRed ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	# Justify paragraphs
	$jf[$user->options["justify"]] = "checked" ;
	$ret .= "<input type=checkbox value=yes name=doJustify ".$jf["yes"].">" ;
	$ret .= "$wikiJustifyParagraphs ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	# Recent changes layout
	$cl[$user->options["changesLayout"]] = "checked" ;
	$ret .= "<input type=checkbox value=table name=ChangesLayout ".$cl["table"].">" ;
	$ret .= "$wikiShowRecentChangesTable ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	# Hide minor edits
	$hm[$user->options["hideMinor"]] = "checked" ;
	$ret .= "<input type=checkbox value=yes name=HideMinor ".$hm["yes"].">" ;
	$ret .= "$wikiHideMinorEdits ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	# Auto number headings
	$nh[$user->options["numberHeadings"]] = "checked" ;
	$ret .= "<input type=checkbox value=yes name=NumberHeadings ".$nh["yes"].">" ;
	$ret .= "$wikiDoNumberHeadings ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	# View frames
	$vf[$user->options["viewFrames"]] = "checked" ;
	$ret .= "<input type=checkbox value=yes name=ViewFrames ".$vf["yes"].">" ;
	$ret .= "$wikiViewWithFrames ($wikiSettingsStandard:$wikiTurnedOff)<br>\n" ;

	$ret .= "</td></tr>" ;
#----------------------------------------------

	# Cols and rows
	$ret .= "<tr><td valign=top nowrap><b>$wikiTextboxDimensions</b><br><font face=courier>\n" ;
  	$ret .= "$wikiCols<INPUT TYPE=text NAME=COLS VALUE=\"".$user->options["cols"]."\" SIZE=5><br>\n" ;
  	$ret .= "$wikiRows<INPUT TYPE=text NAME=ROWS VALUE=\"".$user->options["rows"]."\" SIZE=5></font><br><br>\n" ;
#	$ret .= "<font face=courier size=-1>Recommended sizes:<br>1280x1024 : 135x40<br>1024x 768 : <br>&nbsp;800x 600 : </font>" ;

	$ret .= "</td><td valign=top nowrap>" ;
  	$ret .= "<font face=courier>$wikiYourEmail</font><INPUT TYPE=text NAME=EMAIL VALUE=\"".$user->email."\" SIZE=25><br>\n" ;
	$n = explode ( "$1" , $wikiResultsPerPage ) ;
	$ret .= "<font face=courier>".$n[0]."</font><INPUT TYPE=text NAME=RESULTSPERPAGE VALUE=\"".$user->options["resultsPerPage"]."\" SIZE=4>".$n[1]."<br>\n" ;
	$n = explode ( "$1" , $wikiTimeDiff ) ;
  	$ret .= "<font face=courier>".$n[0]."</font><INPUT TYPE=text NAME=HOURDIFF VALUE=\"".$user->options["hourDiff"]."\" SIZE=4>".$n[1]."<br>\n" ;
	$n = explode ( "$1" , $wikiViewRecentChanges ) ;
  	$ret .= "<font face=courier>".$n[0]."</font><INPUT TYPE=text NAME=VIEWRC VALUE=\"".$user->options["viewRecentChanges"]."\" SIZE=4>".$n[1]."\n" ;

	# Encoding
	if(count($wikiEncodingNames) > 1) {
		$ret .= "<br><font face=courier>$wikiOutputEncoding</font><select name=encoding>\n";
		reset($wikiEncodingNames);
		while(list($i, $enc) = each($wikiEncodingNames))
			$ret .= "<option value=\"$i\"".(($user->options["encoding"] == $i)?" selected":"").">$enc</option>\n";
		$ret .= "</select>\n";
		}

	$ret .= "</td></tr>" ;

	$ret .= "<tr><td><center><input type=submit value=\"$wikiSave\" name=ButtonSave></center></td>" ;
	$ret .= "<td><center><input type=reset value=\"$wikiReset\" name=ButtonReset></center></td></tr>" ;

	$ret .= "</table></FORM>$msg</nowiki>" ;
	return $ret ;
	}

?>
