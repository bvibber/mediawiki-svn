<?
function wfSpecialPreferences()
{
	global $wgUser, $wgOut;
	global $wpSaveprefs, $wpReset;

	if ( 0 == $wgUser->getID() ) {
		$wgOut->errorpage( "prefsnologin", "prefsnologintext" );
		return;
	}
	if ( isset( $wpSaveprefs ) ) {
		savePreferences();
	} else if ( isset( $wpReset ) ) {
		resetPrefs();
		mainPrefsForm( WfMsg( "prefsreset" ) );
	} else {
		resetPrefs();
		mainPrefsForm( "" );
	}
}

/* private */ function savePreferences()
{
	global $wgUser, $wgLang, $wgDeferredUpdateList;
	global $wpQuickbar, $wpOldpass, $wpNewpass, $wpRetype;
	global $wpSkin, $wpEmail, $wpNick, $wpSearch, $wpRecent;
	global $wpRows, $wpCols, $HTTP_POST_VARS;

	if ( "" != $wpNewpass ) {
		if ( $wpNewpass != $wpRetype ) {
			mainPrefsForm( wfMsg( "badretype" ) );			
			return;
		}
		$ep = User::encryptPassword( $wpOldpass );
		if ( $ep != $wgUser->getPassword() ) {
			if ( $ep != $wgUser->getNewpassword() ) {
				mainPrefsForm( wfMsg( "wrongpassword" ) );
				return;
			}
		}
		$wgUser->setPassword( $wpNewpass );
	}
	$wgUser->setEmail( $wpEmail );
	$wgUser->setOption( "nickname", $wpNick );
	$wgUser->setOption( "quickbar", $wpQuickbar );
	$wgUser->setOption( "skin", $wpSkin );
	$wgUser->setOption( "searchlimit", $wpSearch );
	$wgUser->setOption( "rclimit", $wpRecent );
	$wgUser->setOption( "rows", $wpRows );
	$wgUser->setOption( "cols", $wpCols );

	$togs = $wgLang->getUserToggles();
	foreach ( $togs as $tname => $ttext ) {
		$wgUser->setOption( $tname, $HTTP_POST_VARS["wpOp$tname"] );
	}
	$wgUser->setCookies();
	$up = new UserUpdate();
	array_push( $wgDeferredUpdateList, $up );
	mainPrefsForm( wfMsg( "savedprefs" ) );
}

/* private */ function resetPrefs()
{
	global $wgUser, $wgLang;
	global $wpQuickbar, $wpOldpass, $wpNewpass, $wpRetype;
	global $wpRows, $wpCols, $wpSkin, $wpEmail, $wpNick;
	global $wpSearch, $wpRecent, $HTTP_POST_VARS;

	$wpOldpass = $wpNewpass = $wpRetype = "";
	$wpEmail = $wgUser->getEmail();
	$wpNick = $wgUser->getOption( "nickname" );

	$wpQuickbar = $wgUser->getOption( "quickbar" );
	$wpSkin = $wgUser->getOption( "skin" );
	$wpRows = $wgUser->getOption( "rows" );
	$wpCols = $wgUser->getOption( "cols" );
	$wpSearch = $wgUser->getOption( "searchlimit" );
	$wpRecent = $wgUser->getOption( "rclimit" );

	$togs = $wgLang->getUserToggles();
	foreach ( $togs as $tname => $ttext ) {
		$HTTP_POST_VARS["wpOp$tname"] = $wgUser->getOption( $tname );
	}
}

