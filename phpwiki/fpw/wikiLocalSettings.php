<?
# For local site-specific settings. The default configuration is set up
# in wikiSettings.php; DO NOT makes changes there. The settings here
# override the settings in that file.

## Language to use. Default is "en" (English).
# $wikiLanguage = "de"; # Deutsch (German)
# $wikiLanguage = "eo"; # Esperanto

$useCachedPages = false; # Disable page cache
# $wikiReadOnly = true; # Don't allow page editing

$wikiThisDBserver = "127.0.0.1";
$wikiSQLServer = "wikidb";
$wikiThisDBuser = "wikiuser";
$wikiThisDBpassword = "xxx";

# Preferred URL of the web server running Wikipedia
$wikiCurrentServer = "http://www.piclab.com";

# Location of the main wiki.phtml script on that server:
$THESCRIPT = "/wiki/wiki.phtml";

# How to access an article on your site. Change this if you use
# apache's rewrite module to produce nicer URLs on your site (as is
# done on wikipedia.com)
$wikiArticleSource = "$wikiCurrentServer$THESCRIPT?title=$1";

$wikiLogoFile = "/images/emblogo1.gif" ;

# Namespace backgrounds
$wikiNamespaceBackground = array () ;
$wikiNamespaceBackground[$wikiTalk] = "#CCFFFF" ;
$wikiNamespaceBackground["user_talk"] = $wikiNamespaceBackground["talk"] ;
$wikiNamespaceBackground["wikipedia_talk"] = $wikiNamespaceBackground["talk"] ;
$wikiNamespaceBackground[$wikiUser] = "#CCFFFF" ;
$wikiNamespaceBackground[$wikiWikipedia] = "#CCFFFF" ;
$wikiNamespaceBackground["log"] = "#CCFFFF" ;
$wikiNamespaceBackground["special"] = "#CCFFFF" ;

?>
