<?
# The main wiki script and things like database
# conversion and maintenance scripts all share a
# common setup of including lots of classes and
# setting up a few globals
#

include_once( "LocalSettings.php" );
include_once( "GlobalFunctions.php" );
include_once( "Language.php" );
include_once( "Namespace.php" );
include_once( "Skin.php" );
include_once( "OutputPage.php" );
include_once( "DifferenceEngine.php" );
include_once( "SearchEngine.php" );
include_once( "User.php" );
include_once( "LinkCache.php" );
include_once( "Title.php" );
include_once( "Article.php" );

global $wgUser, $wgLang, $wgOut, $wgTitle;
global $wgArticle, $wgDeferredUpdateList, $wgLinkCache;

$wgOut = new OutputPage();
$wgLangClass = "Language" . ucfirst( $wgLanguageCode );
$wgLang = new $wgLangClass();

$wgUser = new User();
$wgUser->loadFromSession();
$wgDeferredUpdateList = array();
$wgLinkCache = new LinkCache();

?>
