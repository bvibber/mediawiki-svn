<?
function userLogout () {
	global $user , $vpage , $WikiLoggedIn , $wikiGoodbye ;
	$vpage->title = "User logout" ;
	setcookie ( "WikiLoggedIn" , "" , time()-3600 ) ;
	if ( $user->options["rememberPassword"] != "on" ) setcookie ( "WikiUserPassword" , "" , time()-3600 ) ;
	$user->isLoggedIn = false ;
	$WikiLoggedIn = false ;
	return str_replace ( "$1" , $user->name , $wikiGoodbye ) ;
	}
?>