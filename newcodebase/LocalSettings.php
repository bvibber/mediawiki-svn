<?
# For local site-specific settings.  This should be the
# only thing you need to configure to install the software.

include_once( "./DefaultSettings.php" );

# URLs for the wiki site
#
$wgServer           = "http://chesoo.fokus.gmd.de/cvswp";
$wgStyleSheetPath   = "$wgServer/stylesheets";
$wgScript           = "/wiki.phtml";
$wgRedirectScript	= "/redirect.phtml";
$wgArticlePath      = "$wgServer$wgScript?title=$1";
$wgUploadPath       = "$wgServer/images";
$wgUploadDirectory	= "/var/www/htdocs/cvswp/images";
$wgLogo				= "$wgUploadPath/wiki.png";
$wgLanguageCode = "en";

# MySQL settings
#
$wgDBserver         = "localhost";
$wgDBname           = "wikidb";
$wgDBuser           = "nobody";
$wgDBpassword       = "suppball";
$wgDBadminpassword  = "suppball";
$wgDBminWordLen		= 3;

# Turn this on during database maintenance
#
# $wgReadOnly = true;

# Turn this on to get HTML debug comments
#
# $wgDebugComments	= true;
# $wgDebugLogFile 	= "/usr/local/apache/htdocs/upload/logfile";

# Which interwiki prefix represents _this_ wiki?  This
# only needs to be set when installing the software on
# on of the sites recognized as an interwiki.
#
$wgLocalInterwiki   = "w";

$wgInputEncoding	= "ISO-8859-1";
$wgOutputEncoding	= "ISO-8859-1";

?>
