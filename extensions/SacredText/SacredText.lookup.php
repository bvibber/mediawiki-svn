<?php
class SacredTextLookup {

	function parseInput( $input, &$book, &$chapternum, &$versenums ) {
	    if( preg_match( "/^\s*([\s\w]*\w+)\s*(\d+):(\d+)/", $input, $matches) ) {
	        $book = $matches[1];
	        $chapternum = $matches[2];
	        $versenums = array();
	        $versenums[] = $matches[3];
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
        	return htmlspecialchars( "Could not find: ". $book ." ".$chapternum.":".$versenums[0]." in the ". $religtext );
    	}
	}

}
