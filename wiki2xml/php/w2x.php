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
	
	$xmlg["book_title"] = $_POST['document_title'] ;
	$xmlg["site_base_url"] = $_POST['site'] ;
	$xmlg["resolvetemplates"] = $_POST['use_templates'] ;
	$xmlg['templates'] = explode ( "\n" , $_POST['templates'] ) ;
	$xmlg['add_gfdl'] = isset ( $_POST['add_gfdl'] ) ;
	
	$t = microtime_float() ;
	$xml = "" ;
	if ( $_POST['whatsthis'] == "wikitext" ) {
		$wiki2xml_authors = array () ;
		$xml = $converter->article2xml ( "" , $wikitext , $xmlg ) ;
	} else {
		$t = microtime_float() ;
		$articles = explode ( "\n" , $wikitext ) ;
		foreach ( $articles AS $a ) {
			$wiki2xml_authors = array () ;
			$a = trim ( $a ) ;
			if ( $a == "" ) continue ;
			$a = explode ( '|' , $a ) ;
			if ( count ( $a ) == 1 ) $a[] = $a[0] ;
			$title_page = trim ( array_shift ( $a ) ) ;
			$title_name = trim ( array_pop ( $a ) ) ;
			$wikitext = $content_provider->get_wiki_text ( $title_page ) ;
			add_authors ( $content_provider->authors ) ;
			$xml .= $converter->article2xml ( $title_name , $wikitext , $xmlg ) ;
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
	} else if ( $format == "odt" ) {
		$out = $converter->articles2odt ( $xml , $xmlg ) ;
		
		$dir = $xmlg['odt_template_dir'] ;
		$out_file = tempnam("/tmp", "ODT");
		
		# Create ODT structure
		$handle = fopen ( $dir . "/content.xml" , "w" ) ;
		fwrite ( $handle , $out ) ;
		fclose ( $handle ) ;

		
		$cmd = $xmlg['zip_odt'] ;
		$cmd = str_replace ( '$1' , escapeshellarg ( $out_file ) , $cmd ) ;
		$cmd = str_replace ( '$2' , escapeshellarg ( $dir . "/*" ) , $cmd ) ;
		@unlink ( $out_file ) ;
		if ( isset ( $xmlg["zip_odt_path"] ) ) # Windows strange bug workaround
			chdir ( $xmlg["zip_odt_path"] ) ;
		exec ( $cmd ) ;
		
		if ( 1 ) { # Return ODT file
			$filename = $xmlg["book_title"] ;
			header('Content-type: application/vnd.oasis.opendocument.text; charset=utf-8');
			header('Content-Disposition: inline; filename="'.$filename.'"');
			$handle = fopen($out_file, 'rb');
			fpassthru ( $handle ) ;
			fclose ( $handle ) ;
			@unlink ( $out_file ) ;
		} else { # Return XML
			header('Content-type: text/xml; charset=utf-8');
			print str_replace ( ">" , ">\n" , $out ) ;
		}
	} else if ( $format == "docbook_xml" ) {
		$out = $converter->articles2docbook_xml ( $xml , $xmlg ) ;
		header('Content-type: text/xml; charset=utf-8');
		print $out ;
	} else if ( $format == "docbook_pdf" || $format == "docbook_html" ) {
		$filetype = substr ( $format , 8 ) ;
		$filename = $converter->articles2docbook_pdf ( $xml , $xmlg , strtoupper ( $filetype ) ) ;
		
		if ( file_exists ( $filename ) ) {
			$fp = fopen($filename, 'rb');
			if ( $format == "docbook_pdf" ) {
				header('Content-type: application/pdf');
				header("Content-Length: " . filesize($filename));
			} else if ( $format == "docbook_pdf" ) {
				header('Content-type: text/html');
			}
			fpassthru($fp);
			fclose ( $fp ) ;
		}
		
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
	if ( isset ( $xmlg['docbook']['command_html'] ) ) {
		$optional[] = "<INPUT type='radio' name='output_format' value='docbook_html'>DocBook HTML" ;
	}
	if ( isset ( $xmlg['zip_odt'] ) ) {
		$optional[] = "<INPUT type='radio' name='output_format' value='odt'>OpenOffice ODT" ;
	}
	$optional = "<br/>" . implode ( "<br/>" , $optional ) ;
	
	print "
<html><head></head><body><form method='post'>
<h1>Magnus' magic MediaWiki-to-XML-to-stuff converter</h1>
<p>All written in PHP - so portable, <s>so incredibly slow...</s> <i>about as fast as the original MediaWiki parser!</i></p>
<h2>Paste article list or wikitext here</h2>
<table border='0' width='100%'><tr>
<td valign='top'><textarea rows='20' cols='80' style='width:100%' name='text'></textarea></td>
<td width='200px' valign='top' nowrap>
<INPUT checked type='radio' name='use_templates' value='all'>Use all templates<br/>
<INPUT type='radio' name='use_templates' value='none'>Do not use templates<br/>
<INPUT type='radio' name='use_templates' value='these'>Use these templates<br/>
<INPUT type='radio' name='use_templates' value='notthese'>Use all but these templates
<textarea rows='15' cols='30' style='width:100%' name='templates'></textarea>
</td></tr></table>
<table border='0'><tr>
<td valign='top'>
This is
<INPUT type='radio' name='whatsthis' value='wikitext'>raw wikitext 
<INPUT checked type='radio' name='whatsthis' value='articlelist'>a list of articles
<br/>

Site : http://<input type='text' name='site' value='".$xmlg["site_base_url"]."'/>/index.php<br/>
Title : <input type='text' name='document_title' value='' size=40/><br/>
<input type='checkbox' name='add_gfdl' value='1' checked>Include GFDL (for some output formats)</input><br/>
<input type='submit' name='doit' value='Convert'/>
</td><td valign='top' style='border-left:1px black solid'>
<b>Output</b>
<br/><INPUT checked type='radio' name='output_format' value='xml'>XML 
<br/><INPUT type='radio' name='output_format' value='text'>Plain text 
 <input type='checkbox' name='plaintext_markup' value='1' checked>Use *_/ markup</input>
 <input type='checkbox' name='plaintext_prelink' value='1' checked>Put &rarr; before internal links</input>
<br/><INPUT type='radio' name='output_format' value='docbook_xml'>DocBook XML 
{$optional}
</tr></table>
</form>
<p>
Known issues:
<ul>
<li>In templates, {{{variables}}} used within &lt;nowiki&gt; tags will be replaced as well (too lazy to strip them)</li>
<li>HTML comments are removed (instead of converted into XML tags)</li>
</ul>
</p>
</body></html>" ;
}

#<input type='checkbox' name='resolvetemplates' value='1' checked>Automatically resolve templates</input><br/>

?>
