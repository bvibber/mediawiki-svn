<?

function wfSpecialUserlogin()
{
	global $wpCreateaccount, $wpLoginattempt, $wpMailmypassword;

	if ( isset( $wpCreateaccount ) ) {
		addNewAccount();
	} else if ( isset( $wpLoginattempt ) ) {
		processLogin();
	} else if ( isset( $wpMailmypassword ) ) {
		mailPassword();
	} else {
		mainLoginForm( "" );
	}
}

/* private */ function addNewAccount()
{
	global $wgUser, $wpPassword, $wpRetype, $wpName, $wpRemember;
	global $wpEmail, $wgDeferredUpdateList;

	if ( 0 != strcmp( $wpPassword, $wpRetype ) ) {
		mainLoginForm( wfMsg( "badretype" ) );
		return;
	}
	if ( "" == $wpName ) {
		mainLoginForm( wfMsg( "noname" ) );
		return;
	}
	$u = User::newFromName( $wpName );

	if ( 0 != $u->idForName() ) {
		mainLoginForm( wfMsg( "userexists" ) );
		return;
	}
	$u->addToDatabase();
	$u->setPassword( $wpPassword );
	$u->setEmail( $wpEmail );
	if ( 1 == $wpRemember ) { $r = 1; }
	else { $r = 0; }
	$u->setOption( "rememberPassword", $r );

	$wgUser = $u;
	$m = str_replace( "$1", $wgUser->getName(), wfMsg( "welcomecreation" ) );
	successfulLogin( $m );
}

/* private */ function processLogin()
{
	global $wgUser, $wpName, $wpPassword, $wpRemember;
	global $returnto;

	if ( "" == $wpName ) {
		mainLoginForm( wfMsg( "noname" ) );
		return;
	}
	$u = User::newFromName( $wpName );
	$id = $u->idForName();
	if ( 0 == $id ) {
		$m = str_replace( "$1", $u->getName(), wfMsg( "nosuchuser" ) );
		mainLoginForm( $m );
		return;
	}
	$u->setId( $id );
	$u->loadFromDatabase();
	if ( 0 != strcmp( $u->getPassword(), $wpPassword ) ) {
		mainLoginForm( wfMsg( "wrongpassword" ) );
		return;
	}
	# We've verified now, update the real record
	#
	if ( 1 == $wpRemember ) { $r = 1; }
	else { $r = 0; }
	$u->setOption( "rememberPassword", $r );

	$wgUser = $u;
	$m = str_replace( "$1", $wgUser->getName(), wfMsg( "loginsuccess" ) );
	successfulLogin( $m );
}

/* private */ function mailPassword()
{
	global $wpName;

	if ( "" == $wpName ) {
		mainLoginForm( wfMsg( "noname" ) );
		return;
	}
	$u = User::newFromName( $wpName );
	$id = $u->idForName();
	if ( 0 == $id ) {
		$m = str_replace( "$1", $u->getName(), wfMsg( "nosuchuser" ) );
		mainLoginForm( $m );
		return;
	}
	$u->setId( $id );
	$u->loadFromDatabase();

	if ( "" == $u->getEmail() ) {
		$m = str_replace( "$1", $u->getName(), wfMsg( "noemail" ) );
		mainLoginForm( $m );
		return;
	}
	$m = str_replace( "$1", $u->getName(), wfMsg( "passwordremindertext" ) );
	$m = str_replace( "$2", $u->getPassword(), $m );

	mail( $u->getEmail(), wfMsg( "passwordremindertitle" ), $m );
	$m = str_replace( "$1", $u->getName(), wfMsg( "passwordsent" ) );
	mainLoginForm( $m );
}

