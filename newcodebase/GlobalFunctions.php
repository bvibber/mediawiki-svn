<?
# Global functions used everywhere

$wgValidSpecialPages = array( "Userlogin", "Userlogout", "Newpages",
	"Recentchanges", "Upload", "Statistics", "Lonelypages",
	"Popularpages", "Wantedpages", "Allpages", "Randompage",
	"Shortpages", "Longpages", "Listusers", "Watchlist",
	"Specialpages", "Preferences", "Deletepage", "Movepage",
	"Protectpage", "Contributions", "Whatlinkshere",
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
	"wpTextbox1", "wpSummary", "wpTextbox2"
);

$wgNumberOfArticles = -1; # Unset
$wgTotalViews = -1;
$wgTotalEdits = -1;

include_once( "DatabaseFunctions.php" );
include_once( "UpdateClasses.php" );

# PHP 4.06 workaround
#
if ( phpversion() < "4.1" ) {
	function array_key_exists( $k, $a ) {
		return key_exists( $k, $a );
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

function wfLocalLink( $a )
{
	global $wgArticlePath;

	$a = str_replace( " ", "_", $a );
	$a = str_replace( "$1", $a, $wgArticlePath );
	return $a;
}

function wfUrlencode ( $s )
{
	$ulink = urlencode( $s );
	$ulink = str_replace( "%3A", ":", $ulink );
	$ulink = str_replace( "%2F", "/", $ulink );
	return $ulink;
}

function wfDebug( $text )
{
	global $wgOut, $wgDebugLogFile;
	$wgOut->debug( $text );

	if ( "" != $wgDebugLogFile ) {
		error_log( $text, 3, $wgDebugLogFile );
	}
}

function wfMsg( $key )
{
	global $wgLang;
	return $wgLang->getMessage( $key );
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
	global $wgOut;

	$wgOut->setPageTitle( "Search" );
	$wgOut->addHTML( "<p>(TODO: Search for \"$s\")" );
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
	wfDebug( "Glob: 1: $sql\n" );

	$res = mysql_query( $sql, $conn );
	if ( ( false === $res ) || ( 0 == mysql_num_rows( $res) ) ) { return; }
	else {
		$s = mysql_fetch_object( $res );
		$wgTotalViews = $s->ss_total_views;
		$wgTotalEdits = $s->ss_total_Edits;
		$wgNumberOfArticles = $s->ss_good_articles;
		mysql_free_result( $res );
	}
}

?>
