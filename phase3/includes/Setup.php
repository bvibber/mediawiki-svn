<?
# The main wiki script and things like database
# conversion and maintenance scripts all share a
# common setup of including lots of classes and
# setting up a few globals.
#

global $IP;
include_once( "$IP/GlobalFunctions.php" );
include_once( "$IP/Namespace.php" );
include_once( "$IP/Skin.php" );
include_once( "$IP/OutputPage.php" );
include_once( "$IP/DifferenceEngine.php" );
include_once( "$IP/SearchEngine.php" );
include_once( "$IP/User.php" );
include_once( "$IP/LinkCache.php" );
include_once( "$IP/Title.php" );
include_once( "$IP/Article.php" );
include_once( "$IP/MemCachedClient.inc.php" );
include_once( "$IP/Block.php" );

global $wgUser, $wgLang, $wgOut, $wgTitle;
global $wgArticle, $wgDeferredUpdateList, $wgLinkCache;
global $wgMemc, $wgUseMemCached, $wgMemCachedDebug;

class MemCachedClientforWiki extends MemCachedClient {
	function _debug( $text ) {
		wfDebug( "memcached: $text\n" );
	}
}

$wgMemc = new MemCachedClientforWiki();
if( $wgUseMemCached ) {
	$wgMemc->set_servers( $wgMemCachedServers );
	$wgMemc->set_debug( $wgMemCachedDebug );
}

include_once( "$IP/Language.php" );

$wgOut = new OutputPage();
$wgLangClass = "Language" . ucfirst( $wgLanguageCode );
if( ! class_exists( $wgLangClass ) ) {
	include_once( "$IP/LanguageUtf8.php" );
	$wgLangClass = "LanguageUtf8";
}
$wgLang = new $wgLangClass();

if( !$wgCommandLineMode ) {
	if( $wgSessionsInMemcached ) {
		include_once( "$IP/MemcachedSessions.php" );
	}
	session_set_cookie_params( 0, $wgCookiePath, $wgCookieDomain );
	session_cache_limiter( "private, must-revalidate" );
	session_start();
	session_register( "wsUserID" );
	session_register( "wsUserName" );
	session_register( "wsUserPassword" );
	session_register( "wsUploadFiles" );
}

$wgUser = User::loadFromSession();
$wgDeferredUpdateList = array();
$wgLinkCache = new LinkCache();

?>
