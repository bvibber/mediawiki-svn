#!/usr/bin/php -q
<?
$THESCRIPT = "wiki.phtml" ;
#include ( "./specialPages.php" ) ;
include ( "./databaseFunctions.php" ) ;
include ( "./basicFunctions.php" ) ;
include ( "./wikiTitle.php" ) ;
include ( "./wikiPage.php" ) ;
include ( "./wikiUser.php" ) ;
include ( "./wikiSettings.php" ) ;

## Language-dependant variables!

## Default/English:
$wikiTalk = "Talk";
$fieldSeparator = "\xb3" ;
$wikiSeeAlso = "See also" ;
$wikiConversionScript = "conversion script" ;
$wikiAutomatedConversion = "Automated conversion" ;
function recodeCharsetStub ( $text ) {
	# Temporarily, we are still using ISO-8859-1 in English
	return $text;
	}
function recodeCharsetLatin1 ( $text ) {
	# To convert ISO-8859-1 to UTF-8
	return utf8_encode ( $text ) ;
	}
	#$recodeCharset = recodeCharsetStub ;
	$recodeCharset = recodeCharsetLatin1 ; # Future conversions should all go to UTF-8

function firstIsLowercaseEn ( $text ) {
    $first = ord(substr($text, 0, 1));
    if ($first >= ord("a") && $first <= ord("z"))
	return true;
    else
	return false;
}
function firstIsLowercasePl ( $text ) {
    $first = ord(substr($text, 0, 1));
    if ($first > 128) {
	return in_array ($first, array (0xB1, 0xE6, 0xEA, 0xB3, 0xF1, 0xB6, 0xFE, 0xBF, 0xBC));
    } else if ($first >= ord("a") && $first <= ord("z")) {
	    return true;
    } else {
	    return false;    
    }
}
$firstIsLowercase = firstIsLowercaseEn;

## Esperanto:
if ( $wikiLanguage =="eo" ) {
	$wikiTalk = "Priparolu" ;
	$wikiSeeAlso = "Legu anka\xc5\xad" ;
	$wikiConversionScript = "konvertilo" ;
	$wikiAutomatedConversion = "A\xc5\xadtomata konvertado" ;
	$recodeCharset = wikiRecodeInputEo ;
	}

## French
if ( $wikiLanguage == "fr" ) {
	$wikiTalk = "Discuter" ;
	$recodeCharset = recodeCharsetLatin1 ;
	}

