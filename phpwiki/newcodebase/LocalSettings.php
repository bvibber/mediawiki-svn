<?
# For local site-specific settings.  This should be the
# only thing you need to configure to install the software.

include_once( "./DefaultSettings.php" );

# URLs for the wiki site
#
$wgServer           = "http://www.myhost.com";
$wgStyleSheetPath   = "$wgServer/style";
$wgScript           = "/wiki.phtml";
$wgRedirectScript	= "/redirect.phtml";
$wgArticlePath      = "$wgServer$wgScript?title=$1";
$wgUploadPath       = "http://www.myhost.com/upload";
$wgUploadDirectory	= "/usr/local/apache/htdocs/upload";
$wgMathPath         = "http://wroclaw.taw.pl.eu.org/~taw/wiki/math";
$wgMathDirectory    = "/usr/local/apache/htdocs/math/";
$wgTmpDirectory     = "/usr/local/apache/htdocs/tmp/";
$wgLogo				= "$wgUploadPath/wiki.png";

# MySQL settings
#
$wgDBserver         = "127.0.0.1";
$wgDBname           = "wikidb";
$wgDBuser           = "wikiuser";
$wgDBpassword       = "userpass";
$wgDBadminpassword  = "adminpass";
$wgDBminWordLen		= 3;

# Turn this on during database maintenance
#
# $wgReadOnly = true;

# Turn this on to get HTML debug comments
#
# $wgDebugComments	= true;
$wgDebugLogFile 	= "/usr/local/apache/htdocs/upload/logfile";

# Which interwiki prefix represents _this_ wiki?  This
# only needs to be set when installing the software on
# on of the sites recognized as an interwiki.
#
$wgLocalInterwiki   = "w";

$wgInputEncoding	= "ISO-8859-1";
$wgOutputEncoding	= "ISO-8859-1";

?>
