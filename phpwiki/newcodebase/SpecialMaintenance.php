<?

function sns()
	{
	global $wgLang ;
	$ns = $wgLang->getNamespaces() ;
	return $ns[-1] ;
	}

function wfSpecialMaintenance ()
	{
	global $wgUser, $wgOut, $wgLang, $wgTitle, $subfunction, $wgLanguageCode, $submitmll;
	if ( $subfunction == "disambiguations" ) return wfSpecialDisambiguations() ;
	if ( $subfunction == "doubleredirects" ) return wfSpecialDoubleRedirects() ;
	if ( $subfunction == "selflinks" ) return wfSpecialSelfLinks() ;
	if ( $subfunction == "missinglanguagelinks" ) return wfSpecialMissingLanguageLinks() ;
	if ( isset ( $submitmll ) ) return wfSpecialMissingLanguageLinks() ;

	$sk = $wgUser->getSkin();
	$ns = $wgLang->getNamespaces() ;
	$r = wfMsg("maintnancepagetext") ;
	$r .= "<UL>\n" ;
	$r .= "<li>".getMPL("disambiguations")."</li>\n" ;
	$r .= "<li>".getMPL("doubleredirects")."</li>\n" ;
	$r .= "<li>".getMPL("selflinks")."</li>\n" ;

	$r .= "<li>";
	$l = getMPL("missinglanguagelinks");
	$l = str_replace ( "</a>" , "" , $l ) ;
	$l = str_replace ( "<a " , "<FORM method=post " , $l ) ;
	$l = explode ( ">" , $l ) ;
	$l = $l[0] ;
	$r .= $l.">\n" ;
	$r .= "<input type=submit name='submitmll' value='" ;
	$r .= wfMsg("missinglanguagelinksbutton");
	$r .= "'>\n" ;
	$r .= "<select name=thelang>\n" ;
	$a = $wgLang->getLanguageNames();
	$ak = array_keys ( $a ) ;
	foreach ( $ak AS $k ) {
		if ( $k != $wgLanguageCode )
			$r .= "<option value='{$k}'>{$a[$k]}</option>\n" ;
		}
	$r .= "</select>\n" ;
	$r .= "</FORM>\n</li>" ;

	$r .= "</UL>\n" ;
	$wgOut->addHTML ( $r ) ;
	}

function getMPL ( $x )
	{
	global $wgUser , $wgLang;
	$sk = $wgUser->getSkin() ;
	return $sk->makeKnownLink(sns().":Maintenance",wfMsg($x),"subfunction={$x}") ;
	}

function getMaintenancePageBacklink()
	{
	global $wgUser , $wgLang , $subfunction ;
	$sk = $wgUser->getSkin() ;
	$ns = $wgLang->getNamespaces() ;
	$r = $sk->makeKnownLink (
		$ns[-1].":Maintenance",
		wfMsg("maintenancebacklink") ) ;
	$t = wfMsg ( $subfunction ) ;
	
	$s = "<table width=100% border=0><tr><td>";
	$s .= "<h2>{$t}</h2></td><td align=right>";
	$s .= "{$r}</td></tr></table>\n" ;
	return $s ;
	}


function wfSpecialDisambiguations()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $limit, $offset; # From query string
	$fname = "wfSpecialDisambiguations";

	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 50; }
	}
	if ( ! $offset ) { $offset = 0; }

	$dp = wfMsg("disambiguationspage");

	$sql = "SELECT la.l_from,la.l_to,"
		. " lb.l_from AS source,lb.l_to AS dest,"
		. " c.cur_id, c.cur_title AS dt"
		. " FROM links AS la, links AS lb, cur AS c, cur AS d"
		. " WHERE la.l_from=\"{$dp}\""
		. " AND la.l_to=lb.l_to"
		. " AND la.l_from<>lb.l_from"
		. " AND c.cur_id=lb.l_to"
		. " AND c.cur_namespace=0"
		. " AND d.cur_title=lb.l_from"
		. " AND d.cur_namespace=0"
		. " LIMIT {$offset}, {$limit}";

	$res = wfQuery( $sql, $fname );

	$sk = $wgUser->getSkin();

	$top = "<p>".wfMsg("disambiguationstext")."</p><br>\n";
	$top = str_replace ( "$1" , $sk->makeKnownLink ( $dp ) , $top ) ;
	$top = getMaintenancePageBacklink().$top ;
	$top .= SearchEngine::showingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = SearchEngine::viewPrevNext( $offset, $limit, "REPLACETHIS" ) ;
	$sl = str_replace ( "REPLACETHIS" , sns().":Maintenance&subfunction=disambiguations" , $sl ) ;
	$wgOut->addHTML( "<br>{$sl}\n" );

	$s = "<ol start=" . ( $offset + 1 ) . ">";
	while ( $obj = wfFetchObject( $res ) ) {
		$l1 = $sk->makeKnownLink ( $obj->source , "" , "redirect=no" ) ;
		$l2 = $sk->makeKnownLink ( $obj->dt ) ;
		$l3 = $sk->makeBrokenLink ( $obj->source , "(".wfMsg("qbedit").")" , "redirect=no" ) ;
		$s .= "<li>{$l1} {$l3} => {$l2}</li>\n" ;
	}
	wfFreeResult( $res );
	$s .= "</ol>";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}\n" );
}

