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
	global $wgUser, $wpPassword2, $wpRetype, $wpName2, $wpRemember2;
	global $wpEmail, $wgDeferredUpdateList;

	if ( 0 != strcmp( $wpPassword2, $wpRetype ) ) {
		mainLoginForm( wfMsg( "badretype" ) );
		return;
	}
	if ( "" == $wpName2 ) {
		mainLoginForm( wfMsg( "noname" ) );
		return;
	}
	$u = User::newFromName( $wpName2 );

	if ( 0 != $u->idForName() ) {
		mainLoginForm( wfMsg( "userexists" ) );
		return;
	}
	$u->addToDatabase();

	$u->setPassword( $wpPassword2 );
	if ( 1 == $wpRemember2 ) {
		$u->setOption( "rememberPassword", 1 );
	}
	$u->setEmail( $wpEmail );

	$wgUser = $u;
	$wgUser->setCookies();
	$up = new UserUpdate();
	array_push( $wgDeferredUpdateList, $up );

	$m = str_replace( "$1", $wgUser->getName(), wfMsg( "welcomecreation" ) );
	successfulLogin( $m );
}

/* private */ function processLogin()
{
	global $wgUser, $wpName1, $wpPassword1, $wpRemember1;

	if ( "" == $wpName1 ) {
		mainLoginForm( wfMsg( "noname" ) );
		return;
	}
	$u = User::newFromName( $wpName1 );
	$id = $u->idForName();
	if ( 0 == $id ) {
		$m = str_replace( "$1", $u->getName(), wfMsg( "nosuchuser" ) );
		mainLoginForm( $m );
		return;
	}
	$u->setId( $id );
	$u->loadFromDatabase();
	if ( 0 != strcmp( $u->getPassword(), $wpPassword1 ) ) {
		mainLoginForm( wfMsg( "wrongpassword" ) );
		return;
	}
	# We've verified now, update the real record
	#
	$wgUser = $u;
	if ( 1 == $wpRemember1 ) {
		$wgUser->setOption( "rememberPassword", 1 );
	}
	$wgUser->setCookies();
	$m = str_replace( "$1", $wgUser->getName(), wfMsg( "loginsuccess" ) );
	successfulLogin( $m );
}

/* private */ function mailPassword()
{
	global $wpName1;

	if ( "" == $wpName1 ) {
		mainLoginForm( wfMsg( "noname" ) );
		return;
	}
	$u = User::newFromName( $wpName1 );
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
	global $wgOut;

	$wgOut->setPageTitle( wfMsg( "loginsuccesstitle" ) );
	$wgOut->setRobotpolicy( "noindex,nofollow" );
	$wgOut->addHTML( $msg . "\n<p>" );
	$wgOut->addWikiText( wfMsg( "returntomain" ) );
}

/* private */ function mainLoginForm( $err )
{
	global $wgUser, $wgOut;
	global $wpName1, $wpPassword1, $wpRemember1;
	global $wpName2, $wpPassword2, $wpRetype, $wpRemember2;
	global $wpEmail;
	global $HTTP_COOKIE_VARS;

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

	$name = $wpName1;
	if ( "" == $name ) {
		if ( 0 != $wgUser->getID() ) {
			$name = $wgUser->getName();
		} else {
			$name = $HTTP_COOKIE_VARS["wcUserName"];
		}
	}
	$pwd = $wpPassword1;
	if ( "" == $pwd ) {
		if ( 0 != $wgUser->getID() ) {
			$pwd = $wgUser->getPassword();
		} else {
			$pwd = $HTTP_COOKIE_VARS["wcUserPassword"];
		}
	}
	$wgOut->setPageTitle( wfMsg( "userlogin" ) );
	$wgOut->setRobotpolicy( "noindex,nofollow" );
	if ( "" == $err ) {
		$wgOut->addHTML( "<h2>$li</h2>\n" );
	} else {
		$wgOut->addHTML( "<h2>$le</h2>\n<font size='+1' color='red'>$err</font>\n<p>" );
	}
	if ( 1 == $wgUser->getOption( "rememberPassword" ) ) {
		$checked = " checked";
	} else {
		$checked = "";
	}
	$action = wfLocalLink( "Special:Userlogin" );

	$wgOut->addHTML( "
<form action='$action' method=post><tt>
$yn<input tabindex=1 type=text name='wpName1' value='$name' size=20><br>
$yp<input tabindex=2 type=password name='wpPassword1' value='$pwd' size=20><br>
<input tabindex=4 type=checkbox name='wpRemember1' value='1'$checked>$rmp<br>
<input tabindex=5 type=submit name='wpLoginattempt' value='$li'>
<input tabindex=6 type=submit name='wpMailmypassword' value='$mmp'>
</tt></form>\n" );

	if ( isset( $wpRemember2 ) ) { $checked = " checked"; }
	else { $checked = ""; } 

	$wgOut->addHTML( "<hr>
<h2>$ca</h2><p>$ayn<br>
<form action='$action' method=post><tt>
$yn<input tabindex=6 type=text name='wpName2' value='$wpName2' size=20><br>
$yp<input tabindex=7 type=password name='wpPassword2' value='$wpPassword2' size=20><br>
$ypa<input tabindex=8 type=password name='wpRetype' value='$wpRetype' size=20>$nuo<br>
$ye<input tabindex=9 type=text name='wpEmail' value='$wpEmail' size=20>$efl<br>
<input tabindex=10 type=checkbox name='wpRemember2' value='1'$checked>$rmp<br>
<input tabindex=11 type=submit name='wpCreateaccount' value='$ca'>
</tt></form>\n" );
}

?>