## Polish:
if ( $wikiLanguage == "pl" ) {
$wikiTalk = "Dyskusja" ;
$fieldSeparator = "\xff";
function RecodeCharsetPl ( $text ) {
	# Convert iso8859-2 to UTF-8
	# In a happy world, we could use iconv for this
	$l2u8 = array ( "\xA0" => "\xc2\xa0", "\xA1" => "\xc4\x84", "\xA2" => "\xcb\x98", "\xA3" => "\xc5\x81", "\xA4" => "\xc2\xa4", "\xA5" => "\xc4\xbd",
	"\xA6" => "\xc5\x9a", "\xA7" => "\xc2\xa7", "\xA8" => "\xc2\xa8", "\xA9" => "\xc5\xa0", "\xAA" => "\xc5\x9e", "\xAB" => "\xc5\xa4",
	"\xAC" => "\xc5\xb9", "\xAD" => "\xc2\xad", "\xAE" => "\xc5\xbd", "\xAF" => "\xc5\xbb", "\xB0" => "\xc2\xb0", "\xB1" => "\xc4\x85",
	"\xB2" => "\xcb\x9b", "\xB3" => "\xc5\x82", "\xB4" => "\xc2\xb4", "\xB5" => "\xc4\xbe", "\xB6" => "\xc5\x9b", "\xB7" => "\xcb\x87",
	"\xB8" => "\xc2\xb8", "\xB9" => "\xc5\xa1", "\xBA" => "\xc5\x9f", "\xBB" => "\xc5\xa5", "\xBC" => "\xc5\xba", "\xBD" => "\xcb\x9d",
	"\xBE" => "\xc5\xbe", "\xBF" => "\xc5\xbc", "\xC0" => "\xc5\x94", "\xC1" => "\xc3\x81", "\xC2" => "\xc3\x82", "\xC3" => "\xc4\x82", 
	"\xC4" => "\xc3\x84", "\xC5" => "\xc4\xb9", "\xC6" => "\xc4\x86", "\xC7" => "\xc3\x87", "\xC8" => "\xc4\x8c", "\xC9" => "\xc3\x89", 
	"\xCA" => "\xc4\x98", "\xCB" => "\xc3\x8b", "\xCC" => "\xc4\x9a", "\xCD" => "\xc3\x8d", "\xCE" => "\xc3\x8e", "\xCF" => "\xc4\x8e", 
	"\xD0" => "\xc4\x90", "\xD1" => "\xc5\x83", "\xD2" => "\xc5\x87", "\xD3" => "\xc3\x93", "\xD4" => "\xc3\x94", "\xD5" => "\xc5\x90", 
	"\xD6" => "\xc3\x96", "\xD7" => "\xc3\x97", "\xD8" => "\xc5\x98", "\xD9" => "\xc5\xae", "\xDA" => "\xc3\x9a", "\xDB" => "\xc5\xb0", 
	"\xDC" => "\xc3\x9c", "\xDD" => "\xc3\x9d", "\xDE" => "\xc5\xa2", "\xDF" => "\xc3\x9f", "\xE0" => "\xc5\x95", "\xE1" => "\xc3\xa1", 
	"\xE2" => "\xc3\xa2", "\xE3" => "\xc4\x83", "\xE4" => "\xc3\xa4", "\xE5" => "\xc4\xba", "\xE6" => "\xc4\x87", "\xE7" => "\xc3\xa7", 
	"\xE8" => "\xc4\x8d", "\xE9" => "\xc3\xa9", "\xEA" => "\xc4\x99", "\xEB" => "\xc3\xab", "\xEC" => "\xc4\x9b", "\xED" => "\xc3\xad", 
	"\xEE" => "\xc3\xae", "\xEF" => "\xc4\x8f", "\xF0" => "\xc4\x91", "\xF1" => "\xc5\x84", "\xF2" => "\xc5\x88", "\xF3" => "\xc3\xb3", 
	"\xF4" => "\xc3\xb4", "\xF5" => "\xc5\x91", "\xF6" => "\xc3\xb6", "\xF7" => "\xc3\xb7", "\xF8" => "\xc5\x99", "\xF9" => "\xc5\xaf", 
	"\xFA" => "\xc3\xba", "\xFB" => "\xc5\xb1", "\xFC" => "\xc3\xbc", "\xFD" => "\xc3\xbd", "\xFE" => "\xc5\xa3", "\xFF" => "\xcb\x99" ) ;
	return strtr ( $text , $l2u8 ) ;
	}
	$recodeCharset = recodeCharsetPl ;
	$firstIsLowercase = firstIsLowercasePl;
}

## Spanish
if ( $wikiLanguage == "es" ) {
	$recodeCharset = recodeCharsetLatin1 ;
	$wikiTalk = $recodeCharset ( "Discusión" ) ;
	}

## German
if ( $wikiLanguage == "de" ) {
	$recodeCharset = recodeCharsetLatin1 ;
	$wikiTalk = "Diskussion" ;
	}

## Dutch
if ( $wikiLanguage == "nl" ) {
	$recodeCharset = recodeCharsetLatin1 ;
	$wikiTalk = "Overleg" ;
	}

## Japanese
if ( $wikiLanguage == "ja" ) {
	# Will need iconv support compiled into PHP
	}

## Where to get the old usemod database files from:
#$rootDir = "/home/groups/w/wi/wikipedia/htdocs/fpw/wiki-de/lib-http/db/wiki/page/" ;
#$rootDir = "/home/manske/wiki/lib-http/db/wiki/page/" ;
$rootDir = "/stuff/wiki/lib-http/db/wiki/page/" ;
#$rootDir = "/tmp/home/wiki-pl/wiki/db/page/" ;
$rootDir = "/tmp/home/wiki-eo/lib-http/db/wiki/page/" ;

$oldid = 100; # Need unique identifiers for page histories; increment each time we use it.

	$FS = $fieldSeparator ;
	$FS1 = $FS."1" ;
	$FS2 = $FS."2" ;
	$FS3 = $FS."3" ;

