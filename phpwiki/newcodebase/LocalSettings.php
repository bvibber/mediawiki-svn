<?
# For local site-specific settings.  This should be the
# only thing you need to configure to install the software.

include_once( "DefaultSettings.php" );

# URLs for the wiki site
#
$wgServer           = "http://localhost";
$wgDirectory	    = "/wiki";
$wgStyleSheetPath   = "$wgServer$wgDirectory/stylesheets";
$wgScript           = "$wgDirectory/wiki.phtml";
$wgArticlePath      = "$wgServer$wgScript?title=$1";
$wgUploadPath       = "$wgServer/upload";
$wgUploadDirectory	= "/var/www/html/upload";
$wgLogo		    	= "$wgUploadPath/wiki.png";

# MySQL settings
#
$wgDBserver         = "127.0.0.1";
$wgDBname           = "newwiki";
$wgDBuser           = "wikiuser";
$wgDBpassword       = "xxx";

# Turn this on during database maintenance
#
# $wgReadOnly = true;

# Turn this on to get HTML debug comments
#
# $wgDebugComments	= true;
# $wgDebugLogFile	= "";

# Which interwiki prefix represents _this_ wiki?  This
# only needs to be set when installing the software on
# on of the sites recognized as an interwiki.
#
$wgLocalInterwiki   = "w";
#$wgLanguageCode	= "en";

?>
