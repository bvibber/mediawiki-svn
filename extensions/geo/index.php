<?

define( "MEDIAWIKI", true );

$wikibasedir = "../../phase3" ;
require_once( "{$wikibasedir}/includes/Defines.php" );
require_once( "{$wikibasedir}/LocalSettings.php" );
require_once( "{$wikibasedir}/includes/Setup.php" );
require_once( "geo.php");

$g = new geo ;
$g->set_from_id ( "germany" ) ;

$p = new geo_params ;
$p->languages = array ( "de" , "en" ) ; # Fallback to "en" if there's no "de"
$p->style_fill = array ( "germany.hamburg" => "fill:red" ) ;

$svg = $g->draw ( $p ) ;
$svg .= $p->get_svg_labels () ;

$styles = "" ;

$viewBox = $p->get_view_box () ;

$svg = 
'<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/SVG/DTD/svg10.dtd">
<svg viewBox="' . $viewBox .
'" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve">
'
	. $styles .
'	<g id="mainlayer">
'
	. $svg .
	'</g>
</svg>
' ;

print $svg ;

?>