<?
$THESCRIPT = "wiki.phtml" ;
#include ( "./specialPages.php" ) ;
include ( "./databaseFunctions.php" ) ;
include ( "./basicFunctions.php" ) ;
include ( "./wikiTitle.php" ) ;
include ( "./wikiPage.php" ) ;
include ( "./wikiUser.php" ) ;


## Language-dependant variables!

## Default/English:
$wikiTalk = "Talk";
$fieldSeparator = "³" ;
function recodeCharsetStub ( $text ) {
	# Some languages may change the internal coding used in the database
	return $text;
	}
	$recodeCharset = recodeCharsetStub ;

## Esperanto:
#$wikiTalk = "Priparolu" ;
#function recodeCharsetEo ( $text ) {
#	  $x = array(
#	  	"Cx", "cx",  "CX", "cX",
#		"Gx", "gx",  "GX", "gX",
#		"Hx", "hx",  "HX", "hX",
#		"Jx", "jx",  "JX", "jX",
#		"Sx", "sx",  "SX", "sX",
#		"Ux", "ux",  "UX", "uX");
#	  $u = array(
#		"\xc4\x88", "\xc4\x89",  "\xc4\x88", "\xc4\x89",
#		"\xc4\x9c", "\xc4\x9d",  "\xc4\x9c", "\xc4\x9d",
#		"\xc4\xa4", "\xc4\xa5",  "\xc4\xa4", "\xc4\xa5",
#		"\xc4\xb4", "\xc4\xb5",  "\xc4\xb4", "\xc4\xb5",
#		"\xc5\x9c", "\xc5\x9d",  "\xc5\x9c", "\xc5\x9d",
#		"\xc5\xac", "\xc5\xad",  "\xc5\xac", "\xc5\xad");
#	return str_replace ( $x , $u , $text ) ;
#	}
#	$recodeCharset = recodeCharsetEo ;

## French
#$wikiTalk = "Discuter" ;

## Polish:
#$wikiTalk = "Dyskusja" ;
#$fieldSeparator = "\xff";

## Spanish
#$wikiTalk = "Discusión" ;

## Where to get the old usemod database files from:
#$rootDir = "/home/groups/w/wi/wikipedia/htdocs/fpw/wiki-de/lib-http/db/wiki/page/" ;
#$rootDir = "/home/manske/wiki/lib-http/db/wiki/page/" ;
$rootDir = "/stuff/wiki/lib-http/db/wiki/page/" ;
#$rootDir = "/tmp/home/wiki-pl/wiki/db/page/" ;
#$rootDir = "/tmp/home/wiki-eo/lib-http/db/wiki/page/" ;

function scanText2 ( $fn ) {
	global $fieldSeparator ;
	$ret = "" ;

	#CONSTANTS
	#$FS = "³" ;
	$FS = $fieldSeparator ;
	$FS1 = $FS."1" ;
	$FS2 = $FS."2" ;
	$FS3 = $FS."3" ;

	#READING FILE
	$t = array () ;
	$fd = fopen ( $fn , "r" ) ;
	if ( $fd == false ) return "There was an error converting this file : file not found." ;
	while (!feof($fd)) {
		$buffer = fgets($fd, 99999);
		array_push ( $t , $buffer ) ;
		}
	fclose ( $fd ) ;

	array_pop ( $t ) ;
	$t = implode ( "" , $t ) ;

	#SPLIT PAGE
	$sp = explode ( $FS1 , $t ) ;

	$x = array_pop ( $sp ) ;
	$sections = explode ( $FS2 , $x ) ;
	foreach ( $sections as $y ) {
		$text = explode ( $FS3 , $y ) ;
		foreach ( $text as $z ) {
			$ret = $z ;
			}
		}

	return $ret ;
	}




function getFileName ( $an ) {
	global $rootDir ;
	$ret = $rootDir ;
	$sd = ucfirst ( substr ( $an , 0 , 1 ) ) ;
	if ( $sd < "A" or $sd > "Z" ) $sd = "other" ;
	$ret .= "$sd/".ucfirst($an).".db" ;
	return $ret ;
	}