function scanText2 ( $fn ) {
	global $fieldSeparator , $FS , $FS1 , $FS2 , $FS3 ;
	$ret = "" ;

	#CONSTANTS
	#$FS = "³" ;

	#READING FILE
	$t = array () ;
	$fd = fopen ( $fn , "r" ) ;
	if ( $fd == false ) return "There was an error converting this file : file not found." ;
	while (!feof($fd)) {
		$buffer = fgets($fd, 99999);
		array_push ( $t , $buffer ) ;
		}
	fclose ( $fd ) ;

	#array_pop ( $t ) ;
	$t = implode ( "" , $t ) ;
/*
	#SPLIT PAGE
	$sp = explode ( $FS1 , $t ) ;

	$x = array_pop ( $sp ) ;
	$sections = explode ( $FS2 , $x ) ;
	foreach ( $sections as $y ) {
		$text = explode ( $FS3 , $y ) ;
		foreach ( $text as $z ) {
			if ( $ret == "text" ) return $z ;
			$ret = $z ;
			}
		}

	return $ret ;
*/
	$page = splitHash ( $FS1 , $t ) ;
	$section = splitHash ( $FS2 , $page["text_default"] ) ;
	$text = splitHash ( $FS3 , $section["data"] ) ; # text_default
	
	return array ( "text" => $text["text"] , "summary" => $text["summary"] ,
		"minor" => $text["minor"] , "ts" => $section["ts"] ,
		"username" => $section["username"] , "host" => $section["host"] ) ;
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
	global $wikiTalk , $wikiSeeAlso ;

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
	$s = ereg_replace ( "([\n ])/([A-Z][a-zA-Z0-9]+)" , "\\1[[/\\2|/\\2]]" , $s ) ;

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
	if ( $backLink != "" ) $backLink = "\n:''$wikiSeeAlso :'' [[$backLink]]" ;
	return substr ( $s , 1 ).$backLink ;
	}

function convertText ( $s ) {
	global $recodeCharset ;
	/*
	$s = str_replace ( "\\'" , "'" , $s ) ;
	$s = str_replace ( "\\\"" , "\"" , $s ) ;
	$s = str_replace ( "\"" , "\\\"" , $s ) ;
	$s = str_replace ( "'" , "\\'" , $s ) ;
	*/
	$s = strtr ( $recodeCharset ( $s ) ,
		array ( "\\" => "\\\\" , "\"" => "\\\"" , "'" => "\\'" ) ) ;

	$a = spliti ( "<nowiki>" , $s ) ;
	$s = fixLinks ( array_shift ( $a ) ) ;
	foreach ( $a as $x ) {
		$b = spliti ( "</nowiki>" , $x , 2 ) ;
		if ( count ( $b ) == 1 ) $s .= "<nowiki>".$x ;
		else $s .= "<nowiki>".$b[0]."</nowiki>".fixLinks($b[1]);
		}

	return $s ;
	}

function splitHash ( $sep , $str ) {
	$temp = explode ( $sep , $str ) ;
	$ret = array () ;
	for ( $i = 0; $i+1 < count ( $temp ) ; $i++ ) {
		$ret[$temp[$i]] = $temp[++$i] ;
		}
	return $ret ;
	}


function makeSafe ( $str ) {
	# Escape strings and convert character set so we can insert text into the db
	global $recodeCharset ;
	return strtr ( $recodeCharset ( $str ) ,
		array ( "\\" => "\\\\" , "\"" => "\\\"" , "'" => "\\'" ) ) ;
	}

function getHistory ( $title , $st) {
	global $fieldSeparator , $FS , $FS1 , $FS2 , $FS3 , $oldid , $recodeCharset ;
	$f = preg_replace ( "/\/page\/(.*)\.db$/" , "/keep/\$1.kp" , getFileName ( $title ) ) ;
	if ( !file_exists ( $f ) ) return "" ;
	
	$fc = file ( $f ) ;
	$keptlist = explode ( $FS1 , implode ( "\n" , $fc ) ) ;
	array_shift ( $keptlist ) ;

	$lastoldid = "0"; $sql = array () ;
	
	foreach ( $keptlist as $rev ) {
		$section = splitHash ( $FS2 , $rev ) ;
		$text = splitHash ( $FS3 , $section["data"] ) ;
		
		$user = makeSafe ( $section["username"] ? $section["username"] : $section["host"] ) ;
		
		if ( $text["text"] && $text["minor"] != "" && ( $section["ts"]*1 > 0 ) ) {
			array_push ( $sql , "INSERT INTO old (old_title,old_text,old_comment,old_user,old_user_text,old_old_version,old_timestamp,old_minor_edit) "
			. "VALUES (\"$st\",\"" . makeSafe($text["text"]) . "\",\"" . makeSafe($text["summary"]) . "\","
			. "0,\"$user\",$lastoldid,FROM_UNIXTIME(" . $section["ts"] . ")," . $text["minor"] . ")" ) ;
			
			$lastoldid = "LAST_INSERT_ID()" ;
		} else {
			echo " (note: skipped a bad old revision)";
			}
		
		}
	return $sql ;
	}

