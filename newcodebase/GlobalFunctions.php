<?
# Global functions used everywhere

$wgValidSpecialPages = array( "Userlogin", "Userlogout", "Newpages",
	"Recentchanges", "Upload", "Statistics", "Lonelypages",
	"Popularpages", "Wantedpages", "Allpages", "Randompage",
	"Shortpages", "Longpages", "Listusers", "Watchlist",
	"Specialpages", "Preferences", "Deletepage", "Movepage",
	"Protectpage", "Contributions", "Whatlinkshere", "Imagelist",
	"Recentchangeslinked", "Sqldump", "Vote", "Debug" );

$wgSysopSpecialPages = array( "Asksql", "Blockip" );

# All of the text fields from every form, put here so that
# they can be slash-stripped.  Handy for debugging, too.
#
$wgFormTextFields = array(
	# Login form
	"wpName", "wpPassword", "wpName", "wpPassword",
	"wpRetype", "wpEmail",
	# Edit form
	"wpTextbox1", "wpSummary", "wpTextbox2",
	# Upload form
	"wpUploadFile", "wpUploadDescription"
);

$wgNumberOfArticles = -1; # Unset
$wgTotalViews = -1;
$wgTotalEdits = -1;

include_once( "DatabaseFunctions.php" );
include_once( "UpdateClasses.php" );

# PHP 4.1+ has array_key_exists, PHP 4.0.6 has key_exists instead, and earlier
# versions of PHP have neither. So we roll our own. Note that this
# function will return false even for keys that exist but whose associated 
# value is NULL.
#
if ( phpversion() == "4.0.6" ) {
	function array_key_exists( $k, $a ) {
		return key_exists( $k, $a );
	}
} else if (phpversion() < "4.1") {
	function array_key_exists( $k, $a ) {
		return isset($a[$k]);
	}
}

$wgRandomSeeded = false;

function wfSeedRandom()
{
	global $wgRandomSeeded;

	if ( ! $wgRandomSeeded ) {
		mt_srand( (double)microtime() * 1000000 );
		$wgRandomSeeded = true;
	}
}

function wfLocalUrl( $a )
{
	global $wgArticlePath;

	$a = str_replace( " ", "_", $a );
	$a = str_replace( "$1", $a, $wgArticlePath );
	return $a;
}

function wfImageUrl( $img )
{
	global $wgServer, $wgUploadPath;

	$nt = Title::newFromText( $img );
	$name = $nt->getDBkey();

	$url = "{$wgServer}{$wgUploadPath}/" . $name{0} . "/" .
	  substr( $name, 0, 2 ) . "/{$name}";
	return $url;
}

function wfImageArchiveUrl( $name )
{
	global $wgServer, $wgUploadPath;

	$url = "{$wgServer}{$wgUploadPath}/archive/" . $name{15} . "/" .
	  substr( $name, 15, 2 ) . "/{$name}";
	return $url;
}

function wfUrlencode ( $s )
{
	$ulink = urlencode( $s );
	$ulink = str_replace( "%3A", ":", $ulink );
	$ulink = str_replace( "%2F", "/", $ulink );
	return $ulink;
}

function wfDebug( $text, $logonly = false )
{
	global $wgOut, $wgDebugLogFile;

	if ( ! $logonly ) {
		$wgOut->debug( $text );
	}
	if ( "" != $wgDebugLogFile ) {
		error_log( $text, 3, $wgDebugLogFile );
	}
}

function wfMsg( $key )
{
	global $wgLang;
	$ret = $wgLang->getMessage( $key );

	if ( "" == $ret ) {
		die( "FATAL: Couldn't find text for message \"{$key}\".");
	}
	return $ret;
}

function wfStripTextFields()
{
	global $wgFormTextFields, $HTTP_POST_VARS;

	if ( ! get_magic_quotes_gpc() ) {
		return;
	}
	foreach ( $wgFormTextFields as $fname ) {
		if ( isset( $HTTP_POST_VARS[$fname] ) ) {
			$HTTP_POST_VARS[$fname] = stripslashes(
			  $HTTP_POST_VARS[$fname] );
		}
		global ${$fname};
		if ( isset( ${$fname} ) ) {
			${$fname} = stripslashes( $$fname );
		}
	}
}

function wfSpecialPage()
{
	global $wgUser, $wgOut, $wgTitle;
	global $wgValidSpecialPages, $wgSysopSpecialPages;

	$wgOut->setArticleFlag( false );
	$wgOut->setPageTitle( wfMsg( strtolower( $wgTitle->getText() ) ) );

	$t = $wgTitle->getDBkey();
	if ( in_array( $t, $wgValidSpecialPages ) ||
	  ( $wgUser->isSysop() && in_array( $t, $wgSysopSpecialPages ) ) ) {
		$inc = "Special" . $t . ".php";
		include_once( $inc );
		$call = "wfSpecial" . $t;
		$call();
	} else {
		$wgOut->errorpage( "nosuchspecialpage", "nospecialpagetext" );
	}
}

function wfSearch( $s )
{
	global $search;

	$se = new SearchEngine( $search );
	$se->showResults();
}

function wfNumberOfArticles()
{
	global $wgNumberOfArticles;

	wfLoadSiteStats();
	return $wgNumberOfArticles;
}

/* private */ function wfLoadSiteStats()
{
	global $wgNumberOfArticles, $wgTotalViews, $wgTotalEdits;
	if ( -1 != $wgNumberOfArticles ) return;

	$conn = wfGetDB();
	$sql = "SELECT ss_total_views, ss_total_edits, ss_good_articles " .
	  "FROM site_stats WHERE ss_row_id=1";
	$res = wfQuery( $sql, $conn, "wfLoadSiteStats" );
	if ( 0 == mysql_num_rows( $res ) ) { return; }
	else {
		$s = mysql_fetch_object( $res );
		$wgTotalViews = $s->ss_total_views;
		$wgTotalEdits = $s->ss_total_Edits;
		$wgNumberOfArticles = $s->ss_good_articles;
		mysql_free_result( $res );
	}
}

function wfEscapeHTML( $in )
{
	$in = str_replace( "&", "&amp;", $in );
	$in = str_replace( "\"", "&quot;", $in );
	$in = str_replace( ">", "&gt;", $in );
	$in = str_replace( "<", "&lt;", $in );
	return $in;
}

function wfUnescapeHTML( $in )
{
	$in = str_replace( "&lt;", "<", $in );
	$in = str_replace( "&gt;", ">", $in );
	$in = str_replace( "&quot;", "\"", $in );
	$in = str_replace( "&amp;", "&", $in );
	return $in;
}

?>
