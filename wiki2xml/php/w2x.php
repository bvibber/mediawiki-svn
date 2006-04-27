<?php
# Copyright by Magnus Manske (2005)
# Released under GPL

if( !defined( 'MEDIAWIKI' ) ) { # Stand-alone
	include_once ( "default.php" ) ; # Which will include local.php, if available
}

require_once ( "mediawiki_converter.php" ) ;

@set_time_limit ( 0 ) ; # No time limit
ini_set('user_agent','MSIE 4\.0b2;'); # Fake user agent

## TIMER FUNCTION

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function get_form () {
	global $xmlg ;
	$optional = array () ;
	if ( isset ( $xmlg['docbook']['command_pdf'] ) ) {
		$optional[] = "<INPUT type='radio' name='output_format' value='docbook_pdf'>DocBook PDF" ;
	}
	if ( isset ( $xmlg['docbook']['command_html'] ) ) {
		$optional[] = "<INPUT type='radio' name='output_format' value='docbook_html'>DocBook HTML" ;
	}
	if ( isset ( $xmlg['zip_odt'] ) ) {
		$optional[] = "<INPUT type='radio' name='output_format' value='odt_xml'>OpenOffice XML" ;
		$optional[] = "<INPUT type='radio' name='output_format' value='odt'>OpenOffice ODT" ;
	}
	$optional = "<br/>" . implode ( "<br/>" , $optional ) ;
	


return "<form method='post'>
<h2>Paste article list or wikitext here</h2>
<table border='0' width='100%'><tr>
<td valign='top'><textarea rows='20' cols='80' style='width:100%' name='text'></textarea></td>
<td width='200px' valign='top' nowrap>
<INPUT checked type='radio' name='use_templates' value='all'>Use all templates<br/>
<INPUT type='radio' name='use_templates' value='none'>Do not use templates<br/>
<INPUT type='radio' name='use_templates' value='these'>Use these templates<br/>
<INPUT type='radio' name='use_templates' value='notthese'>Use all but these templates<br/>
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
<input type='checkbox' name='keep_categories' value='1' checked>Keep categories</input><br/>
<input type='checkbox' name='keep_interlanguage' value='1' checked>Keep interlanguage links</input><br/>
<input type='submit' name='doit' value='Convert'/>
</td><td valign='top' style='border-left:1px black solid'>
<b>Output</b>
<br/><INPUT checked type='radio' name='output_format' value='xml'>XML 
<br/><INPUT type='radio' name='output_format' value='text'>Plain text 
 <input type='checkbox' name='plaintext_markup' value='1' checked>Use *_/ markup</input>
 <input type='checkbox' name='plaintext_prelink' value='1' checked>Put &rarr; before internal links</input>
<br/><INPUT type='radio' name='output_format' value='xhtml'>XHTML 
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
</p>" ;
}

## MAIN PROGRAM

if ( isset ( $_POST['doit'] ) ) { # Process
	$wikitext = stripslashes ( $_POST['text'] ) ;
	
	$content_provider = new ContentProviderHTTP ;
	$converter = new MediaWikiConverter ;

	$xmlg["book_title"] = $_POST['document_title'] || 'document';
	$xmlg["site_base_url"] = $_POST['site'] ;
	$xmlg["resolvetemplates"] = $_POST['use_templates'] ;
	$xmlg['templates'] = explode ( "\n" , $_POST['templates'] ) ;
	$xmlg['add_gfdl'] = isset ( $_POST['add_gfdl'] ) ;
	$xmlg['keep_interlanguage'] = isset ( $_POST['keep_interlanguage'] ) ;
	$xmlg['keep_categories'] = isset ( $_POST['keep_categories'] ) ;
	
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
	} else if ( $format == "xhtml" ) {
		# Header hack for IE
		if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
		  header("Content-type: application/xhtml+xml");
		} else {
		  header("Content-type: text/html");
		}
#		header("Content-type: application/xhtml+xml");
		echo $converter->articles2xhtml ( $xml , $xmlg ) ;
	} else if ( $format == "odt" || $format == "odt_xml" ) {
		if ( $xmlg['sourcedir'] == '.' ) $cwd = getcwd() ;
		else $cwd = $xmlg['sourcedir'] ;
		$template_file = $cwd . '/template.odt' ;

		$dir_file = tempnam($xmlg["temp_dir"], "ODD");
		$dir = $dir_file . "-DIR" ;
		$xmlg['image_destination'] = $dir . "/Pictures" ;

		$zipdir = $cwd ;
		if ( isset ( $xmlg["zip_odt_path"] ) ) # Windows strange bug workaround
			$zipdir = $xmlg["zip_odt_path"] ;
		
		chdir ( $zipdir ) ;

		# Unzip template
		$cmd = $xmlg['unzip_odt'] ;
		$cmd = str_replace ( '$1' , escapeshellarg ( $template_file ) , $cmd ) ;
		$cmd = str_replace ( '$2' , escapeshellarg ( $dir ) , $cmd ) ;
		exec ( $cmd ) ;

		# Convert XML to ODT
		chdir ( $cwd ) ;
		if ( $format == "odt_xml" ) $content_provider->block_file_download = true ;
		$out = $converter->articles2odt ( $xml , $xmlg ) ;
		chdir ( $zipdir ) ;

		# Create ODT structure
		$handle = fopen ( $dir . "/content.xml" , "w" ) ;
		if ($handle) {
			fwrite ( $handle , $out ) ;
			fclose ( $handle ) ;
			# Generate temporary ODT file
			$out_file = tempnam($dir, "ODT");
			$cmd = $xmlg['zip_odt'] ;
			$cmd = str_replace ( '$1' , escapeshellarg ( $out_file ) , $cmd ) ;
			$cmd = str_replace ( '$2' , escapeshellarg ( $dir . "/" ) , $cmd ) ;
			@unlink ( $out_file ) ;
			exec ( $cmd ) ;
		
			if ( $format == "odt" ) { # Return ODT file
				$filename = $xmlg["book_title"] ;
				if (!preg_match('/\.[a-zA-Z]{3}$/',$filename)) { $filename .= '.odt'; }
				if (!preg_match('/\.[a-zA-Z]{3}$/',$out_file)) { $out_file .= '.zip'; }
				header('Content-type: application/vnd.oasis.opendocument.text; charset=utf-8');
				header('Content-Disposition: inline; filename="'.$filename.'"');
				# XXX TODO: error handling here
				$handle = fopen($out_file, 'rb');
				fpassthru ( $handle ) ;
				fclose ( $handle ) ;
			} else { # Return XML
				header('Content-type: text/xml; charset=utf-8');
				print str_replace ( ">" , ">\n" , $out ) ;
			}

			# Cleanup
			SureRemoveDir ( $dir ) ;
			@rmdir ( $dir ) ;
			@unlink ( $dir_file ) ;
			@unlink ( $out_file ) ;
			chdir ( $cwd ) ;
		}	# error occured

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
	exit ;
} else { # Show the form
	if( !defined( 'MEDIAWIKI' ) ) { # Stand-alone
		header('Content-type: text/html; charset=utf-8');
		print "
<html><head></head><body>
<h1>Magnus' magic MediaWiki-to-XML-to-stuff converter</h1>
<p>All written in PHP - so portable, <s>so incredibly slow...</s> <i>about as fast as the original MediaWiki parser!</i></p>" ;
		print get_form () ;
		print "</body></html>" ;
	} else { # MediaWiki extension
		$out = get_form () ;
	}
	
}

#<input type='checkbox' name='resolvetemplates' value='1' checked>Automatically resolve templates</input><br/>

?>
