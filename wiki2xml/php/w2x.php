<?php
# Copyright by Magnus Manske (2005)
# Released under GPL

include_once ( "default.php" ) ; # Which will include local.php, if available
require_once ( "mediawiki_converter.php" ) ;

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
	$converter = new MediaWikiConverter ;
	
	$xmlg["site_base_url"] = $_POST['site'] ;
	$xmlg["resolvetemplates"] = isset ( $_POST['resolvetemplates'] ) ;
	
	$t = microtime_float() ;
	$xml = "" ;
	if ( $_POST['whatsthis'] == "wikitext" ) {
		$xml = $converter->article2xml ( "" , $wikitext , $xmlg ) ;
	} else {
		$t = microtime_float() ;
		$articles = explode ( "\n" , $wikitext ) ;
		foreach ( $articles AS $a ) {
			$a = trim ( $a ) ;
			if ( $a == "" ) continue ;
			$wikitext = $content_provider->get_wiki_text ( $a ) ;
			$xml .= $converter->article2xml ( $a , $wikitext , $xmlg ) ;
		}
	}
	$t = microtime_float() - $t ;
	$tt = $t ;
	$lt = $content_provider->load_time ;
	$t -= $lt ;
	
	$xml = "<articles xmlns:xhtml=\" \" loadtime='{$lt} sec' rendertime='{$t} sec' totaltime='{$tt} sec'>\n{$xml}\n</articles>" ;
	
	# Output format
	$format = $_POST['output_format'] ;
	if ( $format == "xml" ) {
		header('Content-type: text/xml; charset=utf-8');
		print "<?xml version='1.0' encoding='UTF-8' ?>\n" ;
		print $xml ;
	} else if ( $format == "text" ) {
		$xmlg['plaintext_markup'] = isset ( $_POST['plaintext_markup'] ) ;
		$xmlg['plaintext_prelink'] = isset ( $_POST['plaintext_prelink'] )  ;
		$out = $converter->articles2text ( $xml , $xmlg ) ;
		$out = str_replace ( "\n" , "<br/>" , $out ) ;
		header('Content-type: text/html; charset=utf-8');
		print $out ;
	} else if ( $format == "docbook_xml" ) {
		$out = $converter->articles2docbook_xml ( $xml , $xmlg ) ;
		header('Content-type: text/xml; charset=utf-8');
		print $out ;
	} else if ( $format == "docbook_pdf" ) {
		$filename = $converter->articles2docbook_pdf ( $xml , $xmlg ) ;
		$fp = fopen($filename, 'rb');
		header('Content-type: application/pdf');
		header("Content-Length: " . filesize($filename));
		fpassthru($fp);
		fclose ( $fp ) ;
		
		# Cleanup
		$pdf_dir = dirname ( dirname ( $filename ) ) ;
		SureRemoveDir ( $pdf_dir ) ;
		@rmdir ( $pdf_dir ) ;
	}
	
} else { # Show the form
	header('Content-type: text/html; charset=utf-8');
	
	$optional = array () ;
	if ( isset ( $xmlg['docbook']['command_pdf'] ) ) {
		$optional[] = "<INPUT type='radio' name='output_format' value='docbook_pdf'>DocBook PDF" ;
	}
	$optional = "<br/>" . implode ( "<br/>" , $optional ) ;
	
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
<table border='0'><tr>
<td>
This is
<INPUT type='radio' name='whatsthis' value='wikitext'>raw wikitext 
<INPUT checked type='radio' name='whatsthis' value='articlelist'>a list of articles
<br/>

Site : http://<input type='text' name='site' value='".$xmlg["site_base_url"]."'/>/index.php<br/>
<input type='checkbox' name='resolvetemplates' value='1' checked>Automatically resolve templates</input><br/>
<br/><input type='submit' name='doit' value='Convert'/>
</td><td valign='top'>
<b>Output</b>
<br/><INPUT checked type='radio' name='output_format' value='xml'>XML 
<br/><INPUT type='radio' name='output_format' value='text'>Plain text 
 <input type='checkbox' name='plaintext_markup' value='1' checked>Use *_/ markup</input>
 <input type='checkbox' name='plaintext_prelink' value='1' checked>Put &rarr; before internal links</input>
<br/><INPUT type='radio' name='output_format' value='docbook_xml'>DocBook XML 
{$optional}
</form></body></html>" ;
}

?>
