<?
####################################################################### PARSER FUNCTIONS

function replaceAllEntries ( $s , $f1 , $f2 , $r1 , $r2 ) {
	$quit = false ;
	while ( !$quit ) {
		$pieces1=spliti($f1,$s,2);
		if ( count($pieces1) < 2 ) $quit = true ;
		else	{
			$pieces2=spliti($f2,$pieces1[1],2);
			if ( count ( $pieces2 ) < 2 ) $quit = true ;
			else	{
				$middle=$pieces2[0] ;
				$s=$pieces1[0].$r1.$middle.$r2.$pieces2[1];
				}
			}
		}

	

	return $s ;
	}

##############################################################
# DISPLAY PARSER ; INCOMPLETE!!!!
##############################################################

function pingPongReplace ( $x , $s , $r1 , $r2 ) {
	$xa = explode ( $s , $x ) ;
	$ret = array_shift ( $xa ) ;
	$r = $r1 ;
	foreach ( $xa as $y ) {
		$ret .= $r.$y ;
		if ( $r == $r1 ) $r = $r2 ;
		else $r = $r1 ;
		}
	if ( $r == $r2 ) $ret .= $r2 ;
	return $ret ;
	}

function parseLinks ( $x ) {
	global $title ;
	$namespace = getNamespace ( $title ) ;
	$name = stripNamespace ( $title ) ;
	while ( substr_count ( $x , "[[" ) ) {
		$p1 = spliti ( "\[\[" , $x , 2 ) ;
		$p2 = spliti ( "\]\]" , $p1[1] , 2 ) ;
		if ( count ( $p2 ) == 1 ) break ;
		$p3 = spliti ( "\|" , $p2[0] , 2 ) ;
		if ( count ( $p3 ) == 1 ) array_push ( $p3 , $p3[0] ) ;
		$topic = $p3[0] ;
		$text = $p3[1] ;

		$topic = getSecureTitle ( $topic ) ;
		if ( strpos ( $topic , ":" ) === false ) { #No namespace given, current is used
			if ( strpos ( $title , ":" ) === true ) $topic = "$namespace:$topic" ;
			}

		if ( doesTopicExist ( $topic ) ) $enc = "<a href=\"$PHP_SELF?action=view&title=$topic\">$text</a>" ;
		else {
			if ( strpos ( $text , " " ) ) $text = "[$text]" ;
			$enc = "$text<a href=\"$PHP_SELF?action=edit&title=$topic\">?</a>" ;
			}

		$x = $p1[0].$enc.$p2[1] ;
		}	
	return $x ;
	}

function parseExternalLinks ( $x , &$cnt ) {
	while ( count ( spliti ( "\[http://" , $x , 2 ) ) > 1 ) {
		$p1 = spliti ( "\[http://" , $x , 2 ) ;
		$p2 = spliti ( "\]" , $p1[1] , 2 ) ;
		if ( count ( $p2 ) == 1 ) break ;
		$p3 = spliti ( " " , $p2[0] , 2 ) ;
		if ( count ( $p3 ) == 1 ) array_push ( $p3 , $cnt ) ;
		$topic = $p3[0] ;
		$text = $p3[1] ;
		$cnt++ ;

		$x = "$p1[0]<a href=\"http://$topic\">[$text]</a>$p2[1]" ;
		}	
	return $x ;
	}

function parseVariables ( $s ) {
	$var=date("m"); $s = str_replace ( "{{{CURRENTMONTH}}}" , $var , $s ) ;
	$var=date("F"); $s = str_replace ( "{{{CURRENTMONTHNAME}}}" , $var , $s ) ;
	$var=date("d"); $s = str_replace ( "{{{CURRENTDAY}}}" , $var , $s ) ;
	$var=date("l"); $s = str_replace ( "{{{CURRENTDAYNAME}}}" , $var , $s ) ;
	$var=date("Y"); $s = str_replace ( "{{{CURRENTYEAR}}}" , $var , $s ) ;

	if ( strstr ( $s , "{{{NUMBEROFARTICLES}}}" ) ) {
		$connection=getDBconnection() ;
		mysql_select_db ( "nikipedia" , $connection ) ;
		$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_title NOT LIKE \"%ikipedia%\" AND cur_text LIKE \"%,%\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		$var = mysql_fetch_object ( $result ) ;
		$var = $var->number ;
		mysql_free_result ( $result ) ;
		$s = str_replace ( "{{{NUMBEROFARTICLES}}}" , $var , $s ) ;
		}
	return $s ;
	}

