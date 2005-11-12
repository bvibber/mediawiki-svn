<?
# Copyright by Magnus Manske (2005)
# Released under GPL

include ( "wiki2xml.php" ) ;
include ( "default.php" ) ; # Which will include local.php, if available
include ( "content_provider.php" ) ;

set_time_limit ( 0 ) ; # No time limit

## TIMER FUNCTION

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
} 

## MAIN PROGRAM

if ( isset ( $_POST['doit'] ) ) {
	$wikitext = stripslashes ( $_POST['text'] ) ;
	
	$content_provider = new ContentProviderHTTP ;
	$xmlg["site_base_url"] = $_POST['site'] ;
#	$xmlg["namespace_template"] = $_POST['template'] ;

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
			$a = trim ( $a ) ;
			if ( $a == "" ) continue ;
			$p = new wiki2xml ;
			if ( !isset ( $_POST['resolvetemplates'] ) ) $p->auto_fill_templates = false ;
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
<p>All written in PHP - so portable, so incredibly slow...</p>
<p>
Known issues:
<ul>
<li>In templates, {{{variables}}} used within &lt;nowiki&gt; tags will be replaced as well (too lazy to strip them)</li>
<li>HTML comments are removed (instead of converted into XML tags)</li>
</ul>
</p>
<h2>Paste wikitext here</h2>
<textarea rows='20' cols='80' style='width:100%' name='text'></textarea><br/>
This is
<INPUT type='radio' name='whatsthis' value='wikitext'>raw wikitext 
<INPUT checked type='radio' name='whatsthis' value='articlelist'>a list of articles
<br/>
Site : http://<input type='text' name='site' value='".$xmlg["site_base_url"]."'/>/index.php<br/>
<input type='checkbox' name='resolvetemplates' value='1' checked>Automatically resolve templates</input><br/>
<input type='submit' name='doit' value='Convert'/>
</form></body></html>" ;
}

?>