function fixLinks ( $s ) {
	global $npage , $ll , $ull , $allTopics ;
	global $wikiTalk ;

	$talk = explode ( "/" , $npage->secureTitle ) ;
	if ( count($talk)==2 and strtolower($talk[1])==strtolower($wikiTalk) ) $isTalkPage = true ;
	else $isTalkPage = false ;

	# Automatic backlink from a subpage to a "main" page
	$backLink = "" ;
	if ( $isTalkPage == false AND count ( $talk ) == 2 ) {
		$backLink = ucfirst ( $talk[0] ) ;
		$backLink = str_replace ( "_" , " " , $backLink ) ;
		}

	# Automatic subpages, one last time...
	$s = ereg_replace ( "([\n ])/([a-zA-Z0-9]+)" , "\\1[[/\\2|/\\2]]" , $s ) ;

	$s = " $s" ;
	$a = explode ( "[[" , $s ) ;
	$s = array_shift ( $a ) ;
	foreach ( $a as $x ) {
		$b = explode ( "]]" , $x , 2 ) ;
		$s .= "[[" ;
		if ( count ( $b ) == 1 ) $s .= $x ;
		else {
			$c = explode ( "|" , $b[0] ) ;
			$link = $c[0] ;
			if ( substr ( $link , 0 , 1 ) == "/" ) { # Converting subpages
				$u = explode ( "/" , $npage->title ) ;
				if ( count ( $c ) == 1 ) array_push ( $c , substr ( $link , 1 ) ) ;
				$link = $u[0].$link ;
				}
			if ( ucfirst ( str_replace ( "_" , " " , $link ) ) == $backLink ) $backLink = "" ; # No backlink necessary
			$n = str_replace ( " " , "_" , $link ) ;
			$n = ucfirst ( $n ) ;
			$m = substr ( $n , 0 , 1 ) ;
			if ( $m < "A" or $m > "Z" ) $m = "0" ;
			if ( in_array ( $n , $allTopics[$m] ) )  array_push ( $ll , $n ) ;	
			else array_push ( $ull , $n ) ;

			# Re-linking /Talk pages to talk:
			$talk = explode ( "/" , $link ) ;
			if ( $talk[0] == "HomePage" ) {
				$talk[0] = "Main_Page" ;
				$link = $talk[0] ;
				if ( count ( $talk ) == 2 ) $link .= "/".$talk[1] ;
				}
			if ( count ( $talk ) == 2 and strtolower($talk[1]) == strtolower($wikiTalk) ) $link = strtolower($wikiTalk).":".$talk[0] ;
			else if ( $isTalkPage ) {
				if ( count ( $c ) == 1 ) array_push ( $c , $link ) ;
				$link = ":".$link ;
				}

			$s .= $link ;
			if ( count ( $c ) == 2 ) $s .= "|".$c[1] ;
			$s .= "]]".$b[1] ;
			}
		}
	if ( $backLink != "" ) $backLink = "\n:''See also :'' [[$backLink]]" ;
	return substr ( $s , 1 ).$backLink ;
	}

function convertText ( $s ) {
	$s = str_replace ( "\\'" , "'" , $s ) ;
	$s = str_replace ( "\\\"" , "\"" , $s ) ;
	$s = str_replace ( "\"" , "\\\"" , $s ) ;
	$s = str_replace ( "'" , "\\'" , $s ) ;

	$a = spliti ( "<nowiki>" , $s ) ;
	$s = fixLinks ( array_shift ( $a ) ) ;
	foreach ( $a as $x ) {
		$b = spliti ( "</nowiki>" , $x , 2 ) ;
		if ( count ( $b ) == 1 ) $s .= "<nowiki>".$x ;
		else $s .= "<nowiki>".$b[0]."</nowiki>".fixLinks($b[1]);
		}

	return $s ;
	}