function newParser ( $s ) {
	$goodTags = array ( "b" , "/b" , "p" , "/p" , "i" , "/i" , "hr" , "br" ) ;
	$obegin = "" ;

	$cntEL = 0 ;
	$as = explode ( "\n" , $s ) ;
	foreach ( $as as $x ) {
		
		# Double blank line
		if ( trim ( $x ) == "" ) $x = "<br>" ;

		# Horizontal line		
		$x = ereg_replace ( "-----*" , "<hr>" , $x ) ;

		# Replacing wiki-tags with HTML-tags
		$x = pingPongReplace ( $x , "'''" , "<b>" , "</b>" ) ;
		$x = pingPongReplace ( $x , "''" , "<i>" , "</i>" ) ;

		$xa = explode ( "<" , "$x" ) ;
		if ( substr ( $x , 0 , 1 ) == "<" ) $x = "" ;
		else $x = array_shift ( $xa ) ;
		foreach ( $xa as $y ) {
			$ya = explode ( ">" , $y , 2 ) ;
			if ( count($ya) < 2 ) {
				if ( $ya[0] != "" ) 
					$x .= "&lt;$ya[0]" ;
			} else {
				if ( in_array ( $ya[0] , $goodTags ) ) $x .= "<$ya[0]>$ya[1]" ;
				else $x .= "&lt;$ya[0]&gt;$ya[1]" ;
				}
			}

		$x = parseLinks ( $x ) ;
		$x = parseExternalLinks ( $x , $cntEL ) ;
		$x = parseVariables ( $x ) ;

		$fc = substr ( $x , 0 , 1 ) ;
		$ofc = substr ( $obegin , 0 , 1 ) ;
		for ( $a = 0 ; $x[$a] == $fc ; $a++ ) ;
		$nbegin = substr ( $x , 0 , $a ) ;
		$pre = "" ;

		# Undoing obegin
		if ( $ofc == $fc ) {
			while ( $obegin > $nbegin ) {
				if ( $fc == ":" ) $x .= "\n</DL>\n" ;
				$obegin = substr ( $obegin , 1 ) ;
				}
			while ( $obegin < $nbegin ) {
				if ( $fc == ":" ) $pre .= "<DL>" ;
				$obegin .= $fc ;
				}
		} else {
			for ( $a = 0 ; $a < $ofc ; $a++ ) {
				if ( $ofc == ":" ) $x .= "\n</DL>\n" ;
				}
			}

		#Inserting code
		if ( $fc == ":" ) $pre .= "<dt><dd>" ;
		$x = $pre.$x ;

		$obegin = $nbegin ;

		$ret .= "$x \n" ;
		}
	

	return $ret ;
	}

