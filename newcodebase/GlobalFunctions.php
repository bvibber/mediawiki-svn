<?
# Global functions used everywhere

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
	$a = urlencode( $a );
	$a = str_replace( "$1", $a, $wgArticlePath );
	return $a;
}

function wfImageUrl( $img )
{
	global $wgUploadPath;

	$nt = Title::newFromText( $img );
	$name = $nt->getDBkey();
	$hash = md5( $name );

	$url = "{$wgUploadPath}/" . $hash{0} . "/" .
	  substr( $hash, 0, 2 ) . "/{$name}";
	return $url;
}

function wfImageArchiveUrl( $name )
{
	global $wgUploadPath;

	$hash = md5( substr( $name, 15) );
	$url = "{$wgUploadPath}/archive/" . $hash{0} . "/" .
	  substr( $hash, 0, 2 ) . "/{$name}";
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

function wfReadOnly()
{
	global $wgReadOnlyFile;

	if ( "" == $wgReadOnlyFile ) { return false; }
	return is_file( $wgReadOnlyFile );
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

function wfCleanFormFields( $fields )
{
	global $HTTP_POST_VARS;

	if ( ! get_magic_quotes_gpc() ) {
		return;
	}
	foreach ( $fields as $fname ) {
		if ( isset( $HTTP_POST_VARS[$fname] ) ) {
			$HTTP_POST_VARS[$fname] = stripslashes(
			  $HTTP_POST_VARS[$fname] );
		}
		global ${$fname};
		if ( isset( ${$fname} ) ) {
			${$fname} = stripslashes( ${$fname} );
		}
	}
}

function wfCleanQueryVar( $var )
{
	if ( get_magic_quotes_gpc() ) {
		return stripslashes( $var );
	} else { return $var; }
}

function wfSpecialPage()
{
	global $wgUser, $wgOut, $wgTitle, $wgLang;

	$validSP = $wgLang->getValidSpecialPages();
	$sysopSP = $wgLang->getSysopSpecialPages();
	$devSP = $wgLang->getDeveloperSpecialPages();

	$wgOut->setArticleFlag( false );
	$wgOut->setRobotpolicy( "noindex,follow" );
	$wgOut->setPageTitle( wfMsg( strtolower( $wgTitle->getText() ) ) );

	$t = $wgTitle->getDBkey();
	if ( array_key_exists( $t, $validSP ) ||
	  ( $wgUser->isSysop() && array_key_exists( $t, $sysopSP ) ) ||
	  ( $wgUser->isDeveloper() && array_key_exists( $t, $devSP ) ) ) {
		$inc = "Special" . $t . ".php";
		include_once( $inc );
		$call = "wfSpecial" . $t;
		$call();
	} else if ( array_key_exists( $t, $sysopSP ) ) {
		$wgOut->sysopRequired();
	} else if ( array_key_exists( $t, $devSP ) ) {
		$wgOut->developerRequired();
	} else {
		$wgOut->errorpage( "nosuchspecialpage", "nospecialpagetext" );
	}
}

function wfSearch( $s )
{
	global $search;

	$se = new SearchEngine( wfCleanQueryVar( $search ) );
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

	$sql = "SELECT ss_total_views, ss_total_edits, ss_good_articles " .
	  "FROM site_stats WHERE ss_row_id=1";
	$res = wfQuery( $sql, "wfLoadSiteStats" );

	if ( 0 == wfNumRows( $res ) ) { return; }
	else {
		$s = wfFetchObject( $res );
		$wgTotalViews = $s->ss_total_views;
		$wgTotalEdits = $s->ss_total_Edits;
		$wgNumberOfArticles = $s->ss_good_articles;
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

function wfImageDir( $fname )
{
	global $wgUploadDirectory;

	$hash = md5( $fname );
	$oldumask = umask(0);
	$dest = $wgUploadDirectory . "/" . $hash{0};
	if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
	$dest .= "/" . substr( $hash, 0, 2 );
	if ( ! is_dir( $dest ) ) { mkdir( $dest, 0777 ); }
	
	umask( $oldumask );
	return $dest;
}

function wfImageArchiveDir( $fname )
{
	global $wgUploadDirectory;

	$hash = md5( $fname );
	$oldumask = umask(0);
	$archive = "{$wgUploadDirectory}/archive";
	if ( ! is_dir( $archive ) ) { mkdir( $archive, 0777 ); }
	$archive .= "/" . $hash{0};
	if ( ! is_dir( $archive ) ) { mkdir( $archive, 0777 ); }
	$archive .= "/" . substr( $hash, 0, 2 );
	if ( ! is_dir( $archive ) ) { mkdir( $archive, 0777 ); }

	umask( $oldumask );
	return $archive;
}

function wfRecordUpload( $name, $oldver, $size, $desc )
{
	global $wgUser, $wgLang, $wgTitle, $wgOut;
	$fname = "wfRecordUpload";

	$sql = "SELECT img_name,img_size,img_timestamp,img_description,img_user," .
	  "img_user_text FROM image WHERE img_name='{$name}'";
	$res = wfQuery( $sql, $fname );

	if ( 0 == wfNumRows( $res ) ) {
		$sql = "INSERT INTO image (img_name,img_size,img_timestamp," .
		  "img_description,img_user,img_user_text) VALUES ('" .
		  wfStrencode( $name ) . "',{$size},'" . date( "YmdHis" ) . "','" .
		  wfStrencode( $description ) . "', '" . $wgUser->getID() .
		  "', '" . wfStrencode( $wgUser->getName() ) . "')";
		wfQuery( $sql, $fname );
	} else {
		$s = wfFetchObject( $res );

		$sql = "INSERT INTO oldimage (oi_name,oi_archive_name,oi_size," .
		  "oi_timestamp,oi_description,oi_user,oi_user_text) VALUES ('" .
		  wfStrencode( $s->img_name ) . "','" .
		  wfStrencode( $oldver ) .
		  "',{$s->img_size},'{$s->img_timestamp}','" .
		  wfStrencode( $s->img_description ) . "','" .
		  wfStrencode( $s->img_user ) . "','" .
		  wfStrencode( $s->img_user_text) . "')";
		wfQuery( $sql, $fname );

		$sql = "UPDATE image SET img_size={$size}," .
		  "img_timestamp='" . date( "YmdHis" ) . "',img_user='" .
		  $wgUser->getID() . "',img_user_text='" .
		  wfStrencode( $wgUser->getName() ) . "', img_description='" .
		  wfStrencode( $desc ) . "' WHERE img_name='" .
		  wfStrencode( $name ) . "'";
		wfQuery( $sql, $fname );
	}
	$logpage = wfStrencode( wfMsg( "uploadlogpage" ) );
	$sql = "SELECT cur_id,cur_text FROM cur WHERE cur_namespace=" .
	  Namespace::getIndex( "Wikipedia" ) . " AND cur_title='{$logpage}'";
	$res = wfQuery( $sql, $fname );

	if ( 0 == wfNumRows( $res ) ) {
		# TODO: Error: need Upload log article
	}
	$s = wfFetchObject( $res );
	$text = $s->cur_text;
	$id = $s->cur_id;

	$uid = $wgUser->getID();
	$ut = $wgUser->getName();
	if ( 0 == $uid ) { $ul = $ut; }
	else { $ul = "[[User:{$ut}|{$ut}]]"; }

	$d = $wgLang->timeanddate( date( "YmdHis" ) );

	preg_match( "/^(.*?)<ul>(.*)$/sD", $text, $m );	
	$da = str_replace( "$1", $name, wfMsg( "uploadedimage" ) );

	$text = "{$m[1]}<ul><li>{$d} {$ul} {$da}</li>\n{$m[2]}";

	$sql = "UPDATE cur SET cur_timestamp='" . date( "YmdHis" ) .
	  "', cur_user={$uid}, cur_user_text='" .wfStrencode( $ut ) .
	  "', cur_text='" . wfStrencode( trim( $text ) ) . "' " .
	  "WHERE cur_id={$id}";
	wfQuery( $sql, $fname );
}

?>
