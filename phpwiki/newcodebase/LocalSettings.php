<?
# For local site-specific settings.  This should be the
# only thing you need to configure to install the software.

include_once( "./DefaultSettings.php" );

# URLs and directories for the wiki site
#
$wgServer           = "http://www.piclab.com";
$wgScriptPath	    = "/wiki";
$wgScript           = "{$wgScriptPath}/wiki.phtml";
$wgRedirectScript	= "{$wgScriptPath}/redirect.phtml";
$wgStyleSheetPath   = "{$wgServer}/style";
$wgArticlePath      = "{$wgServer}{$wgScript}?title=$1";
$wgUploadPath       = "{$wgServer}/upload";
$wgLogo				= "{$wgUploadPath}/wiki.png";
$wgUploadDirectory	= "/var/www/html/upload";

# MySQL settings
#
$wgDBserver         = "127.0.0.1";
$wgDBname           = "yaw";
$wgDBuser           = "wikiadmin";
$wgDBpassword       = "oberon";

# Creating the file below makes the DB read-only; do this
# during database maintenance.
#
$wgReadOnlyFile		= "/var/www/html/upload/readonly";

# Turn this on to get debug comments.  Logfile should only
# be used during testing, never in production.
#
$wgDebugComments	= true;
$wgDebugLogFile 	= "/var/www/html/upload/logfile";

# Which interwiki prefix represents _this_ wiki?  This
# only needs to be set when installing the software on
# on of the sites recognized as an interwiki.
#
$wgLocalInterwiki   = "w";

# $wgInputEncoding	= "ISO-8859-1";
# $wgOutputEncoding	= "ISO-8859-1";

?>
