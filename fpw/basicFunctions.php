<?
/* Output error message. Rarely used.
 */
function error ( $error )
{
	global $wikiErrorPageTitle , $wikiErrorMessage ;
	$page = new WikiPage ;
	$page->special ( $wikiErrorPageTitle ) ;
	$page->contents = str_replace ( "$1" , "$error" , $wikiErrorMessage ) ;
	return $page->renderPage () ;
}

/* Protect slashes and colons in URLs
 */
function nurlencode ( $s )
{
	$ulink = urlencode ( $s ) ;
	$ulink = str_replace ( "%3A" , ":" , $ulink ) ;
	$ulink = str_replace ( "%2F" , "/" , $ulink ) ;
	return $ulink ;
}

/* Convert MySQL timestame to date
 */
function tsc ( $t )
{
	$year = substr ( $t , 0 , 4 ) ;
	$month = substr ( $t , 4 , 2 ) ;
	$day = substr ( $t , 6 , 2 ) ;
	$hour = substr ( $t , 8 , 2 ) ;
	$min = substr ( $t , 10 , 2 ) ;
	$sec = substr ( $t , 12 , 2 ) ;
	return mktime ( $hour , $min , $sec , $month , $day , $year ) ;
}

/* We unfortunately can't trust the locale functions for now,
 * so we'll roll our own
 */
function ucfirstIntl ( $str )
{
	global $wikiUpperChars , $wikiLowerChars ;
	
	if ( $str == "" ) return $str ;
	
	if ( is_array ( $wikiUpperChars ) ) {
		# Multi-byte charsets or multi-character letters to be capitalised
        # (eg Dutch ij->IJ)
		# FIXME: For now, assuming UTF-8
		return preg_replace ( "/^([\\x00-\\x7f]|[\\xc0-\\xff][\\x80-\\xbf]*)/e", "strtr ( \"\$1\" , \$wikiUpperChars )" , $str ) ;
	}
	# Simple single-byte charsets
	return strtr ( substr ( $str , 0 , 1 ) , $wikiLowerChars , $wikiUpperChars ) . substr ( $str , 1 );
}

function strtoupperIntl ( $str )
{
	global $wikiUpperChars , $wikiLowerChars ;
	
	if ( is_array ( $wikiUpperChars ) ) {
		return strtr ( $str, $wikiUpperChars ) ;
    } else {
		return strtr ( $str , $wikiLowerChars , $wikiUpperChars );
	}
}

function strtolowerIntl ( $str )
{
	global $wikiUpperChars , $wikiLowerChars ;
	
	if ( is_array ( $wikiLowerChars ) ) {
		return strtr ( $str, $wikiLowerChars ) ;
    } else {
		return strtr ( $str , $wikiUpperChars , $wikiLowerChars );
	}
}

function isBlockedIP ()
{
	global $wikiBlockedIPsLink ;
	$ip = getenv ( REMOTE_ADDR ) ;
	$list = getMySQL ( "cur" , "cur_text" , "cur_title=\"$wikiBlockedIPsLink\"" ) ;
    if ( preg_match( "/^\*\s?$ip \(/", $list ) ) return true;
    else return false ;
}

function view ( $title )
{
	global $FromEditForm , $action , $namespaceBackground , $wikiNamespaceBackground ;
	global $redirect ;
	global $vpage , $wikiDescribePage ;
	if ( $FromEditForm ) {
		include_once ( "basic_edit.php" ) ;
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

	return $vpage->renderPage () ;
}

function doPrint ( $title )
{
	global $vpage ;

	$vpage = new WikiPage ;
	$vpage->load ( $title ) ;
	return $vpage->renderPage ( true ) ;
}

# Checking for talk subpage
function fixTalk ( $title )
{
	global $wikiTalk ;

	$sp = explode ( "/" , $title ) ;
	$ns = explode ( ":" , $title ) ;
	$lsp = array_pop ( $sp ) ;
	if ( strtolowerIntl ( $lsp ) == $wikiTalk and count ( $sp ) > 0 ) {
		if ( count ( $ns ) == 1 or strtolowerIntl ( $ns[0] ) == $wikiTalk )
			$title = "$wikiTalk:".implode ( "/" , $sp ) ;
	}
	return $title ;
}

# EXPERIMENTAL!
function framepage ()
{
	global $title ;

	$p = "wiki.phtml?" ;
	$v = get_defined_vars() ;
	$vk = array_keys ( $v ) ;
	foreach ( $vk as $x ) {
		$p .= "&$x=".$v[$x] ;
	}
	$ret = "" ;
	$ret .= "<html><head></head><body><FRAMESET rows=\"150,*\"><FRAME src=\"$p&framed=top\">" ;
	$ret .= "<FRAMESET cols=\"*,140\"><FRAME src=\"$p&framed=main\"><FRAME src=\"$p&framed=bar\">" ;
	$ret .= "</FRAMESET></FRAMESET></body></html>" ;

	return $ret ;
}

function getmicrotime(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

function loadSkin () {
	global $user , $title , $doSkin ;
	include_once ( "wikiSkins.php" ) ; # Dummy class only
	if ( $user->isLoggedIn ) {
		if ( strtolower ( $title ) == "special:editusersettings" AND $doSkin != "" ) { # Chose new skin!
			$t = str_replace ( " " , "" , $doSkin ) ;
		} else $t = str_replace ( " " , "" , $user->options["skin"] ) ;
		if ( strtolower ( $t ) == "none" OR $t == "" ) $t = "Standard" ;
		include_once ( "wikiSkin$t.php" ) ;
	} else {
		include_once ( "wikiSkinStandard.php" ) ;
		}
	}

?>
