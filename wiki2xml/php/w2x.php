<?
# Copyright by Magnus Manske (2005)
# Released under GPL

include ( "wiki2xml.php" ) ;
include ( "local.php" ) ;

class ContentProvider {
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
		$s = file_get_contents ( $url ) ;
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

## TIMER FUNCTION

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
} 

## MAIN PROGRAM

if ( isset ( $_POST['doit'] ) ) {
	$wikitext = stripslashes ( $_POST['text'] ) ;
	
	$content_provider = new ContentProvider ;
	$xmlg["site_base_url"] = $_POST['site'] ;
	$xmlg["namespace_template"] = $_POST['template'] ;

	header('Content-type: text/xml; charset=utf-8');
	print "<?xml version='1.0' encoding='UTF-8' ?>\n" ;

	$t = microtime_float() ;
	$text = "" ;
	$article_open = '<article>' ;
	if ( $_POST['whatsthis'] == "wikitext" ) {
		$p = new wiki2xml ;
		$text = $article_open . $p->parse ( $wikitext ) . "</article>" ;
	} else {
		$t = microtime_float() ;
		$articles = explode ( "\n" , $wikitext ) ;
		foreach ( $articles AS $a ) {
			$p = new wiki2xml ;
			$wikitext = $content_provider->get_wiki_text ( $a ) ;
			$text .= $article_open . $p->parse ( $wikitext ) . "</article>" ;
		}
	}	
	$t = microtime_float() - $t ;
# xmlns:xhtml=\"http://www.w3.org/1999/xhtml\"
	print "<articles xmlns:xhtml=\" \" rendertime='{$t} sec'>{$text}</articles>" ;
} else if ( isset ( $_GET['showsource'] ) ) {
	header('Content-type: text/plain; charset=utf-8');
	print file_get_contents ( "wiki2xml.php" ) ;
} else {
	header('Content-type: text/html; charset=utf-8');
	print "
<html><head></head><body><form method='post'>
<h1>Magnus' magic wiki-to-XML converter</h1>
<p>All written in PHP - so portable, so incredibly slow... (see <a href=\"wiki2xml.php?showsource=true\">the source</a>)</p>
<p>
Known bugs:
<ul>
<li>The \";\" markup doesn't work yet (not implemented); \":\" does, though</li>
</ul>
</p>
<h2>Paste wikitext here</h2>
<textarea rows='20' cols='80' style='width:100%' name='text'></textarea><br/>
This is
<INPUT type='radio' name='whatsthis' value='wikitext'>raw wikitext 
<INPUT checked type='radio' name='whatsthis' value='articlelist'>a list of articles
<br/>
Site : http://<input type='text' name='site' value='".$xmlg["site_base_url"]."'/>/index.php<br/>
Template namespace name : <input type='text' name='template' value='".$xmlg["namespace_template"]."'/><br/>
<input type='submit' name='doit' value='Convert'/>
</form></body></html>" ;
}

?>