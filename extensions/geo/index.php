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
show:germany.bavaria
fit:germany.bavaria
" ) ;
#style:germany.bavaria[state],germany.bavaria[isle]=fill:#CCCCCC; stroke:black; stroke-width:10
#style:germany.hamburg=fill:red

$svg = $p->getSVG () ;

$batik_cmd = "java -jar /home/magnus/batik-1.5.1/batik-rasterizer.jar $1 -d $2" ;
$output_filename = "/srv/www/htdocs/test.png" ;

# Storing in temp file
$tmpfname = tempnam ( "" , "TR2" ) . ".svg" ;
$outfname = tempnam ( "" , "TR2" ) . ".png" ;
$handle = fopen($tmpfname, "w");
fwrite($handle, $svg);
fclose($handle);

$cmd = str_replace ( "$1" , $tmpfname , $batik_cmd ) ;
$cmd = str_replace ( "$2" , $outfname , $cmd ) ;

$out = system ( $cmd ) ;

unlink($tmpfname);

print "<html><head></head><body>" ;
print $cmd . " : " . $out ;
#print "<img src=\"/test.png\"/>" ;
print "</body></html>" ;

?>