<?
# For local site-specific settings.  This should be the
# only thing you need to configure to install the software.

include_once( "DefaultSettings.php" );

# URLs for the wiki site
#
$wgServer           = "http://192.168.0.14";
$wgDirectory	    = "/cvs/phpwiki/phpwiki/newcodebase" ;
$wgStyleSheetPath   = "$wgServer$wgDirectory/stylesheets";
$wgScript           = "$wgDirectory/wiki.phtml";
$wgArticlePath      = "$wgServer/newwiki/$1";
$wgUploadPath       = "$wgDirectory/upload";
$wgLogo		    = "$wgServer$wgUploadPath/wiki.png";

# MySQL settings
#
$wgDBserver         = "127.0.0.1";
$wgDBname           = "newwiki";
$wgDBuser           = "root";
$wgDBpassword       = "";

# Turn this on during database maintenance
#
# $wgReadOnly = true;

# Turn this on to get HTML debug comments
#
$wgDebugComments	= true;
$wgDebugLogFile		= "";

# Which interwiki prefix represents _this_ wiki?  This
# only needs to be set when installing the software on
# on of the sites recognized as an interwiki.
#
$wgLocalInterwiki   = "w";
#$wgLanguageCode	= "en";

?>