function storeInDB ( $title , $text ) {
	global $of , $npage , $ll , $ull , $wikiTalk , $recodeCharset ;
	$ll = array () ;
	$ull = array () ;
	$title = str_replace ( "\\'" , "'" , $title ) ;
	$title = str_replace ( "\\\"" , "\"" , $title ) ;
	$npage = new wikiPage ;
	$npage->title = $title ;
	$npage->makeAll () ;
	$text = convertText ( $text ) ;
	$ll1 = implode ( "\n" , $ll ) ;
	$ull1 = implode ( "\n" , $ull ) ;
	$st = $npage->secureTitle ;

	# Move talk pages to talk namespace
	$talk = explode ( "/" , $st ) ;
	if ( $talk[0] == "HomePage" ) { $talk[0] = "Main_Page" ; $st = $talk[0] ; }
	if ( count ( $talk ) == 2 and $talk[1] == $wikiTalk ) $st = $wikiTalk.":".$talk[0] ;
	if ( count ( $talk ) == 2 and $talk[1] == strtolower($wikiTalk) ) return ;

	$sql = "INSERT INTO cur (cur_title,cur_ind_title,cur_text,cur_comment,cur_user,cur_user_text,cur_old_version,cur_minor_edit,cur_linked_links,cur_unlinked_links) VALUES ";
	$sql .= "(\"" . $recodeCharset ( $st ) . "\",\"" . $recodeCharset ( $st ) . "\",\"" . $recodeCharset ( $text ) . "\",";
	$sql .= "\"Automated conversion\",0,\"conversion script\",0,1,\"" . $recodeCharset ( $ll1 ) . "\",\"" . $recodeCharset ( $ull1 ) . "\");\n" ;
	fwrite ( $of , $sql ) ;
	}

function getTopics ( $dir ) {
	$ret = array () ;
	
	$mydir = opendir($dir);
	while ($entry = readdir($mydir)) {
		if ($entry != '.' && $entry != '..') {
			if ( is_dir ( "$dir/$entry" ) ) {
				$a = getTopics ( "$dir/$entry" ) ;
				foreach ( $a as $x ) array_push ( $ret , "$entry/$x" ) ;
			} else {
				$x = substr ( $entry , 0 , strlen ( $entry ) - 3 ) ;
				array_push ( $ret , $x ) ;
				}
			}
	}
	closedir($mydir);

	return $ret ;
	}

function dir2DB ( $letter )  {
	global $rootDir ;
	$a = getTopics ( "$rootDir/$letter" ) ;
	print "Reading :\n" ;
	foreach ( $a as $an ) {
		$fl = substr ( $an , 0 , 1 ) ;
		if ( $fl >= "a" and $fl <= "z" ) {
			print "IGNORING LOWERCASE FIRST FILE : $an\n" ;
		} else {
			print "$an" ;
			$fn = getFileName ( $an ) ;
			storeInDB ( $an , scantext2 ( $fn ) ) ;
			print "\n" ;
			}
		}
	print "\n" ;
	}

function getAllTopics () {
	global $allTopics , $rootDir , $wikiTalk;
	$allTopics = array () ;
	for ( $c = 65 ; chr($c) <= "Z" ; $c++ ) $allTopics[chr($c)] = getTopics ( "$rootDir/".chr($c) ) ;
	$allTopics["0"] = getTopics ( "$rootDir/other" ) ;
	}

# MAIN PROGRAM
	global $rootDir ;

	set_time_limit ( 30000 ) ; # Enough time for this script...

	global $ll , $ull , $allTopics ;
	$ll = array () ;
	$ull = array () ;
	getAllTopics () ;

	global $l , $of ;
	$of = fopen ( "./newiki.sql" , "w" ) ;
	fwrite ( $of , "DELETE FROM cur WHERE cur_title NOT LIKE \"%:%\";\n" ) ;
	fwrite ( $of , "DELETE FROM cur WHERE cur_title LIKE \"$wikiTalk:%\";\n" ) ;
	do {
		if ( !isset ( $l ) ) $l = 65 ;
		if ( $l == "other" ) $letter = "other" ;
		else $letter = chr ( $l ) ;
		$nl = $l+1 ;
		if ( $letter == "Z" ) $nl = "other" ;
		$l = $nl ;
		dir2DB ( $letter ) ;
	} while ( $letter != "other" ) ;
	fclose ( $of ) ;

	print "FINISHED!\n" ;
?>
