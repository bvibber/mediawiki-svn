<?
function userLogin () {
	global $WikiUserPassword , $WikiLoggedIn ;
	global $loginattempt , $user , $vpage , $WikiUserID , $expiration , $wikiLogIn ;
	global $wikiYourName , $wikiYourPassword , $wikiYourPasswordAgain , $wikiNewUsersOnly , $wikiRememberMyPassword , $wikiLoginProblem , $wikiLoginPageTitle ;
	$vpage->title = $wikiLoginPageTitle ;

	if ( isset ( $loginattempt ) ) {
		unset ( $loginattempt ) ;
		global $USERNAME , $USERPASSWORD , $RETYPE , $REMEMBERPASSWORD ;

		# Language recode
		$USERNAME = wikiRecodeInput ( $USERNAME ) ;
		$USERPASSWORD = wikiRecodeInput ( $USERPASSWORD ) ;
		$RETYPE = wikiRecodeInput ( $RETYPE ) ;

		if ( $REMEMBERPASSWORD == "" ) $REMEMBERPASSWORD = "off" ;
		$nu = new WikiUser ;
		$nu->name = $USERNAME ;
		$nu->password = $USERPASSWORD ;
		$nu->options["rememberPassword"] = $REMEMBERPASSWORD ;
		$nu->retypePassword = $RETYPE ;

		$s = "<h1>".$nu->verify()."</h1>" ;
		if ( $nu->isLoggedIn ) {
			$user = new WikiUser ;
			$user = $nu ;
			setcookie ( "WikiUserID" , $user->id , $expiration ) ;
			setcookie ( "WikiLoggedIn" , "yes" , $expiration ) ;
			if ( $user->options["rememberPassword"] == "on" ) setcookie ( "WikiUserPassword" , $user->password , $expiration ) ;
			$user->options["rememberPassword"] = $REMEMBERPASSWORD ;
			$user->saveSettings() ;
		} else if ( $USERPASSWORD == $RETYPE and !($nu->doesUserExist()) ) {
			$user = new wikiUser ;
			$nu->name = ucfirst ( $nu->name ) ;
			$nu->addToDatabase () ;
			$user = $nu ;
			global $wikiWelcomeCreation ;
			$s = str_replace ( "$1" , $user->name , $wikiWelcomeCreation ) ;
			setcookie ( "WikiLoggedIn" , "yes" , $expiration ) ;
			setcookie ( "WikiUserID" , $user->id , $expiration ) ;
			if ( $user->options["rememberPassword"] == "on" ) setcookie ( "WikiUserPassword" , $user->password , $expiration ) ;
			$user->options["rememberPassword"] = $REMEMBERPASSWORD ;
			$user->saveSettings() ;

		if ( $user->options["rememberPassword"] == "on" ) $check = "checked" ;
	  	$s .= "<FORM action=\"".wikiLink("special:userLogin")."\" method=post><font face=courier>\n" ;
	  	$s .= "$wikiYourName<INPUT TABINDEX=1 TYPE=text NAME=USERNAME VALUE=\"$user->name\" SIZE=20><br>\n" ;
	  	$s .= "$wikiYourPassword<INPUT TABINDEX=2 TYPE=password NAME=USERPASSWORD VALUE=\"$user->password\" SIZE=20><br>\n" ;
  		$s .= "<INPUT TABINDEX=4 TYPE=checkbox NAME=REMEMBERPASSWORD $check>$wikiRememberMyPassword<br>\n" ;
	  	$s .= "<input TABINDEX=5 type=submit name=loginattempt value=\"$wikiLogIn\">\n" ;
  		$s .= "</font></FORM>\n" ;

		} else {
			$s .= $wikiLoginProblem ;
			}
	} else {
		global $wikiAlreadyLoggedIn , $wikiPleaseLogIn , $wikiAreYouNew ;
		$s = "" ;
		if ( $user->isLoggedIn ) $s .= str_replace ( "$" , $user->name , $wikiAlreadyLoggedIn ) ;
	  	$s .= $wikiPleaseLogIn ;
		global $WikiUserID , $WikiUserPassword , $WikiLoggedIn ;
		if ( $WikiUserID != "" ) {
			$user->name = getMySQL ( "user" , "user_name" , "user_id=$WikiUserID" ) ;
			$u = new WikiUser ;
			$u->id = $WikiUserID ;
			$u->isLoggedIn = true ;
			$u->loadSettings() ;
			$user->options["rememberPassword"] = $u->options["rememberPassword"] ;
			if ( $user->options["rememberPassword"] == "on" )
				$user->password = $WikiUserPassword ;
			}
		if ( $user->options["rememberPassword"] == "on" ) $check = "checked" ;
		$s .= $wikiAreYouNew ;
	  	$s .= "<FORM action=\"".wikiLink("special:userLogin")."\" method=post><font face=courier>\n" ;
	  	$s .= "$wikiYourName<INPUT TABINDEX=1 TYPE=text NAME=USERNAME VALUE=\"$user->name\" SIZE=20><br>\n" ;
	  	$s .= "$wikiYourPassword<INPUT TABINDEX=2 TYPE=password NAME=USERPASSWORD VALUE=\"$user->password\" SIZE=20><br>\n" ;
	  	$s .= "$wikiYourPasswordAgain<INPUT TABINDEX=2 TYPE=password NAME=RETYPE VALUE=\"\" SIZE=20>$wikiNewUsersOnly<br>\n" ;
  		$s .= "<INPUT TABINDEX=4 TYPE=checkbox NAME=REMEMBERPASSWORD $check>$wikiRememberMyPassword<br>\n" ;
	  	$s .= "<input TABINDEX=5 type=submit name=loginattempt value=\"$wikiLogIn\">\n" ;
  		$s .= "</font></FORM>\n" ;
		}

	return $s ;
	}

?>