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
draw:germany[state],germany.bavaria[city]
draw:germany.sh.cities#kiel
style:germany[state],germany[isle]=fill:#CCCCCC; stroke:black; stroke-width:10
style:germany.hamburg=fill:red
label:germany.bavaria[city]=font-size:medium;fill-opacity:1.0;clickable:yes
label:germany[state]=font-size:medium;fill-opacity:0.7
" ) ;

$svg = $p->getSVG () ;
print $svg ;
exit ( 0 ) ; # just make SVG




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