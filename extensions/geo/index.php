<?
define( "MEDIAWIKI", true );

$wikibasedir = "../../phase3" ;
require_once( "{$wikibasedir}/includes/Defines.php" );
require_once( "{$wikibasedir}/LocalSettings.php" );
require_once( "{$wikibasedir}/includes/Setup.php" );
require_once( "geo.php");

$p = new geo_params ;

$p->settings ( "
languages:de,en
show:germany
fit:germany
style:germany[state]=fill:#CCCCCC; stroke:black; stroke-width:10
style:germany.hamburg=fill:red
" ) ;

print $p->getSVG () ;

?>