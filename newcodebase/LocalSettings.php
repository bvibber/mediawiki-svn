<?
# For local site-specific settings.  This should be the
# only thing you need to configure to install the software.

include_once( "./DefaultSettings.php" );

# URLs for the wiki site
#
$wgServer           = "http://www.piclab.com";
$wgStyleSheetPath   = "$wgServer/style";
$wgScript           = "/newwiki/wiki.phtml";
$wgRedirectScript	= "/newwiki/redirect.phtml";
$wgArticlePath      = "$wgServer$wgScript?title=$1";
$wgUploadPath       = "http://images.piclab.com";
$wgUploadDirectory	= "/rfs/upload";
$wgLogo				= "$wgUploadPath/wiki.png";

# MySQL settings
#
$wgDBserver         = "127.0.0.1";
$wgDBname           = "yaw";
$wgDBuser           = "wikiuser";
$wgDBpassword       = "oberon";
$wgDBminWordLen		= 3;

# Turn this on during database maintenance
#
# $wgReadOnly = true;

# Turn this on to get HTML debug comments
#
# $wgDebugComments	= true;
$wgDebugLogFile 	= "/home/lee/src/phpwiki/newcodebase/logfile";

# Which interwiki prefix represents _this_ wiki?  This
# only needs to be set when installing the software on
# on of the sites recognized as an interwiki.
#
$wgLocalInterwiki   = "w";

$wgInputEncoding	= "ISO-8859-1";
$wgOutputEncoding	= "ISO-8859-1";

?>
