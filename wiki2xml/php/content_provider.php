<?php

# Abstract base class
class ContentProvider {
	var $load_time = 0 ; # Time to load text and templates, to judge actual parsing speed
	var $article_list = array () ;
	
	function get_wiki_text ( $title , $do_cache = false ) {} # dummy
	function get_template_text ( $title ) {} # dummy
	
	function add_article ( $title ) {
		$this->article_list[] = urlencode ( trim ( $title ) ) ;
	}
	
	function is_an_article ( $title ) {
		$title = urlencode ( trim ( $title ) ) ;
		return in_array ( $title , $this->article_list ) ;
	}

	/**
	 * Gets the numeric namespace
	 * "-8" = category link
	 * "-9" = interlanguage link
	 */
	function get_namespace_id ( $text ) {
		$text = strtoupper ( $text ) ;
		$text = explode ( ":" , $text , 2 ) ;
		if ( count ( $text ) != 2 ) return 0 ;
		$text = trim ( array_shift ( $text ) ) ;
		if ( $text == "" ) return 0 ;		
		$ns = 0 ;
		
		if ( $text == "CATEGORY" || $text == "KATEGORIE" ) return -8 ; # Hackish, for category link
		if ( strlen ( $text ) < 4 ) return -9 ; # Hackish, for interlanguage link
		
		# Horrible manual hack, for now
		if ( $text == "IMAGE" || $text == "BILD" ) $ns = 6 ;
		
		return $ns ;
	}

	function copyimagefromwiki ( $lang , $imagename , $localtarget = "" )
		{
		if ( $localtarget == "" ) $localtarget = $imagename ;
		$i = $imagename ;
		$i = utf8_encode ( $i ) ;
		$i = str_replace ( " " , "_" , $i ) ;
		print $i . " : " ;
		$m = md5 ( $i ) ;
		print $m ;
		$i = substr ( $m , 0 , 1 ) . "/" . substr ( $m , 0 , 2 ) . "/" . urlencode ( $i ) ;
		$fn1 = "http://upload.wikimedia.org/wikipedia/{$lang}/{$i}" ;
		$fn2 = "http://upload.wikimedia.org/wikipedia/commons/{$i}" ;
		print "<br/>" ;
		if ( @copy ( $fn1 , $localtarget ) ) return true ; # Trying language
		if ( @copy ( $fn2 , $localtarget ) ) return true ; # Trying commons
		return false ; # No such image
		}
		
	
	function get_image_url ( $name ) {
		global $xmlg ;
		$site = $xmlg['site_base_url'] ;
		$parts = explode ( ".wikipedia.org/" , $site ) ;
		$i = utf8_encode ( $name ) ;
		$i = str_replace ( " " , "_" , $i ) ;
		$m = md5 ( $i ) ;
		$i = substr ( $m , 0 , 1 ) . "/" . substr ( $m , 0 , 2 ) . "/" . urlencode ( $i ) ;
		if ( count ($parts ) > 1 ) {
			$lang = array_shift ( $parts ) ;
			$url = "http://upload.wikimedia.org/wikipedia/{$lang}/{$i}" ;
			$url2 = "http://upload.wikimedia.org/wikipedia/commons/{$i}" ;
			$h = @fopen ( $url , "r" ) ;
			if ( $h === false ) $url = $url2 ;
			else fclose ( $h ) ;
#			if ( !file_exists ( $url ) ) $url = $url2 ;
		} else {
			$url = "http://{$site}/images/{$i}" ;
		}
		return $url ;
	}
	
}


# Access through HTTP protocol
class ContentProviderHTTP extends ContentProvider {
	var $article_cache = array () ;
	var $first_title = "" ;
	
	function get_wiki_text ( $title , $do_cache = false ) {
		global $xmlg ;
		$title = trim ( $title ) ;
		if ( $title == "" ) return "" ; # Just in case...
		if ( isset ( $this->article_cache[$title] ) ) # Already in the cache
			return $this->article_cache[$title] ;
		
		if ( $this->first_title == "" ) $this->first_title = $title ;
		
		# Retrieve it
		$url = "http://" . $xmlg["site_base_url"] . "/index.php?action=raw&title=" . urlencode ( $title ) ;
		
		$t1 = microtime_float() ;
		$s = @file_get_contents ( $url ) ;
		if ( strtoupper ( substr ( $s , 0 , 9 ) ) == "#REDIRECT" ) {
			$t2 = explode ( "[[" , $s , 2 ) ;
			$t2 = array_pop ( $t2 ) ;
			$t2 = explode ( "]]" , $t2 , 2 ) ;
			$t2 = array_shift ( $t2 ) ;
			$url = "http://" . $xmlg["site_base_url"] . "/index.php?action=raw&title=" . urlencode ( $t2 ) ;
			$s = @file_get_contents ( $url ) ;
		}
		$this->load_time += microtime_float() - $t1 ;
		
		$comp = '<!DOCTYPE html PUBLIC "-//W3C//DTD' ;
		if ( substr ( $s , 0 , strlen ( $comp ) ) == $comp ) $s = "" ; # Catching wrong title error
		
		if ( $do_cache ) $this->article_cache[$title] = $s ;
		return $s ;
	}
	
	function get_template_text ( $title ) {
		global $xmlg ;
		
		# Check for fix variables
		if ( $title == "PAGENAME" ) return $this->first_title ;
		if ( $title == "PAGENAMEE" ) return urlencode ( $this->first_title ) ;
		if ( $title == "SERVER" ) return "http://" . array_shift ( explode ( "/" , $xmlg["site_base_url"] , 2 ) ) ;
		if ( strtolower ( substr ( $title , 0 , 9 ) ) == "localurl:" )
			return "/" . array_pop ( explode ( "/" , $xmlg["site_base_url"] , 2 ) ) . "/index.php?title=" . urlencode ( substr ( $title , 9 ) ) ;
		
		$title = trim ( $title ) ;
		if ( count ( explode ( ":" , $title , 2 ) ) == 1 ) # Does the template title contain a ":"?
			$title = $xmlg["namespace_template"] . ":" . $title ;
		else if ( substr ( $title , 0 , 1 ) == ":" ) # Main namespace
			$title = substr ( $title , 1 ) ;
		return $this->get_wiki_text ( $title , true ) ; # Cache template texts
	}
}

?>