function storeInDB ( $title , $text ) {
	global $of , $npage , $ll , $ull , $wikiTalk , $recodeCharset , $oldid , $wikiAutomatedConversion , $wikiConversionScript ;
	global $historyOnly , $noHistory , $connection ;
	$ll = array () ;
	$ull = array () ;
	$title = str_replace ( "\\'" , "'" , $title ) ;
	$title = str_replace ( "\\\"" , "\"" , $title ) ;
	$npage = new wikiPage ;
	$npage->title = $recodeCharset ( $title ) ;
	$npage->makeAll () ;
	$thetext = convertText ( $text["text"] ) ;
	#$ll1 = implode ( "\n" , $ll ) ;
	#$ull1 = implode ( "\n" , $ull ) ;
	$st = $npage->secureTitle ;

	# Move talk pages to talk namespace
	$talk = explode ( "/" , $st ) ;
	if ( $talk[0] == "HomePage" ) { $talk[0] = "Main_Page" ; $st = $talk[0] ; }
	if ( count ( $talk ) == 2 and $talk[1] == $wikiTalk ) $st = $wikiTalk.":".$talk[0] ;
	if ( count ( $talk ) == 2 and $talk[1] == strtolower($wikiTalk) ) return ;

	$lastoldid = "0"; $sql = array () ;
if ( ! $noHistory ) { # for testing { ****
	$sql = getHistory ( $title , $st ) ;
	if ( ! $sql ) $sql = array () ;
	if (count($sql)) $lastoldid = "LAST_INSERT_ID()" ;
	
	# Insert untouched version as the last in the history chain
	$user = makeSafe ( $text["username"] ? $text["username"] : $text["host"] ) ;
	if ( $text["text"] && $text["minor"] != "" && ( $text["ts"]*1 > 0 ) ) {
		array_push ( $sql , "INSERT INTO old (old_title,old_text,old_comment,old_user,old_user_text,old_old_version,old_timestamp,old_minor_edit) "
		. "VALUES (\"$st\",\"" . makeSafe($text["text"]) . "\",\"" . makeSafe($text["summary"]) . "\","
		. "0,\"$user\",$lastoldid,FROM_UNIXTIME(" . $text["ts"] . ")," . $text["minor"] . ")" );
		$lastoldid = "LAST_INSERT_ID()" ;
	} else {
		echo " (skipping last old revision - $text[ts] $text[minor] $text[text])";
		}
} # for testing **** }

	if ( $historyOnly ) {
		$sqlx = "SELECT cur_old_version FROM cur WHERE cur_title=\"$st\"" ;
		$result = mysql_query ( $sqlx , $connection ) ;
		echo mysql_error () ;
		if ( $s = mysql_fetch_object ( $result ) ) {
			$oid = $s->cur_old_version ;
			mysql_free_result ( $result ) ;
		} else $oid = 0 ;
		
		if ( $oid )
			array_push ( $sql , "UPDATE old SET old_old_version=LAST_INSERT_ID(),old_timestamp=old_timestamp WHERE old_old_version=0 AND old_title=\"$st\"" ) ;
		else
			array_push ( $sql , "UPDATE cur SET cur_old_version=LAST_INSERT_ID(),cur_timestamp=cur_timestamp WHERE cur_title=\"$st\"" ) ;

		foreach ( $sql as $line ) {
			mysql_query ( $line , $connection ) ;
			echo mysql_error ();
			}
	} else {
	$sql = implode ( ";\n" , $sql ) ;
	$sql .= ";\nINSERT INTO cur (cur_title,cur_ind_title,cur_text,cur_comment,cur_user,cur_user_text,cur_old_version,cur_minor_edit) VALUES ";
	$sql .= "(\"$st\",\"$st\",\"$thetext\",";
	$sql .= "\"$wikiAutomatedConversion\",0,\"$wikiConversionScript\",$lastoldid,1);\n" ;
	foreach ( $ll as $l ) {
		$sql .= "INSERT INTO linked (linked_from,linked_to) VALUES (\"$st\",\"$l\");\n";
		}
	foreach ( $ull as $l ) {
		$sql .= "INSERT INTO unlinked (unlinked_from,unlinked_to) VALUES (\"$st\",\"$l\");\n";
		}
	fwrite ( $of , $sql ) ;
	}
	}