/* private */ function successfulLogin( $msg )
{
	global $wgUser, $wgOut, $returnto;
	global $wgDeferredUpdateList;

	$wgUser->setCookies();
	$up = new UserUpdate();
	array_push( $wgDeferredUpdateList, $up );

	$wgOut->setPageTitle( wfMsg( "loginsuccesstitle" ) );
	$wgOut->setRobotpolicy( "noindex,nofollow" );
	$wgOut->setArticleFlag( false );
	$wgOut->addHTML( $msg . "\n<p>" );
	$wgOut->returnToMain();
}

/* private */ function mainLoginForm( $err )
{
	global $wgUser, $wgOut, $returnto;
	global $wpName, $wpPassword, $wpRetype, $wpRemember;
	global $wpEmail, $HTTP_COOKIE_VARS;

	$le = wfMsg( "loginerror" );
	$yn = wfMsg( "yourname" );
	$yp = wfMsg( "yourpassword" );
	$ypa = wfMsg( "yourpasswordagain" );
	$rmp = wfMsg( "remembermypassword" );
	$ayn = wfMsg( "areyounew" );
	$nuo = wfMsg( "newusersonly" );
	$li = wfMsg( "login" );
	$ca = wfMsg( "createaccount" );
	$ye = wfMsg( "youremail" );
	$efl = wfMsg( "emailforlost" );
	$mmp = wfMsg( "mailmypassword" );

	$name = $wpName;
	if ( "" == $name ) {
		if ( 0 != $wgUser->getID() ) {
			$name = $wgUser->getName();
		} else {
			$name = $HTTP_COOKIE_VARS["wcUserName"];
		}
	}
	$pwd = $wpPassword;
	if ( "" == $pwd ) {
		if ( 0 != $wgUser->getID() ) {
			$pwd = $wgUser->getPassword();
		} else {
			$pwd = $HTTP_COOKIE_VARS["wcUserPassword"];
		}
	}
	$wgOut->setPageTitle( wfMsg( "userlogin" ) );
	$wgOut->setRobotpolicy( "noindex,nofollow" );
	$wgOut->setArticleFlag( false );

	if ( "" == $err ) {
		$wgOut->addHTML( "<h2>$li:</h2>\n" );
	} else {
		$wgOut->addHTML( "<h2>$le:</h2>\n<font size='+1' color='red'>$err</font>\n" );
	}
	if ( 1 == $wgUser->getOption( "rememberPassword" ) ) {
		$checked = " checked";
	} else {
		$checked = "";
	}
	$action = wfLocalLink( "Special:Userlogin" );
	if ( "" != $returnto ) { $action .= "&returnto=$returnto"; }

	$wgOut->addHTML( "
<form method=post action='$action'>
<table border=0><tr>
<td align=right>$yn:</td>
<td colspan=2 align=left>
<input tabindex=1 type=text name='wpName' value='$name' size=20>
</td></tr><tr>
<td align=right>$yp:</td>
<td align=left>
<input tabindex=2 type=password name='wpPassword' value='$pwd' size=20>
</td>
<td align=left>
<input tabindex=3 type=submit name='wpLoginattempt' value='$li'>
</td></tr>
<tr><td colspan=3>&nbsp;</td></tr><tr>
<td align=right>$ypa:</td>
<td align=left>
<input tabindex=4 type=password name='wpRetype' value='$wpRetype' size=20>
</td><td>$nuo</td></tr>
<tr>
<td align=right>$ye:</td>
<td align=left>
<input tabindex=5 type=text name='wpEmail' value='$wpEmail' size=20>
</td><td align=left>
<input tabindex=6 type=submit name='wpCreateaccount' value='$ca'>
</td></tr>
<tr>
<td colspan=3 align=left>
<input tabindex=7 type=checkbox name='wpRemember' value='1'$checked>$rmp
</td></tr>
<tr><td colspan=3>&nbsp;</td></tr><tr>
<td colspan=3 align=left>
<p>$efl<br>
<input tabindex=8 type=submit name='wpMailmypassword' value='$mmp'>
</td></tr></table>
</form>\n" );
}

?>
