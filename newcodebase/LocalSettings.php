<?
# For local site-specific settings.  This should be the
# only thing you need to configure to install the software.

include_once( "./DefaultSettings.php" );

# URLs and directories for the wiki site
#
$wgServer           = "http://www.piclab.com";
$wgScriptPath	    = "/wiki";
$wgScript           = "{$wgScriptPath}/wiki.phtml";
$wgStyleSheetPath   = "{$wgServer}/style";
$wgArticlePath      = "{$wgServer}{$wgScript}?title=$1";
$wgUploadPath       = "{$wgServer}/upload";
$wgLogo				= "{$wgUploadPath}/wiki.png";
$wgUploadDirectory	= "/var/www/html/upload";

# MySQL settings
#
$wgDBserver         = "127.0.0.1";
$wgDBname           = "newwiki";
$wgDBuser           = "wikiuser";
$wgDBpassword       = "userpwd";

# Turn this on during database maintenance
#
# $wgReadOnly = true;

# Turn this on to get HTML debug comments
#
$wgDebugComments	= true;
$wgDebugLogFile 	= "/var/www/html/wikitest/logfile";

# Which interwiki prefix represents _this_ wiki?  This
# only needs to be set when installing the software on
# on of the sites recognized as an interwiki.
#
$wgLocalInterwiki   = "w";

# $wgInputEncoding	= "ISO-8859-1";
# $wgOutputEncoding	= "ISO-8859-1";

?>