function getTopics ( $dir ) {
	global $firstIsLowercase;
	$ret = array () ;
	
	$mydir = opendir($dir);
	while ($entry = readdir($mydir)) {
		if ($entry != '.' && $entry != '..') {
			if ( is_dir ( "$dir/$entry" ) ) {
				$a = getTopics ( "$dir/$entry" ) ;
				foreach ( $a as $x ) array_push ( $ret , "$entry/$x" ) ;
			} else {
				if ( $firstIsLowercase ( $entry ) ) {
				    print "Warning: File <b>\"$entry\"</b> starts with lower case letter, <b>ignored</b><br>\n" ;
				} else if (substr ($entry, strlen ( $entry ) - 3, 3) == '.db') {
				    $x = substr ( $entry , 0 , strlen ( $entry ) - 3 ) ;
				    array_push ( $ret , $x ) ;
				} else {
				    print "Warning: File <b>\"$entry\"</b> doesn't seem to contain an article, <b>ignored</b><br>\n" ;
				}
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
	global $recodeCharset ;
	$allTopics = array () ;
	for ( $c = 65 ; chr($c) <= "Z" ; $c++ ) {
		$allTopics[chr($c)] = getTopics ( "$rootDir/".chr($c) ) ;
		for ( $i = 0; $i < count($allTopics[chr($c)]) ; $i++ )
			$allTopics[chr($c)][$i] = $recodeCharset ( $allTopics[chr($c)][$i] ) ;
		}
	$allTopics["0"] = getTopics ( "$rootDir/other" ) ;
	for ( $i = 0; $i < count($allTopics["0"]) ; $i++ )
		$allTopics["0"][$i] = $recodeCharset ( $allTopics["0"][$i] ) ;
	}

# MAIN PROGRAM
	global $rootDir , $historyOnly , $connection ;

	$historyOnly = in_array ( "--history" , $argv ) ; # To import only old histories into database
	$noHistory = in_array ( "--nohistory" , $argv ) ; # For testing

	set_time_limit ( 0 ) ; # Enough time for this script...

	global $ll , $ull , $allTopics ;
	$ll = array () ;
	$ull = array () ;
	getAllTopics () ;

	global $l , $of ;
	if ( ! $historyOnly ) {
		$of = fopen ( "./newiki.sql" , "w" ) ;
		$nw = file ( "./wikipedia.sql" ) ;
		foreach ( $nw as $line ) fwrite ( $of , $line ) ;
		fwrite ( $of , "DELETE FROM cur WHERE cur_title NOT LIKE \"%:%\";\n" ) ;
		fwrite ( $of , "DELETE FROM cur WHERE cur_title LIKE \"$wikiTalk:%\";\n" ) ;
	} else {
		# We need a live database to check against to import the histories
		//establish user connection
		$connection = mysql_pconnect($wikiThisDBserver , $wikiThisDBuser , $wikiThisDBpassword )
			or die("Could not get connection to database server.") ;
		//open up database
		mysql_select_db ($wikiSQLServer , $connection)
			or die("Could not select database: $wikiSQLServer");
		}
	do {
		if ( !isset ( $l ) ) $l = 65 ;
		if ( $l == "other" ) $letter = "other" ;
		else $letter = chr ( $l ) ;
		$nl = $l+1 ;
		if ( $letter == "Z" ) $nl = "other" ;
		$l = $nl ;
		dir2DB ( $letter ) ;
	} while ( $letter != "other" ) ;
	if (isset ( $of )) fclose ( $of ) ;

	print "FINISHED!\n" ;
?>