/* private */ function mainPrefsForm( $err )
{
	global $wgUser, $wgOut, $wgLang;
	global $wpQuickbar, $wpOldpass, $wpNewpass, $wpRetype;
	global $wpSkin, $wpEmail, $wpNick, $wpSearch, $wpRecent;
	global $wpRows, $wpCols, $wpSaveprefs, $wpReset;

	$wgOut->setPageTitle( wfMsg( "preferences" ) );
	$wgOut->setArticleFlag( false );

	if ( "" != $err ) {
		$wgOut->addHTML( "<font size='+1' color='red'>$err</font>\n<p>" );
	}
	$uname = $wgUser->getName();
	$uid = $wgUser->getID();

	$wgOut->addHTML( "<p>You are logged in as \"$uname\".\n" );
	$wgOut->addHTML( "Your internal ID number is $uid.\n" );

	$qbs = $wgLang->getQuickbarSettings();
	$skins = $wgLang->getSkinNames();
	$togs = $wgLang->getUserToggles();

	$action = wfLocalUrl( "Special:Preferences" );
	$qb = wfMsg( "qbsettings" );
	$cp = wfMsg( "changepassword" );
	$sk = wfMsg( "skin" );
	$opw = wfMsg( "oldpassword" );
	$npw = wfMsg( "newpassword" );
	$rpw = wfMsg( "retypenew" );
	$svp = wfMsg( "saveprefs" );
	$rsp = wfMsg( "resetprefs" );
	$tbs = wfMsg( "textboxsize" );
	$tbr = wfMsg( "rows" );
	$tbc = wfMsg( "columns" );
	$yem = wfMsg( "youremail" );
	$ynn = wfMsg( "yournick" );
	$rpp = wfMsg( "resultsperpage" );
	$rcc = wfMsg( "recentchangescount" );

	$wgOut->addHTML( "<form action=\"$action\" method=post>
<table border=\"1\"><tr><td valign=top nowrap><b>$qb:</b><br>\n" );

	# Quickbar setting
	#
	for ( $i = 0; $i < count( $qbs ); ++$i ) {
		if ( $i == $wpQuickbar ) { $checked = " checked"; }
		else { $checked = ""; }
		$wgOut->addHTML( "<input type=radio name=\"wpQuickbar\"
value=\"$i\"$checked> {$qbs[$i]}<br>\n" );
	}

	# Fields for changing password
	#
	$wpOldpass = wfEscapeHTML( $wpOldpass );
	$wpNewpass = wfEscapeHTML( $wpNewpass );
	$wpRetype = wfEscapeHTML( $wpRetype );

	$wgOut->addHTML( "</td><td vaign=top nowrap><b>$cp:</b><br>
$opw: <input type=password name=\"wpOldpass\" value=\"$wpOldpass\" size=20><br>
$npw: <input type=password name=\"wpNewpass\" value=\"$wpNewpass\" size=20><br>
$rpw: <input type=password name=\"wpRetype\" value=\"$wpRetype\" size=20><br>
</td></tr>\n" );

	# Skin setting
	#
	$wgOut->addHTML( "<tr><td valign=top nowrap><b>$sk:</b><br>\n" );
	for ( $i = 0; $i < count( $skins ); ++$i ) {
		if ( $i == $wpSkin ) { $checked = " checked"; }
		else { $checked = ""; }
		$wgOut->addHTML( "<input type=radio name=\"wpSkin\"
value=\"$i\"$checked> {$skins[$i]}<br>\n" );
	}

	# Various checkbox options
	#
	$wgOut->addHTML( "</td><td valign=top nowrap>\n" );
	foreach ( $togs as $tname => $ttext ) {
		if ( 1 == $wgUser->getOption( $tname ) ) {
			$checked = " checked";
		} else {
			$checked = "";
		}
		$wgOut->addHTML( "<input type=checkbox value=\"1\" "
		  . "name=\"wpOp$tname\"$checked>$ttext<br>\n" );
	}
	$wgOut->addHTML( "</td></tr><tr>" );

	# Textbox rows, cols
	#
	$wgOut->addHTML( "<td valign=top nowrap><b>$tbs:</b><br>
$tbr: <input type=text name=\"wpRows\" value=\"$wpRows\" size=6><br>
$tbc: <input type=text name=\"wpCols\" value=\"$wpCols\" size=6>
</td>" );

	# Email, etc.
	#
	$wpEmail = wfEscapeHTML( $wpEmail );
	$wpNick = wfEscapeHTML( $wpNick );

	$wgOut->addHTML( "<td valign=top nowrap>
$yem: <input type=text name=\"wpEmail\" value=\"$wpEmail\" size=20><br>
$ynn: <input type=text name=\"wpNick\" value=\"$wpNick\" size=12><br>
$rpp: <input type=text name=\"wpSearch\" value=\"$wpSearch\" size=6><br>
$rcc: <input type=text name=\"wpRecent\" value=\"$wpRecent\" size=6></td>
</tr><tr>
<td align=center><input type=submit name=\"wpSaveprefs\" value=\"$svp\"></td>
<td align=center><input type=submit name=\"wpReset\" value=\"$rsp\"></td>
</tr></table></form>\n" );
}

?>