############################################################################################################################
function parseContent ( $s ) {
	$newOutput = newParser ( $s ) ;
	return $newOutput ;
	global $title , $dummyArticle ;
	if ( $s == "" ) $s = $dummyArticle ;
	$s = str_replace ( "\r" , "" , $s ) ;

#	Automatic /Talk page
#	if ( !strpos ( $title , "/" ) and !strpos ( $s , "/Talk" ) ) $s .= "\n----\n[[/Talk]]" ;

	$namespace = "" ;
	$rtitle = $title ;
	$dbpos = strpos ( $title , ":" ) ;
	if ( $dbpos ) {
		$namespace = substr ( $title , 0 , $dbpos ) ;
		$rtitle = substr ( $title , $dbpos ) ;
		}

	# Replace {{{variable}}}
	$var=date("m"); $s = str_replace ( "{{{CURRENTMONTH}}}" , $var , $s ) ;
	$var=date("F"); $s = str_replace ( "{{{CURRENTMONTHNAME}}}" , $var , $s ) ;
	$var=date("d"); $s = str_replace ( "{{{CURRENTDAY}}}" , $var , $s ) ;
	$var=date("l"); $s = str_replace ( "{{{CURRENTDAYNAME}}}" , $var , $s ) ;
	$var=date("Y"); $s = str_replace ( "{{{CURRENTYEAR}}}" , $var , $s ) ;

	if ( strstr ( $s , "{{{NUMBEROFARTICLES}}}" ) ) {
		$connection=getDBconnection() ;
		mysql_select_db ( "nikipedia" , $connection ) ;
		$sql = "SELECT COUNT(*) as number FROM cur WHERE cur_title NOT LIKE \"%/Talk\" AND cur_title NOT LIKE \"%ikipedia%\" AND cur_text LIKE \"%,%\"" ;
		$result = mysql_query ( $sql , $connection ) ;
		$var = mysql_fetch_object ( $result ) ;
		$var = $var->number ;
		mysql_free_result ( $result ) ;
		$s = str_replace ( "{{{NUMBEROFARTICLES}}}" , $var , $s ) ;
		}



	# Replace [[ and ]] with internal links
	$tag1="\[\[";
	$tag2="\]\]";
	while ( eregi($tag1,$s) && eregi($tag2,$s) ) {
		$pieces1=spliti($tag1,$s,2);
		$pieces2=spliti($tag2,$pieces1[1],2);
		$middle=$pieces2[0] ;
		$original = $middle ;
		$linkto=getSecureTitle($middle);

		if ( strstr ( $middle , "|" ) ) { # show left part, link to right part
			$pos = strpos ( $middle , "|" ) ;
			$linkto = trim ( substr ( $middle , 0 , $pos ) ) ;
			$middle = trim ( substr ( $middle , $pos+1 , 9999 ) ) ;
			}

		if ( substr ( $linkto , 0 , 1 ) == "/" ) {
			$p=spliti("/",$rtitle,2);
			$linkto = $p[0].$linkto ;
			}

		# Namespace
		$dbpos = strpos ( " ".$linkto , ':' ) ;
		if ( $dbpos == true ) {
			if ( $dbpos == 1 ) $linkto = substr ( $linkto , 1 ) ;
			} else {
				if ( $namespace != "" ) $linkto = $namespace.":".$linkto ;
			}

		if ( substr_count ( $linkto , "/" ) < 2 ) {
			$st = strtolower ( getSecureTitle ( $linkto ) ) ;
			if ( $st == "random_page" or $st == "page_index" ) $exists = true ;
			else $exists = doesTopicExist($linkto) ;
			if ( $exists ) {
				$middle="<a href=\"$PHP_SELF?title=$linkto&action=view\">$middle</a>" ;
			} else {
				if ( strstr($middle," ") ) $middle="[$middle]" ;
				$middle="$middle<a href=\"$PHP_SELF?title=$linkto&action=edit\">?</a>" ;
				}
			} else $middle = "$original" ;
		$s=$pieces1[0].$middle.$pieces2[1];
		}

	# Replace '''
	$s = replaceAllEntries ( $s , "'''" , "'''" , "<b>" , "</b>" ) ;


	# Replace ''
	$s = replaceAllEntries ( $s , "''" , "''" , "<i>" , "</i>" ) ;

	# Replace *
	$s = replaceAllEntries ( $s , "\n\*" , "\n" , "<ul><li>" , "</li></ul>\n" ) ;
	$s = replaceAllEntries ( $s , "<ul><li>\*" , "</li></ul>" , "<ul><li><ul><li>" , "</li></ul></li></ul>\n" ) ;
	$s = str_replace ( "</ul>\n" , "</ul>" , $s ) ;
	while ( strstr ( $s , "</li></ul><ul><li>" ) or strstr ( $s , "</li><li><ul>" ) ) {
		$s = str_replace ( "</li></ul><ul><li>" , "</li><li>" , $s ) ;
		$s = str_replace ( "</li><li><ul>" , "<ul>" , $s ) ;
		}


	# Replace #
	$s = replaceAllEntries ( $s , "\n\#" , "\n" , "<ol><li>" , "</li></ol>\n" ) ;
	$s = replaceAllEntries ( $s , "<ol><li>\#" , "</li></ol>" , "<ol><li><ol><li>" , "</li></ol></li></ol>\n" ) ;
	$s = str_replace ( "</ol>\n" , "</ol>" , $s ) ;
	while ( strstr ( $s , "</li></ol><ol><li>" ) or strstr ( $s , "</li><li><ol>" ) ) {
		$s = str_replace ( "</li></ol><ol><li>" , "</li><li>" , $s ) ;
		$s = str_replace ( "</li><li><ol>" , "<ol>" , $s ) ;
		}

	# Courier
	$s = replaceAllEntries ( $s , "\n " , "\n" , "\n&nbsp;<font face=\"courier\">" , "</font>\n" ) ;


	# Line by line
	$arr = explode ( "\n" , $s ) ;
	$narr = array () ;

	$dp = false ;
	foreach ( $arr as $x ) {
		$y = $x ;

		# External images
		while ( strstr ( $y , "http://" ) ) {
			$pieces1 = spliti("http://",$y,2);
			$pieces2 = spliti(" ",$pieces1[1],2);
			$thelink = $pieces2[0] ;
			$thetype = strtolower ( strrchr ( $thelink , "." ) ) ;
			if ( $thetype == ".gif" or $thetype == ".png" or $thetype == ".jpg" or $thetype == ".tif" )
				$y = $pieces1[0]."<img src=\"~~HTTP~~".$thelink."\">".$pieces2[1] ;
			else $y = $pieces1[0]."~~HTTP~~".$thelink." ".$pieces2[1] ;
			}
		$y = str_replace ( "~~HTTP~~" , "http://" , $y ) ;

		if ( substr ( $y , 0 , 1 ) == ":" ) {
			$y = "<dt><dd>".substr ( $y , 1 , 99999 ) ;
			if ( !$dp ) $y = "<DL>".$y ;
			$dp = true ;
		} else if ( $dp ) {
			$y .= "</DL>" ;
			$dp = false ;
			}
		if ( substr ( $y , 0 , 4 ) == "----" ) $y = "<hr>" ;
		if ( substr ( $y , 0 , 4 ) == "<hr>" ) $footnote = 1 ;

		# Outside links
		$footnote = 1 ;
		$tag1="\[http://";
		$tag2="\]";
		while ( eregi($tag1,$y) && eregi($tag2,$y) ) {
			$pieces1=spliti($tag1,$y,2);
			$pieces2=spliti($tag2,$pieces1[1],2);
			$linkto=trim($pieces2[0]) ;

			if ( strpos ( $linkto , " " ) ) {
				$middle = substr ( $linkto , strpos ( $linkto , " " ) + 1 , 99999 ) ;
				$linkto = substr ( $linkto , 0 , strpos ( $linkto , " " ) ) ;
			} else {
				$middle = $footnote ;
				$footnote++ ;
				}

			$y=$pieces1[0]."<a href=\"http://$linkto\">[$middle]</a>".$pieces2[1];
			}


		if ( $y == "" ) $y = "</p><p>" ;
		array_push ( $narr , $y ) ;
		}

	$s = implode ( "\n" , $narr ) ;
	
	# Final
	$s = "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML//EN\">".$s ; # Does this do anything good?


	# Double display
#	$s = "<table width=100%><tr><td valign=top width=50%>$s</td><td valign=top width=50%>$newOutput</td></tr></table>" ;
	$s = "$newOutput\n<hr>\n$s" ;
	return $s ;
	}

function getCurrentUserText () {
	global $USERNAME , $USERPASSWORD , $USERLOGGEDIN ;
	global $REMOTE_ADDR ;
#	if ( $USERLOGGEDIN != "YES" and $USERNAME != "" and $USERPASSWORD != "" ) {
#		if ( checkUserPassword ( $USERNAME , $USERPASSWORD ) ) setcookie ( "USERLOGGEDIN" , "YES" ) ;
#		$USERLOGGEDIN = "YES" ;
#		}
	if ( $USERLOGGEDIN != "YES" or $USERNAME == "" ) {
		$u = "$REMOTE_ADDR<br>\n<a href=\"$PHP_SELF?action=login\">log in</a>" ;
		}
	else {
		$v = getSecureTitle ( $USERNAME ) ;
		$u = "<a href=\"$PHP_SELF?action=view&title=user:$v\">$USERNAME</a><br>\n<a href=\"$PHP_SELF?action=logout\">log out</a>" ;
		$u .= " <a href=\"$PHP_SELF?action=prefs\">Preferences</a>" ;
		}
	return $u ;
	}
?>