function wfSpecialDoubleRedirects()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $limit, $offset; # From query string
	$fname = "wfSpecialDoubleRedirects";

	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 50; }
	}
	if ( ! $offset ) { $offset = 0; }

	$sql = "SELECT l_from,l_to,cb.cur_text AS rt,cb.cur_title AS ti FROM links,cur AS ca, cur AS cb WHERE ca.cur_is_redirect=1 AND cb.cur_is_redirect=1 AND l_to=cb.cur_id AND l_from=ca.cur_title LIMIT {$offset}, {$limit}" ;

	$res = wfQuery( $sql, $fname );

	$top = getMaintenancePageBacklink();
	$top .= "<p>".wfMsg("doubleredirectstext")."</p><br>\n";
	$top .= SearchEngine::showingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = SearchEngine::viewPrevNext( $offset, $limit, "REPLACETHIS" ) ;
	$sl = str_replace ( "REPLACETHIS" , sns().":Maintenance&subfunction=doubleredirects" , $sl ) ;
	$wgOut->addHTML( "<br>{$sl}\n" );

	$sk = $wgUser->getSkin();
	$s = "<ol start=" . ( $offset + 1 ) . ">";
	while ( $obj = wfFetchObject( $res ) ) {
		$n = explode ( "\n" , $obj->rt ) ;
		$n = $n[0] ;
		$l1 = $sk->makeKnownLink ( $obj->l_from , "" , "redirect=no" ) ;
		$l2 = $sk->makeKnownLink ( $obj->ti , "" , "redirect=no" ) ;
		$s .= "<li>{$l1} => {$l2} (\"{$n}\")</li>\n" ;
	}
	wfFreeResult( $res );
	$s .= "</ol>";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}\n" );
}

function wfSpecialSelfLinks()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	global $limit, $offset; # From query string
	$fname = "wfSpecialSelfLinks";

	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 50; }
	}
	if ( ! $offset ) { $offset = 0; }

	$sql = "SELECT cur_title FROM cur,links WHERE cur_is_redirect=0 AND cur_namespace=0 AND l_from=cur_title AND l_to=cur_id LIMIT {$offset}, {$limit}";

	$res = wfQuery( $sql, $fname );

	$top = getMaintenancePageBacklink();
	$top .= "<p>".wfMsg("selflinkstext")."</p><br>\n";
	$top .= SearchEngine::showingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = SearchEngine::viewPrevNext( $offset, $limit, "REPLACETHIS" ) ;
	$sl = str_replace ( "REPLACETHIS" , sns().":Maintenance&subfunction=selflinks" , $sl ) ;
	$wgOut->addHTML( "<br>{$sl}\n" );

	$sk = $wgUser->getSkin();
	$s = "<ol start=" . ( $offset + 1 ) . ">";
	while ( $obj = wfFetchObject( $res ) )
		$s .= "<li>".$sk->makeKnownLink ( $obj->cur_title )."</li>\n" ;
	wfFreeResult( $res );
	$s .= "</ol>";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}\n" );
}

function wfSpecialMissingLanguageLinks()
{
	global $wgUser, $wgOut, $wgLang, $wgTitle, $thelang, $subfunction;
	global $limit, $offset; # From query string
	$fname = "wfSpecialMissingLanguageLinks";
	$subfunction = "missinglanguagelinks" ;
	if ( $thelang == "w" ) $thelang = "en" ; # Fix for international wikis

	if ( ! $limit ) {
		$limit = $wgUser->getOption( "rclimit" );
		if ( ! $limit ) { $limit = 50; }
	}
	if ( ! $offset ) { $offset = 0; }

	$sql = "SELECT cur_title FROM cur WHERE cur_namespace=0 AND cur_is_redirect=0 AND cur_title NOT LIKE '%/%' AND cur_text NOT LIKE '%[[{$thelang}:%' LIMIT {$offset}, {$limit}";

	$res = wfQuery( $sql, $fname );


	$mll = wfMsg("missinglanguagelinkstext");
	$mll = str_replace ( "$1" , $wgLang->getLanguageName($thelang) , $mll ) ;

	$top = getMaintenancePageBacklink();
	$top .= "<p>$mll</p><br>";
	$top .= SearchEngine::showingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = SearchEngine::viewPrevNext( $offset, $limit, "REPLACETHIS" ) ;
	$sl = str_replace ( "REPLACETHIS" , sns().":Maintenance&subfunction=missinglanguagelinks&thelang={$thelang}" , $sl ) ;
	$wgOut->addHTML( "<br>{$sl}\n" );

	$sk = $wgUser->getSkin();
	$s = "<ol start=" . ( $offset + 1 ) . ">";
	while ( $obj = wfFetchObject( $res ) )
		$s .= "<li>".$sk->makeKnownLink ( $obj->cur_title )."</li>\n" ;
	wfFreeResult( $res );
	$s .= "</ol>";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}\n" );
}

?>
