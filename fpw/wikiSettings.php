<?
# ATTENTION:
# To fit your local settings, PLEASE edit wikiLocalSettings.php ONLY!
# Change settings here ONLY if they're to become global in all wikipedias!
#
# This file loads up the default English message strings
# and the default server configuration for the English wikipedia.

include_once ( "wikiTextEn.php" ) ;

# Essential stuff, so the script knows where it is, whar database to use, etc.
#$wikiCurrentServer = "http://127.0.0.1" ;
$wikiCurrentServer = "http://" . getenv("SERVER_NAME");
$wikiSQLServer = "wiki" ; # The name of the database, actually...
$wikiArticleSource = "$wikiCurrentServer/wiki/$1" ;
$wikiLogoFile = "/wiki.png" ;
$wikiStarTrekImage = "/startrek.png" ;
$THESCRIPT = "/wiki.phtml" ; # The name and location of the script. The $PHP_SELF variable doesn't work with Netscape

# For the MySQL database
$wikiThisDBserver = "127.0.0.1" ;
$wikiThisDBuser = "root" ;
$wikiThisDBpassword = "" ;

# Cache system enabled by default
$useCachedPages = true ;

# Now, load local site-specific settings
include_once ( "wikiLocalSettings.php" ) ;

# Initialize list of available character encodings to the default if none was set up.
if ( ! isset ( $wikiEncodingCharsets ) ) $wikiEncodingCharsets = array($wikiCharset);
if ( ! isset ( $wikiEncodingNames ) ) $wikiEncodingNames = array($wikiCharset); # Localised names


# Functions

# Is there any reason to localise this function? Ever?
function wikiLink ( $a ) {
	global $wikiArticleSource ;
	$a = str_replace ( " " , "+" , $a ) ;
	$a = str_replace ( "$1" , $a , $wikiArticleSource ) ;
	return $a ;
	}

?>
