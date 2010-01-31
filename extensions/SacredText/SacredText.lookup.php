<?php
class SacredTextLookup {

	function parseInput( $input, &$book, &$chapternum, &$versenums ) {
	    if( preg_match( "/^\s*([\s\w]*\w+)\s*(\d+):(\d+)(?:-(\d+))?\s*$/", $input, $matches) ) {
	        $book = $matches[1];
	        $chapternum = $matches[2];
	        $versenums = array();
			if( count( $matches ) <= 4 ) {
	        	$versenums[] = $matches[3];
			}
			else for( $x = $matches[3]; $x <= $matches[4]; $x++ ) {
				$versenums[] = $x;
			}
				
	        return true;
	    } else {
	        return false;
	    }
	}

	function hookBible( $input, $args, &$parser, $frame ) {
	    if( self::parseInput( $input, $book, $chapternum, $versenums ) ) {
	        $lang = "en";
	        $ver = "kjv";
	        if( array_key_exists("lang", $args) ) $lang = $args["lang"];
	        if( array_key_exists("ver", $args) ) $ver = $args["ver"];
	        return htmlspecialchars( $input ) ." ". self::lookup( "Christian Bible", $book, $chapternum, $versenums, $lang, $ver );
	    } else {
	        return htmlspecialchars( $input . " Could not parse reference.  Please use the format 'Gen 1:10'." );
	    }
	}
	
	function hookSacredText( $input, $args, &$parser, $frame ) {
	    if( self::parseInput( $input, $book, $chapternum, $versenums ) ) {
	        $lang = "en";
	        $ver = "kjv";
	        $religtext = "Christian Bible";
	        if( array_key_exists("lang", $args) ) $lang = $args["lang"];
	        if( array_key_exists("ver", $args) ) $ver = $args["ver"];
	        if( array_key_exists("text", $args) ) $religtext = $args["text"];
	
	        return htmlspecialchars( $input ) ." ". self::lookup( $religtext, $book, $chapternum, $versenums, $lang, $ver );
	    } else {
	        return htmlspecialchars( $input . " Could not parse reference.  Please use the format 'Gen 1:10'." );
	    }
	}
	
	function lookup( $religtext, $book, $chapternum, $versenums, $lang, $ver ) 
	{
	    global $wgSacredChapterAlias;
	    $dbr = wfGetDB( DB_SLAVE );
	
	    if( array_key_exists($religtext, $wgSacredChapterAlias) &&
	        array_key_exists($book, $wgSacredChapterAlias[$religtext] ) )
	    {
	        $book = $wgSacredChapterAlias[$religtext][$book];
	    }
	
	    if( strcasecmp($religtext,"Christian Bible")==0 ) {
	        if( strcasecmp($ver,"AV")==0 ) {
	            $ver = "KJV";
	        }
	    }
	
	    $obj = $dbr->selectRow( "sacredtext_verses", array("st_text"),
	        array(
	            "st_religious_text" => $religtext,
	            "st_book"           => $book,
	            "st_chapter_num"    =>$chapternum,
	            "st_verse_num"      =>$versenums[0],
	            "st_translation"    =>$ver,
	            "st_language"       =>$lang
	        ) );
	    if( $obj ) {
	        return htmlspecialchars( $obj->st_text );
	    } else {
			$r = self::fallback( $religtext, $book, $chapternum, $versenums, $lang, $ver );
			if( $r ) return htmlspecialchars( $r );
			
        	return htmlspecialchars( "Could not find: ". $book ." ".$chapternum.":".$versenums[0]." in the ". $religtext );
    	}
	}

	/* This function makes it possible to look for the verse on other websites, if the requested verse 
		cannot be found in the database.
	*/
	function fallback( $religtext, $book, $chapternum, $versenums, $lang, $ver ) 
	{
		global $wgSacredFallbackServers;
	    if( !array_key_exists($religtext, $wgSacredFallbackServers) ) return false;
	    if( !array_key_exists($lang, $wgSacredFallbackServers[$religtext]) ) return false;
	   	if( !array_key_exists($ver, $wgSacredFallbackServers[$religtext][$lang]) ) return false;

		$url0 = $wgSacredFallbackServers[$religtext][$lang][$ver]["url"];
		$regex = $wgSacredFallbackServers[$religtext][$lang][$ver]["regex"];


		$url0 = str_replace(
			array('$(religtext)','$(lang)','$(ver)','$(book)','$(chapternum)'),
			array($religtext,$lang,$ver,$book,$chapternum),
			$url0);

		$ret = "";
		foreach( $versenums as $versenum )
		{
			$url = str_replace('$(versenum)',$versenum, $url0);

			$h = fopen($url,'r' );
        	$str='';
			$length = 8192;
        	while(!feof($h)) $str.=fread($h,$length);
        	fclose($h);
        	$num = preg_match_all( $regex, $str, $matches, PREG_PATTERN_ORDER );
        	if( $num ) {
            	$ret .= implode( " ", $matches[1] ) . " ";
        	}
			else {
				return sprintf("Failed to match pattern %s from results from %s.",$regex,$url);
			}
		}
		if( empty($ret) ) return "Failed to retrieve match from '$url'.";
		else return "$ret";
	}
}
