<?
# Copyright by Magnus Manske (2005)
# Released under GPL

include ( "wiki2xml.php" ) ;
include ( "default.php" ) ; # Which will include local.php, if available
include ( "content_provider.php" ) ;

@set_time_limit ( 0 ) ; # No time limit
ini_set('user_agent','MSIE 4\.0b2;'); # Fake user agent

## TIMER FUNCTION

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
} 

## MAIN PROGRAM

if ( isset ( $_POST['doit'] ) ) { # Process
	$wikitext = stripslashes ( $_POST['text'] ) ;
	
	$content_provider = new ContentProviderHTTP ;
	$xmlg["site_base_url"] = $_POST['site'] ;

	$t = microtime_float() ;
	$xml = "" ;
	$article_open = '<article>' ;
	if ( $_POST['whatsthis'] == "wikitext" ) {
		$p = new wiki2xml ;
		$xml = $article_open . $p->parse ( $wikitext ) . "</article>" ;
	} else {
		$t = microtime_float() ;
		$articles = explode ( "\n" , $wikitext ) ;
		foreach ( $articles AS $a ) {
			$a = trim ( $a ) ;
			if ( $a == "" ) continue ;
			$p = new wiki2xml ;
			if ( !isset ( $_POST['resolvetemplates'] ) ) $p->auto_fill_templates = false ;
			$wikitext = $content_provider->get_wiki_text ( $a ) ;
			$xml .= $article_open . $p->parse ( $wikitext ) . "</article>" ;
		}
	}	
	$t = microtime_float() - $t ;
	
	# Output format
	$format = $_POST['output_format'] ;
	if ( $format == "xml" ) {
		header('Content-type: text/xml; charset=utf-8');
		print "<?xml version='1.0' encoding='UTF-8' ?>\n" ;
		print "<articles xmlns:xhtml=\" \" rendertime='{$t} sec'>{$xml}</articles>" ;
	} else if ( $format == "text" ) {
		require_once ( "./xml2txt.php" ) ;

		$x2t = new xml2php ;
		$tree = $x2t->scanString ( $xml ) ;
		if ( isset ( $_POST['plaintext_markup'] ) ) {
			$tree->bold = '*' ;
			$tree->italics = '/' ;
			$tree->underline = '_' ;
		}
		if ( isset ( $_POST['plaintext_prelink'] ) ) {
			$tree->pre_link = "&rarr;" ;
		}
		$out = trim ( $tree->parse ( $tree ) ) ;

		$out = str_replace ( "\n" , "<br/>" , $out ) ;
		header('Content-type: text/html; charset=utf-8');
		print $out ;
	}
	
} else { # Show the form
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

Output : 
<INPUT checked type='radio' name='output_format' value='xml'>XML 
<INPUT type='radio' name='output_format' value='text'>Plain text 

<br/>Plain text :
 <input type='checkbox' name='plaintext_markup' value='1' checked>Use *_/ markup</input>
 <input type='checkbox' name='plaintext_prelink' value='1' checked>Put &rarr; before internal links</input>

<br/><input type='submit' name='doit' value='Convert'/>
</form></body></html>" ;
}

?>
