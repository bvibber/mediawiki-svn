<?
include_once ( "special_makelog.php" ) ;

function blockIP () {
	 global $target , $vpage , $wikiBlockIPTitle ,
	 $wikiBlockIPText , $user ;
	 $vpage->special ( $wikiBlockIPTitle ) ;

	global $REMODE_ADDR ;
#	$now = date ( "Y-m-d H:i:s" , time () ) ;
#	$userText = "[[$wikiUser:$user->name|$user->name]]" ;
	$logText = "$target (".time().")" ;
	$line = str_replace ( array("$1","$2") ,
	array($target,$user->name) , $wikiBlockIPText ) ;
	makeLog ( "wikipedia:Blocked IPs" , "*$logText : $line\n" , $line , false ) ;

	 return "<font size='+3' color=red>$line</font>" ;
	 }
?>
