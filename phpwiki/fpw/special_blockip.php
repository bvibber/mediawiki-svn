<?
include_once ( "special_makelog.php" ) ;

function blockIP () {
	 global $target , $vpage , $wikiBlockIPTitle , $THESCRIPT , $wikiArticleSource , $wikiRecodeInput , $wikiBlockExplain ,
	 $wikiBlockIPText , $user , $wikiBlockedIPsLink , $wikiGetDate , $iamsure , $CommentBox , $wikiBlockInvalidIPAddress , $wikiBlockIP ;
	 $vpage->special ( $wikiBlockIPTitle ) ;

	global $REMODE_ADDR ;
	
	# Can only ban IP addresses currently; some basic sanity checking is in order
	if ( ! preg_match ( '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/' , $target ) ) {
		return ( str_replace ( "$1" , htmlspecialchars ( $target ) , $wikiBlockInvalidIPAddress ) ) ;
		}
	
	if ( $iamsure == "yes" ) {
#	$now = date ( "Y-m-d H:i:s" , time () ) ;
#	$userText = "[[$wikiUser:$user->name|$user->name]]" ;
		$logText = "$target (".$wikiGetDate(time()).")" ;
		if ( isset ( $CommentBox ) )
			$CommentBox = $wikiRecodeInput ( stripslashes ( $CommentBox ) ) ;
		$line = str_replace ( array("$1","$2") ,
			array("[" . str_replace ( "$1", "special:Contributions&theuser=$target",
				$wikiArticleSource ) . " $target]",$user->name) , $wikiBlockIPText )
			. ($CommentBox ? (" <b><nowiki>[" . $CommentBox . "]</nowiki></b>" ) : "" ) ;
		$linerc = str_replace ( array("$1","$2") ,
			array( $target , $user->name ) , $wikiBlockIPText ) . ($CommentBox ? (" - " . $CommentBox) : "" );
		makeLog ( $wikiBlockedIPsLink , "*$logText : $line\n" , $linerc , false ) ;

		 return "<font size='+3' color=red>$line</font>" ;
	}
	
	# Ask for confirmation *and a reason why!*
	return "<form action=\"$THESCRIPT\" method=\"post\">"
	. str_replace ( "$1" , $target , $wikiBlockExplain ) . "<br>
	<input type=hidden name=title value=\"special:blockIP\">
	<input type=hidden name=target value=\"$target\">
	<input type=\"text\" value=\"\" name=\"CommentBox\" size=\"50\" maxlength=\"200\"><br>
	<input type=hidden name=iamsure value=yes>
	<input type=submit name=Upload value=$wikiBlockIP>
	</form>";
	}
?>
