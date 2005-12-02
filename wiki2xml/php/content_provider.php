<?php

# Abstract base class
class ContentProvider {
	function get_wiki_text ( $title , $do_cache = false ) {} # dummy
	function get_template_text ( $title ) {} # dummy
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
				
		$s = @file_get_contents ( $url ) ;
		if ( $do_cache ) $this->article_cache[$title] = $s ;
		return $s ;
	}
	
	function get_template_text ( $title ) {
		global $xmlg ;
		
		# Check for fix variables
		if ( $title == "PAGENAME" ) return $this->first_title ;
		if ( $title == "PAGENAMEE" ) return urlencode ( $this->first_title ) ;
		
		$title = trim ( $title ) ;
		if ( count ( explode ( ":" , $title , 2 ) ) == 1 ) # Does the template title contain a ":"?
			$title = $xmlg["namespace_template"] . ":" . $title ;
		else if ( substr ( $title , 0 , 1 ) == ":" ) # Main namespace
			$title = substr ( $title , 1 ) ;
		return $this->get_wiki_text ( $title , true ) ; # Cache template texts
	}
}

?>
